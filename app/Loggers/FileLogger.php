<?php

namespace App\Loggers {

    use App\Contracts\LoggerInterface;

    class FileLogger implements LoggerInterface
    {
        public function log(string $message): mixed
        {
            return "[FILE] {$message}";
        }
    }
}
