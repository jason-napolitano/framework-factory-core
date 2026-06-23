<?php

namespace Tests\Services {

    use Tests\Contracts\LoggerInterface;

    class CacheWarmer
    {
        public function __construct(protected LoggerInterface $logger)
        {
            // ...
        }

        public function warm(): mixed
        {
            return $this->logger->log('Warming cache...');
        }
    }
}
