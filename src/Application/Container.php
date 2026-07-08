<?php

namespace FrameworkFactory\Application {

	use FrameworkFactory\Contracts\Container\ContainerInstance;
	use FrameworkFactory\Exceptions\Container\ServiceNotFound;
	use FrameworkFactory\Attributes\Providers\CreatesBinding;
	use FrameworkFactory\Contracts\Container\ContextBuilder;
	use FrameworkFactory\Application\Context\Builder;
	use FrameworkFactory\Application\Getters;

	/**
     * The container is built to house all dependencies that an
     * application is going to use.
     */
    class Container implements ContainerInstance
    {
        /** @var array $bindings container bindings */
        private array $bindings = [];

        /** @var array $singletons singleton instances */
        private array $singletons = [];

        /** @var array $aliases binding aliases */
        private array $aliases = [];

        /** @var array $providers service providers */
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
            $this->bindings[$id] = $factory;
        }

        /**
         * @inheritdoc
         */
        public function singleton(string $id, callable $factory): void
        {
            $this->bindings[$id] = function (ContainerInstance $c) use ($id, $factory) {
                return $this->instances[$id] ??= $factory($c);
            };
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

            if (isset($this->instances[$id])) {
                return $this->instances[$id];
            }

            $this->buildStack[] = $id;

            try {
                foreach ($this->beforeResolving[$id] ?? [] as $cb) {
                    $cb($this, $id);
                }

                if (! isset($this->bindings[$id])) {
                    $this->loadDeferredProvider($id);
                }

                if (! isset($this->bindings[$id])) {
                    throw new ServiceNotFound("The [$id] service has not been bound to the container.");
                }

                $object = $this->resolveWithContext($id);

                foreach ($this->afterResolving[$id] ?? [] as $cb) {
                    $cb($this, $object);
                }

                return $this->singletons[$id] = $object;
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
			// forego the registration if the provider is already loaded
            if (isset($this->loadedProviders[$provider])) {
                return;
            }

			// assemble the arrays
            $this->loadedProviders[$provider] = true;
            $this->providers[] = $provider;

	        // run a check to verify whether the service provider calls CreatesBinding
	        if (Getters\Attribute::has($provider, CreatesBinding::class)) {
		        $attribute = Getters\Attribute::get($provider, CreatesBinding::class);
		        $this->bind($attribute->id, fn () => new $attribute->concrete());
	        }

			// otherwise, run the providers register() method
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
            if (! isset($this->deferred[$service])) {
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
            // If resolving as a dependency of something else
            if (count($this->buildStack) > 1) {
                $parent = $this->buildStack[count($this->buildStack) - 2];

                if (isset($this->contextual[$parent][$id])) {
                    $concrete = $this->contextual[$parent][$id];

                    return is_callable($concrete) ? $concrete($this) : new $concrete();
                }
            }

            // Default binding
            return ($this->bindings[$id])($this);
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
