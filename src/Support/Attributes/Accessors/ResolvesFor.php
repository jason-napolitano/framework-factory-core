<?php

namespace FrameworkFactory\Support\Attributes\Accessors {

    use Attribute;

    #[Attribute(Attribute::TARGET_CLASS)]
    readonly class ResolvesFor
    {
        public function __construct(public string $accessor)
        {
        }
    }
}
