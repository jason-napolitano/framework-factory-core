<?php

namespace FrameworkFactory\Application\Traits {

    /**
     * Allows for application configuration to be dynamically
     * assigned, and retrieved for each application instance.
     */
    trait HasOptions
    {
        /** @var array|string[] $options configurable options */
        private static array $options = [];

        /**
         * Return the config option from the options array,
         * or null otherwise
         *
         * @param string $name
         * @param array  $arguments
         *
         * @return string
         */
        public static function __callStatic(string $name, array $arguments)
        {
            return self::$options[self::camelize($name)] ?? null;
        }

        /**
         * Set the value of an option and return an instance
         * of the application for method chaining
         *
         * @param string $method
         * @param array  $arguments
         *
         * @return $this
         */
        public function __call(string $method, array $arguments)
        {
            if (str_starts_with($method, 'set')) {
                $option = self::camelize(substr($method, 3));

                self::$options[$option] = $arguments[0] ?? null;

                return $this;
            }

            throw new \BadMethodCallException(
                sprintf('%s::%s() does not exist.', static::class, $method)
            );
        }

        /**
         * Returns a camel-case string
         *
         * @param string $string
         *
         * @return string
         */
        private static function camelize(string $string): string
        {
            return strtolower(
                preg_replace('/(?<!^)[A-Z]/', '_$0', $string)
            );
        }
    }
}
