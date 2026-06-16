<?php

namespace Tests\Accessors {

	use FrameworkFactory\Application\Accessor;
	use FrameworkFactory\Support\Attributes;
	use Tests\Services\MessageService;

    /**
     * @method static display(string $message): string
     */
    #[Attributes\Accessors\ResolvesFor('message')]
    class Message extends Accessor
    {
        // ...
    }
}
