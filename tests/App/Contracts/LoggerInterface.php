<?php

namespace Tests\App\Contracts {

    interface LoggerInterface
    {
        public function log(string $message): mixed;
    }
}
