<?php

namespace Tests\App\Loggers {

    use Tests\App\Contracts\LoggerInterface;

    class NullLogger implements LoggerInterface
    {
        public function log(string $message): mixed
        {
            return "[NULL] {$message}";
        }
    }
}
