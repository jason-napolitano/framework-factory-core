<?php

namespace FrameworkFactory\Support\Cache {

    class Attribute
    {
        protected static array $cache = [];

        public static function get(string $class, string $attribute): ?object
        {
            $key = "{$class}:{$attribute}";

            if (!isset(self::$cache[$key])) {
                $reflection = new \ReflectionClass($class);

                $attrs = $reflection->getAttributes($attribute);

                self::$cache[$key] = $attrs[0]->newInstance();
            }

            return self::$cache[$key];
        }
    }
}
