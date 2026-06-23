<?php

namespace Tests\Services {

    use Tests\Contracts\LoggerInterface;

    class ReportService
    {
        public function __construct(protected LoggerInterface $logger)
        {
            // ...
        }

        public function generate(): mixed
        {
            return $this->logger->log('Generating report...');
        }
    }
}
