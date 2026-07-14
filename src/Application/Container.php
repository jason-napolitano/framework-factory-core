<?php

namespace FrameworkFactory\Application {

    use FrameworkFactory\Exceptions\Container\ContainerException;
    use FrameworkFactory\Contracts\Container\ContainerInstance;
	use FrameworkFactory\Contracts\Container\ContainerBinding;
	use FrameworkFactory\Contracts\Providers\ServiceProvider;
	use FrameworkFactory\Attributes\Providers\CreatesBinding;
	use FrameworkFactory\Contracts\Container\ContextBuilder;
    use FrameworkFactory\Application\Context\Builder;

    /**
     * The container is built to house all dependencies that an
     * application is going to use.
     */
    class Container implements ContainerInstance
    {
        /** @var array<ContainerBinding> $bindings container bindings */
        private array $bindings = [];

        /** @var array<ContainerBinding> $singletons singleton instances */
        private array $singletons = [];

        /** @var array<string> $aliases binding aliases */
        private array $aliases = [];

        /** @var array<string, string> $providers service providers */
        private array $providers = [];

        /** @var bool $booted has a provider been booted? */
        private bool $booted = false;

        /** @var array $deferred deferred providers */
        private array $deferred = [];

        /** @var array $loadedProviders loaded providers */
        private array $loadedProviders = [];

        /** @var array $afterResolving hooks for after a provider is loaded */
        private array $afterResolving  = [];

        /** @var array $beforeResolving hooks for before a provider is loaded */
        private array $beforeResolving = [];

        /** @var array $contextual lookup table of context overrides */
        private array $contextual = [];

        /** @var array $buildStack current context build stack */
        private array $buildStack = [];

        /** @var string $cacheFile the cached bootstrap file */
        private string $cacheFile;

        /**
         * Builds the container instance
         *
         * @param string $cachePath Path to the cache directory
         */
        public function __construct(string $cachePath)
        {
            $this->cacheFile = rtrim($cachePath, '/') . '/app.php';
        }

        /**
         * @inheritdoc
         */
        public function bind(string $id, callable $factory): void
        {
            $this->bindings[$id] = $this->bindingInstance($factory);
        }

        /**
         * @inheritdoc
         */
        public function singleton(string $id, callable $factory): void
        {
            $this->singletons[$id] = $this->bindingInstance($factory, true);
        }

        /**
         * @inheritdoc
         */
        public function when(string $concrete): ContextBuilder
        {
            return new Builder($this, $concrete);
        }

        /**
         * @inheritdoc
         */
        public function addContextualBinding(string $concrete, string $abstract, callable|string $implementation): void
        {
            $this->contextual[$concrete][$abstract] = $implementation;
        }

        /**
         * @inheritdoc
         */
        public function beforeResolving(string $id, callable $callback): void
        {
            $this->beforeResolving[$id][] = $callback;
        }

        /**
         * @inheritdoc
         */
        public function afterResolving(string $id, callable $callback): void
        {
            $this->afterResolving[$id][] = $callback;
        }

        /**
         * @inheritdoc
         */
        public function alias(string $alias, string $id): void
        {
            $this->aliases[$alias] = $id;
        }

        /**
         * @inheritdoc
         */
        public function get(string $id): mixed
        {
            $id = $this->aliases[$id] ?? $id;

            if (isset($this->singletons[$id])) {
                return $this->singletons[$id];
            }

            $this->buildStack[] = $id;

            try {
                foreach ($this->beforeResolving[$id] ?? [] as $callback) {
                    $callback($this, $id);
                }

                if (! isset($this->bindings[$id])) {
                    $this->loadDeferredProvider($id);
                }

                $binding = $this->bindings[$id];

                $object = $this->resolveWithContext($id);

                if ($binding['shared']) {
                    $this->singletons[$id] = $object;
                }

                foreach ($this->afterResolving[$id] ?? [] as $callback) {
                    $callback($this, $object);
                }

                return $object;
            } finally {
                array_pop($this->buildStack);
            }
        }

        /**
         * @inheritdoc
         */
        public function has(string $id): bool
        {
            return isset($this->bindings[$id]) || isset($this->aliases[$id]);
        }

        /**
         * @inheritdoc
         */
        public function registerProvider(string $provider): void
        {
            // check to see if the $provider extends the base service provider class
            if (!is_subclass_of($provider, ServiceProvider::class)) {
                throw new ContainerException(sprintf('Service Providers must extend %s', ServiceProvider::class));
            }

            // forego the registration if the provider is already loaded
            if (isset($this->loadedProviders[$provider])) {
                return;
            }

            // assemble the provider arrays
            $this->loadedProviders[$provider] = true;
            $this->providers[] = $provider;

            // if a service provider uses the CreatesBinding attribute
            if (Getters\Attribute::has($provider, CreatesBinding::class)) {
                // bind a new service to the container using its attached metadata
                $attribute = Getters\Attribute::get($provider, CreatesBinding::class);
                $this->bind($attribute->id, fn () => new $attribute->concrete());
            }

            // create a new provider instance and run its register() method
            new $provider($this)->register();
        }

        /**
         * Run the providers' boot methods
         *
         * @return void
         */
        public function bootProviders(): void
        {
            if ($this->booted) {
                return;
            }

            foreach ($this->providers as $provider) {
                new $provider($this)->boot();
            }

            $this->booted = true;
        }

        /**
         * @inheritdoc
         */
        public function bootstrap(array $providers): void
        {
            if ($this->cacheFileExists()) {
                $this->loadCache();
                return;
            }

            foreach ($providers as $provider) {
                $this->registerProvider($provider);
            }
        }

        /**
         * @inheritdoc
         */
        public function providers(): array
        {
            return [...$this->providers, ...$this->deferred];
        }

        /**
         * Loads a deferred service provider
         *
         * @param string $service
         *
         * @return void
         */
        private function loadDeferredProvider(string $service): void
        {
            if (!isset($this->deferred[$service])) {
                return;
            }

            $provider = $this->deferred[$service];
            unset($this->deferred[$service]);

            $this->registerProvider($provider);
        }

        /**
         * Dependency context resolution
         *
         * @param string $id
         *
         * @return mixed
         */
        private function resolveWithContext(string $id): mixed
        {
            if (count($this->buildStack) > 1) {
                $parent = $this->buildStack[count($this->buildStack) - 2];

                if (isset($this->contextual[$parent][$id])) {
                    $concrete = $this->contextual[$parent][$id];

                    return is_callable($concrete)
                        ? $concrete($this)
                        : new $concrete();
                }
            }

            return ($this->bindings[$id]['factory'])($this);
        }

        /**
         * Returns a binding instance as an array
         *
         * @param callable $factory
         * @param bool     $shared
         *
         * @return array
         */
        private function bindingInstance(callable $factory, bool $shared = false): array
        {
            return (array) new ContainerBinding($factory, $shared);
        }

        /**
         * Does the cache file exist?
         *
         * @return bool
         */
        private function cacheFileExists(): bool
        {
            return file_exists($this->cacheFile);
        }

        /**
         * Loads the cached file
         *
         * @return void
         */
        private function loadCache(): void
        {
            $data = require $this->cacheFile;

            $this->deferred = $data['deferred'] ?? [];

            foreach ($data['aliases'] as $alias => $id) {
                $this->alias($alias, $id);
            }

            foreach ($data['providers'] as $provider) {
                $this->registerProvider($provider);
            }
        }
    }
}
