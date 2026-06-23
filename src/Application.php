<?php

namespace FrameworkFactory {

    use FrameworkFactory\Application as App;
    use FrameworkFactory\Application\Traits\HasOptions;
    use FrameworkFactory\Contracts\Application\ApplicationInstance;
    use FrameworkFactory\Contracts\Application\AutoloaderInstance;
    use FrameworkFactory\Contracts\Container\ContainerInstance;

    /**
     * This is the application entry point used to build and
     * bootstrap an application. It sets up the container and
     * configures the core libraries.
     *
     * USAGE:
     * ```
     * $app = Application::build(...);
     *
     * // app configuration ...
     *
     * $app->fire()
     * ```
     */
    final class Application implements Contracts\Application\ApplicationInstance
    {
        use HasOptions;

        /** @var Contracts\Container\ContainerInstance $container service container */
        private static Contracts\Container\ContainerInstance $container;

        /** @var array $providers base service providers */
        private static array $providers = [];

        /** @var string $basePath the base path for the application */
        private static string $basePath;

        /** @var string $cachePath the path for the cached bootstrap file */
        private static string $cachePath;

        /** @var AutoloaderInstance $autoloader autoloader instance */
        private static AutoloaderInstance $autoloader;

        /** @var string $appNamespace application namespace */
        private static string $appNamespace;

        /**
         * @inheritdoc
         */
        public static function build(string $basePath, string $appNamespace = 'App', string $appDirectory = 'app'): ApplicationInstance
        {
            // assign the base and cache paths
            self::$basePath = rtrim($basePath, '/') . DIRECTORY_SEPARATOR;
            self::setCachePath(self::$basePath);

            // build a new container instance
            self::$container = new App\Container(self::$cachePath);

            // registers a new autoloader instance
            self::registerAutoloader($appNamespace, self::$basePath . $appDirectory);

            // auto-discover service providers
            self::autoDiscoverProviders(self::autoloader());

            // configure the facade / accessor system
            App\Accessor::setContainer(self::$container);

            // return a new application instance
            return new self();
        }

        /**
         * @inheritdoc
         */
        public function fire(): void
        {
            // run the bootstrap build process
            App\Bootstrap::build(self::$container, self::filteredProviders(self::$providers), self::$cachePath);

            // bootstrap the service providers and run their boot methods
            self::$container->bootstrap(self::$providers);
            self::$container->bootProviders();
        }

        /**
         * Auto-discover providers and add them to the providers array
         *
         * @param AutoloaderInstance $autoloader autoloader instance
         *
         * @return void
         */
        private static function autoDiscoverProviders(AutoloaderInstance $autoloader): void
        {
            // add each autoloaded provider to the $providers array
            foreach ($autoloader->getClasses(self::$appNamespace . '\Providers') as $class) {
                self::$providers[] = $class;
            }
        }

        /**
         * Returns an array of filtered providers
         *
         * @param array $items
         *
         * @return array
         */
        private static function filteredProviders(array $items): array
        {
            return array_filter($items, self::isProviderClass(...)) |> array_values(...);
        }

        /**
         * Check to see if the class provided is an approved service
         * provider class
         *
         * @param string $class
         *
         * @return bool
         */
        private static function isProviderClass(string $class): bool
        {
            return str_ends_with($class, 'Provider') || str_ends_with($class, 'ServiceProvider');
        }

        /**
         * @inheritdoc
         */
        public function withProviders(array $providers): void
        {
            // if the $providers is an empty array, throw an exception
            if (empty($providers)) {
                throw new Exceptions\Container\EmptyProvidersValue('The providers array cannot be empty');
            }

            // assign the providers
            self::$providers = [...self::$providers, ...$providers];
        }

        /**
         * @inheritdoc
         */
        public static function providers(): array
        {
            return self::$container->providers();
        }

        /**
         * @inheritdoc
         */
        public static function get(string $id): mixed
        {
            return self::$container->get($id);
        }

        /**
         * @inheritdoc
         */
        public static function container(): ContainerInstance
        {
            return self::$container;
        }

        /**
         * @inheritdoc
         */
        public static function autoloader(): AutoloaderInstance
        {
            return self::$autoloader;
        }

        /**
         * Registers a new autoloader instance
         *
         * @param string $namespace
         * @param string $path
         *
         * @return void
         */
        private static function registerAutoloader(string $namespace, string $path): void
        {
            self::$appNamespace = ucfirst($namespace);
            self::$autoloader = new App\Bootstrap\Autoloader();
            self::$autoloader->register();
            self::$autoloader->addNamespace($namespace, $path);
        }

        /**
         * Builds the cache path location
         *
         * @param string $basePath
         *
         * @return void
         */
        private static function setCachePath(string $basePath): void
        {
            self::$cachePath = $basePath . 'cache';
        }
    }
}
