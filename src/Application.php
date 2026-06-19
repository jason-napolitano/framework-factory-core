<?php

namespace FrameworkFactory {

    use FrameworkFactory\Contracts\Container\ContainerInstance;
    use FrameworkFactory\Application\Traits\HasOptions;
    use FrameworkFactory\Application as App;

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

        /**
         * @inheritdoc
         */
        public static function build(string $basePath): self
        {
            // assign the base and cache paths
            self::$basePath = rtrim($basePath, '/') . DIRECTORY_SEPARATOR;
            self::setCachePath(self::$basePath);

            // build a new container instance
            self::$container = new App\Container(self::$cachePath);

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
            App\Bootstrap::build(self::$container, self::$providers, self::$cachePath);

            // bootstrap the service providers and run their boot methods
            self::$container->bootstrap(self::$providers);
            self::$container->bootProviders();
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
