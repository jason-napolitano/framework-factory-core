<?php

namespace FrameworkFactory\Contracts\Providers {

    use FrameworkFactory\Contracts\Container;

    /**
     * This class is to be extended by all service providers
     */
    abstract class ServiceProvider
    {
        /**
         * Builds a new service provider
         *
         * @param Container\ContainerInstance $container container instance
         */
        public function __construct(private readonly Container\ContainerInstance $container)
        {
            // ...
        }

        /**
         * Registers new container bindings
         *
         * @return void
         */
        public function register(): void
        {
            // ...
        }

        /**
         * Deferred binding ID(s)
         *
         * @return array
         */
        public function provides(): array
        {
            return [];
        }

        /**
         * Shorthand function to reach the container's beforeResolving hook
         *
         * @param string $id
         * @param        $concrete
         *
         * @return void
         */
        public function beforeResolving(string $id, $concrete): void
        {
            $this->container->beforeResolving($id, $concrete);
        }

        /**
         * Shorthand function to reach the container's beforeResolving hook
         *
         * @param string $id
         * @param        $concrete
         *
         * @return void
         */
        public function afterResolving(string $id, $concrete): void
        {
            $this->container->afterResolving($id, $concrete);
        }

        /**
         * Shorthand function for binding a service to the container
         *
         * @param string $id
         * @param        $concrete
         *
         * @return void
         */
        public function bind(string $id, $concrete): void
        {
            $this->container->bind($id, $concrete);
        }

        /**
         * Shorthand function for binding a singleton instance to the
         * container
         *
         * @param string $id
         * @param        $concrete
         *
         * @return void
         */
        public function singleton(string $id, $concrete): void
        {
            $this->container->singleton($id, $concrete);
        }

        /**
         * A shorthand function for utilizing the Context API
         *
         * @param string $concrete
         *
         * @return Container\ContextBuilder
         */
        public function when(string $concrete): Container\ContextBuilder
        {
            return $this->container->when($concrete);
        }

        /**
         * Boots after all providers are registered
         *
         * @return void
         */
        public function boot(): void
        {
            // ...
        }
    }
}
