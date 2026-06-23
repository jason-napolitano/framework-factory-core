<?php

namespace FrameworkFactory\Contracts\Application {

    interface AutoloaderInstance
    {
        /**
         * Register the autoloader with SPL
         *
         * @return void
         */
        public function register(): void;

        /**
         * Register a PSR-4 namespace
         *
         * @param string $prefix
         * @param string $baseDir
         * @param bool   $prepend
         *
         * @return $this
         */
        public function addNamespace(string $prefix, string $baseDir, bool $prepend = false): self;

        /**
         * Get all registered prefixes
         *
         * @return array
         */
        public function prefixes(): array;

        /**
         * Get all classes known to the autoloader
         *
         * @return array
         */
        /**
         * Returns classes loaded into the autoloader
         *
         * @param string|null $subNamespace
         *
         * @return array
         */
        public function getClasses(?string $subNamespace = null): array;

        /**
         * Get total class count
         *
         * @return int
         */
        public function getClassCount(): int;

        /**
         * Determine whether a class exists in the map
         *
         * @param string $class
         *
         * @return bool
         */
        public function hasClass(string $class): bool;

        /**
         * Get file path for a class
         *
         * @param string $class
         *
         * @return string|null
         */
        public function getClassFile(string $class): ?string;

        /**
         * Manually add a class map entry
         *
         * @param string $class
         * @param string $file
         *
         * @return $this
         */
        public function addClassMap(string $class, string $file): self;
    }
}
