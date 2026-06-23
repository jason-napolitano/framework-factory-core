<?php

namespace App\Services {

    use App\Contracts\LoggerInterface;

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
