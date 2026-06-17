<?php

namespace FrameworkFactory\Application {

    use FrameworkFactory\Support\Attributes\Accessors\ResolvesFor;
    use FrameworkFactory\Contracts\Container\ContainerInstance;
    use FrameworkFactory\Support\Cache\Attribute as Cache;

    /**
     * The Accessor class acts a facade system. It grants
     * access to services that are bound to the container
     */
    abstract class Accessor
    {
        /** @var ContainerInstance $container the container instance */
        private static ContainerInstance $container;

        /** @var string|null $key the key used to resolve the container binding */
        protected static ?string $key = null;

        /** @var array $attributeCache cached attributes */
        protected static array $attributeCache = [];

        /**
         * Set the container which will be used for
         * binding resolution
         *
         * @param ContainerInstance $container
         *
         * @return void
         */
        public static function setContainer(ContainerInstance $container): void
        {
            static::$container = $container;
        }

        /**
         * Container bindings resolver
         *
         * @return string
         */
        private static function resolver(): string
        {
            // if $key is assigned, let's use the value of $key
            if (static::$key) {
                return static::$key;
            }

            // otherwise, let's use the $id value of ResolvesFor()
            /** @var ResolvesFor $attribute */
            $attribute = Cache::get(static::class, ResolvesFor::class);
            return $attribute->id;
        }

        /**
         * Returns the container binding instance
         *
         * @return mixed
         */
        private static function instance(): mixed
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
