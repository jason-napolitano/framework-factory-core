<?php

namespace FrameworkFactory\Application\Bootstrap {

    use FrameworkFactory\Contracts\Application\AutoloaderInstance;

    class Autoloader implements AutoloaderInstance
    {
        /** @var array<string, array<int, string>> $prefixes registered psr-4 namespace prefixes */
        protected array $prefixes = [];

        /** @var array<string, string> $classmap fully-qualified class => file path */
        protected array $classmap = [];

        /** @var array<string, string> $loadedFiles files loaded by this autoloader */
        protected array $loadedFiles = [];

        /** @var array<string, string> $availableFiles discovered PHP files */
        protected array $availableFiles = [];

        protected array $namespaceIndex = [];

        /**
         * @inheritdoc
         */
        public function register(): void
        {
            spl_autoload_register([$this, 'loadClass']);
        }

        /**
         * @inheritdoc
         */
        public function addNamespace(string $prefix, string $baseDir, bool $prepend = false): self
        {
            $prefix = trim($prefix, '\\') . '\\';
            $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            if (!isset($this->prefixes[$prefix])) {
                $this->prefixes[$prefix] = [];
            }

            if ($prepend) {
                array_unshift($this->prefixes[$prefix], $baseDir);
            } else {
                $this->prefixes[$prefix][] = $baseDir;
            }

            $this->indexNamespaceDirectory($prefix, $baseDir);

            return $this;
        }

        /**
         * @inheritdoc
         */
        public function prefixes(): array
        {
            return $this->prefixes;
        }

        /**
         * @inheritdoc
         */
        public function getClasses(?string $subNamespace = null): array
        {
            if ($subNamespace === null) {
                return array_keys($this->classmap);
            }

            $subNamespace = trim($subNamespace, '\\');

            return $this->namespaceIndex[$subNamespace] ?? [];
        }

	    /**
	     * Returns any sub-namespaces within the classmap
	     *
	     * @param string $namespace
	     *
	     * @return array
	     */
	    public function getSubNamespaces(string $namespace): array
	    {
		    $namespace = trim($namespace, '\\');

		    $children = [];

		    foreach (array_keys($this->classmap) as $class) {
			    if (!str_starts_with($class, $namespace . '\\')) {
				    continue;
			    }

			    $remaining = substr($class, strlen($namespace) + 1);

			    if ($remaining === false) {
				    continue;
			    }

			    $parts = explode('\\', $remaining);

			    // If there is no namespace below the requested namespace,
			    // then the class is directly inside it.
			    if (count($parts) <= 1) {
				    continue;
			    }

			    $children[$parts[0]] = true;
		    }

		    $namespaces = array_keys($children);

		    sort($namespaces);

		    return $namespaces;
	    }

        /**
         * @inheritdoc
         */
        public function getClassCount(): int
        {
            return count($this->classmap);
        }

        /**
         * @inheritdoc
         */
        public function hasClass(string $class): bool
        {
            return isset($this->classmap[$class]);
        }

        /**
         * @inheritdoc
         */
        public function getClassFile(string $class): ?string
        {
            return $this->classmap[$class] ?? null;
        }

        /**
         * @inheritdoc
         */
        public function addClassMap(string $class, string $file): self
        {
            $this->classmap[$class] = $file;

            if (file_exists($file)) {
                $realPath = realpath($file) ?: $file;
                $this->availableFiles[$realPath] = $realPath;
            }

            return $this;
        }

        /**
         * Load a class
         *
         * @param string $class
         *
         * @return bool
         */
        private function loadClass(string $class): bool
        {
            if (!isset($this->classmap[$class])) {
                return false;
            }

            return $this->requireFile($this->classmap[$class]);
        }

        /**
         * Require a file if it exists
         *
         * @param string $file
         *
         * @return bool
         */
        private function requireFile(string $file): bool
        {
            if (!file_exists($file)) {
                return false;
            }

            require $file;

            $realPath = realpath($file) ?: $file;

            $this->loadedFiles[$realPath] = $realPath;

            return true;
        }

        /**
         * Build a class map and file index from a namespace directory
         *
         * @param string $prefix
         * @param string $baseDir
         *
         * @return void
         */
        private function indexNamespaceDirectory(string $prefix, string $baseDir): void
        {
            if (!is_dir($baseDir)) {
                return;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (
                    !$file->isFile() ||
                    strtolower($file->getExtension()) !== 'php'
                ) {
                    continue;
                }

                $path = $file->getRealPath();

                if (!$path) {
                    continue;
                }

                $this->availableFiles[$path] = $path;

                $relativePath = substr($path, strlen(realpath($baseDir)) + 1)
                        |> (static fn ($x) => str_replace(['/', '\\'], '\\', $x))
                        |> (static fn ($x) => preg_replace('/\.php$/i', '', $x));

                $class = $prefix . $relativePath;
                $this->classmap[$class] = $path;

                $parts = explode('\\', $class);

                // build namespace tree index
                $current = '';
                foreach ($parts as $i => $part) {
                    if ($i === count($parts) - 1) {
                        break;
                    }

                    $current .= ($current ? '\\' : '') . $part;
                    $this->namespaceIndex[$current][] = $class;
                }
            }
        }
    }
}
