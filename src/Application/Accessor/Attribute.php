<?php

namespace FrameworkFactory\Application\Accessor {

	class Attribute
	{
		/** @var array $cache the cached array of attributes */
		protected static array $cache = [];

		/**
		 * Returns a cached instance of an attribute passed in at
		 * class level
		 *
		 * @param string $class
		 * @param string $attribute
		 *
		 * @return object
		 *
		 * @throws \ReflectionException
		 */
		public static function get(string $class, string $attribute): object
		{
			// designate the cache key
			$key = "{$class}:{$attribute}";

			// if the cache value is not already set
			if (!isset(self::$cache[$key])) {
				// create a new reflection instance
				$reflection = new \ReflectionClass($class);

				// get the classes attributes
				$attrs = $reflection->getAttributes($attribute);

				// assign the cache value
				self::$cache[$key] = $attrs[0]->newInstance();
			}
			// return the cached value
			return self::$cache[$key];
		}
	}
}