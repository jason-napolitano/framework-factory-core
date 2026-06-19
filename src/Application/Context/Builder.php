<?php

namespace FrameworkFactory\Application\Context {

    use FrameworkFactory\Contracts;

    /**
     * The context builder grants access to a service providers Context API,
     * allowing for services to be swapped depending on the context of the
     * swap
     */
    class Builder implements Contracts\Container\ContextBuilder
    {
        /** @var string $needs stores the specific dependency that will be overridden for a given context. */
        private string $needs;

        /**
         * New builder instance
         *
         * @param Contracts\Container\ContainerInstance $container
         * @param string                                $concrete
         */
        public function __construct(protected Contracts\Container\ContainerInstance $container, protected string $concrete)
        {
        }

        /**
         * @inheritdoc
         */
        public function needs(string $abstract): self
        {
            $this->needs = $abstract;
            return $this;
        }

        /**
         * @inheritdoc
         */
        public function give(callable|string $implementation): void
        {
            $this->container->addContextualBinding($this->concrete, $this->needs, $implementation);
        }
    }
}
