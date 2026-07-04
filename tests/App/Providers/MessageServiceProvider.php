<?php

namespace Tests\App\Providers {

    use Tests\App\Services\MessageService;
    use FrameworkFactory\Contracts;

    class MessageServiceProvider extends Contracts\Providers\ServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(): void
        {
            $this->bind('message', fn () => new MessageService());
        }
    }
}
