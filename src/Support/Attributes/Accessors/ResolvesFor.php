<?php

namespace FrameworkFactory\Support\Attributes\Accessors {

    use Attribute;

    #[Attribute(Attribute::TARGET_CLASS)]
    readonly class ResolvesFor
    {
        /**
         * @param string $id identifier of the entry to look for
         */
        public function __construct(public string $id)
        {
            // ...
        }
    }
}
