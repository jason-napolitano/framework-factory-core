<?php

namespace Tests\App\Providers {

    use FrameworkFactory\Attributes\Providers\CreatesBinding;
    use Tests\App\Services\MessageService;
    use FrameworkFactory\Contracts;

    #[CreatesBinding('message', MessageService::class)]
    class MessageServiceProvider extends Contracts\Providers\ServiceProvider
    {
        // ...
    }
}
