<?php

namespace FrameworkFactory\Application {

    use FrameworkFactory\Exceptions\FileSystem\DirectoryNotCreated;
    use FrameworkFactory\Contracts\Container\ContainerInstance;
    use FrameworkFactory\Contracts\Providers\ServiceProvider;
    use FrameworkFactory\Application\Bootstrap\Formatter;

    /**
     * The bootstrap file is responsible for building a cache file and
     * loading any service providers into the cache file, along with their
     * aliases.
     */
    class Bootstrap
    {
        /**
         * Bootstraps the application by creating a cache file
         *
         * @param ContainerInstance      $container
         * @param array<ServiceProvider> $providers
         * @param string                 $cachePath
         *
         * @return void
         */
        public static function build(ContainerInstance $container, array $providers, string $cachePath): void
        {
            // empty data arrays
            $eager = [];
            $deferred = [];
            $aliases = [];

            // run through the providers for their data to
            // add to the cache file
            foreach ($providers as $provider) {
                $instance = new $provider($container);
                $services = $instance->provides();

                if ($services) {
                    foreach ($services as $service) {
                        $deferred[$service] = $provider;
                        $aliases[$service] = $service;
                    }
                } else {
                    $eager[] = $provider;
                }
            }

            // the data for the cache file
            $cache = [
                'providers' => array_unique($eager),
                'deferred'  => $deferred,
                'aliases'   => $aliases,
            ];

            // create the cache directory
            self::createCacheDirectory($cachePath);

            // update the cache file
            file_put_contents(rtrim($cachePath, '/') . '/app.php', self::export($cache));
        }

        /**
         * Creates a new cache directory if none exists, ignores the
         * directory creation if one does
         *
         * @param string $path the path of the directory to create
         *
         * @return void
         */
        private static function createCacheDirectory(string $path): void
        {
            // if it's already a directory, do nothing
            if (is_dir($path)) {
                return;
            }

            // otherwise, attempt to create it
            if (!mkdir($path, 0775, true) && !is_dir($path)) {
                throw new DirectoryNotCreated(sprintf('Directory "%s" was not created', $path));
            }
        }

        /**
         * Exports the contents of the cache file
         *
         * @param array $data
         *
         * @return string
         */
        private static function export(array $data): string
        {
            return "<?php\n\nreturn " . Formatter::make()->indentWithTabs()->export($data) . ";\n";
        }
    }
}
