<?php

namespace App\Loggers {

    use App\Contracts\LoggerInterface;

    class NullLogger implements LoggerInterface
    {
        public function log(string $message): mixed
        {
            return "[NULL] {$message}";
        }
    }
}
