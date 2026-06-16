<?php

namespace FrameworkFactory\Application {

    use FrameworkFactory\Contracts;
    use FrameworkFactory\Support;

    /**
     * The Accessor class acts a facade system. It grants
     * access to services that are bound to the container
     */
    abstract class Accessor
    {
        /** @var Contracts\Container\ContainerInstance $container the container instance */
        private static Contracts\Container\ContainerInstance $container;

        /** @var string|null $key the key used to resolve the container binding */
        protected static ?string $key = null;

        /**
         * Set the container which will be used for
         * binding resolution
         *
         * @param Contracts\Container\ContainerInstance $container
         *
         * @return void
         */
        public static function setContainer(Contracts\Container\ContainerInstance $container): void
        {
            static::$container = $container;
        }

        /**
         * Container bindings resolver
         *
         * @return string
         */
        protected static function resolver(): string
        {
            if (static::$key) {
                return static::$key;
            }

            $reflection = new \ReflectionClass(static::class);
            $attributes = $reflection->getAttributes(Support\Attributes\Accessors\ResolvesFor::class);

            /** @var Support\Attributes\Accessors\ResolvesFor $attribute */
            $attribute = $attributes[0]->newInstance();
            return $attribute->accessor;
        }

        /**
         * Returns the container binding instance
         *
         * @return mixed
         */
        protected static function instance(): mixed
        {
            return static::$container->get(static::resolver());
        }

        /**
         * Forward the static calls to the bound instance
         *
         * @param string $method
         * @param array  $arguments
         *
         * @return mixed
         */
        public static function __callStatic(string $method, array $arguments)
        {
            return static::instance()->{$method}(...$arguments);
        }
    }
}
