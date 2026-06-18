<?php

namespace Tests\Accessors {

	use FrameworkFactory\Application\Accessor;

	/**
     * @method static display(string $message): string
     */
    #[\FrameworkFactory\Attributes\Accessors\ResolvesFor('message')]
    class Message extends Accessor
    {
        // ...
    }
}
