<?php

namespace Tests\App\Loggers {

    use Tests\App\Contracts\LoggerInterface;

    class FileLogger implements LoggerInterface
    {
        public function log(string $message): mixed
        {
            return "[FILE] {$message}";
        }
    }
}
