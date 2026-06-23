<?php

namespace App\Modules\Providers {

    use FrameworkFactory\Contracts\Container\ContainerInstance;
    use FrameworkFactory\Contracts\Providers\ServiceProvider;
    use App\Contracts\LoggerInterface;
    use App\Services\ReportService;
    use App\Services\CacheWarmer;
    use App\Loggers\FileLogger;
    use App\Loggers\NullLogger;

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
