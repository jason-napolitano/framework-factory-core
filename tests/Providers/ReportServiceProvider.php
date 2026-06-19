<?php

namespace Tests\Providers {

    use FrameworkFactory\Contracts\Container\ContainerInstance;
    use FrameworkFactory\Contracts\Providers\ServiceProvider;
    use Tests\Contracts\LoggerInterface;
    use Tests\Services\ReportService;
    use Tests\Services\CacheWarmer;
    use Tests\Loggers\NullLogger;
    use Tests\Loggers\FileLogger;

    class ReportServiceProvider extends ServiceProvider
    {
        public function register(): void
        {
            // container bindings
            $this->bind(LoggerInterface::class, fn () => new NullLogger());

            $this->bind(
                ReportService::class,
                fn (ContainerInstance $c) => new ReportService(
                    $c->get(LoggerInterface::class)
                )
            );

            $this->bind(
                CacheWarmer::class,
                fn (ContainerInstance $c) => new CacheWarmer(
                    $c->get(LoggerInterface::class)
                )
            );

            // context-api
            $this->when(ReportService::class)
                ->needs(LoggerInterface::class)
                ->give(FileLogger::class);

            $this->when(CacheWarmer::class)
                ->needs(LoggerInterface::class)
                ->give(NullLogger::class);

        }
    }
}
