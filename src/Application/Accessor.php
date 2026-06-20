<?php

namespace FrameworkFactory\Application {

	use FrameworkFactory\Exceptions\Container\ContainerException;
	use FrameworkFactory\Contracts\Container\ContainerInstance;
	use FrameworkFactory\Attributes\Accessors\ResolvesFor;
	use FrameworkFactory\Application\Getters\Attribute;

	/**
     * The Accessor class acts a facade system. It grants
     * access to services that are bound to the container
     */
    abstract class Accessor
    {
        /** @var ContainerInstance $container the container instance */
        private static ContainerInstance $container;

        /** @var string $key the key used to resolve the container binding */
        protected static string $key = '';

        /**
         * Set the container instance used for binding resolution
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
	     * Forward the static calls to the bound instance
	     *
	     * @param string $method
	     * @param array  $arguments
	     *
	     * @return mixed
	     */
	    public static function __callStatic(string $method, array $arguments): mixed
	    {
		    return static::instance()->{$method}(...$arguments);
	    }

        /**
         * Container bindings resolver
         *
         * @return string
         */
        private static function resolver(): string
        {
			try {
				// if $key is an empty string (the default value), let's assign it
				// the value of the $id property from the ResolvesFor() attribute
				if (static::$key === '') {
					static::$key = Attribute::get(static::class, ResolvesFor::class)->id;
				}
				return static::$key;
			} catch (\Throwable) {
				throw new ContainerException('The container could not resolve the dependency.');
			}
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
    }
}
