<?php

namespace FrameworkFactory\Contracts\Application {

    use FrameworkFactory\Exceptions;

    /**
     * This represents the application's main entrypoint instance
     */
    interface ApplicationInstance
    {
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
    }
}
