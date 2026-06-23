<?php

namespace FrameworkFactory\Contracts\Application {

    use FrameworkFactory\Exceptions;
    use FrameworkFactory\Contracts;

    /**
     * This represents the application's main entrypoint instance
     */
    interface ApplicationInstance
    {
        /**
         * The initial bootstrap process which builds
         * the container, generates a built-in
         * autoloader and returns the app instance
         *
         * @param string $basePath Path the base directory
         *
         * @return self
         */
        public static function build(string $basePath): ApplicationInstance;

        /**
         * Registers multiple service providers
         *
         * @param array $providers
         *
         * @return void
         *
         * @throws Exceptions\Container\EmptyProvidersValue
         */
        public function withProviders(array $providers): void;

        /**
         * Fires up the application to finalize the bootstrap
         * process
         *
         * @return void
         */
        public function fire(): void;

        /**
         * A collection of service providers
         *
         * @return array<string>
         */
        public static function providers(): array;

        /**
         * Returns a service that has been loaded into
         * the container
         *
         * @return mixed
         */
        public static function get(string $id): mixed;

        /**
         * Returns the container instance
         *
         * @return Contracts\Container\ContainerInstance
         */
        public static function container(): Contracts\Container\ContainerInstance;

        /**
         * Returns an autoloader instance
         *
         * @return AutoloaderInstance
         */
        public static function autoloader(): AutoloaderInstance;
    }
}
