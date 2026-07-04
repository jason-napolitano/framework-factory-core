<?php

namespace Tests\App\Providers {

    use Tests\App\Services\MessageService;
    use FrameworkFactory\Contracts;

    class DeferredServiceProvider extends Contracts\Providers\ServiceProvider
    {
        /**
         * @inheritdoc
         */
        public function register(): void
        {
            $this->singleton('deferred_message', fn () => new MessageService());
        }

        /**
         * @inheritdoc
         */
        public function provides(): array
        {
            return ['deferred_message'];
        }
    }
}
