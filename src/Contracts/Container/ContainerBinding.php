<?php

namespace FrameworkFactory\Contracts\Container {


	final readonly class ContainerBinding
	{
		/**
		 * A class that models the shape of a service being bound
		 * into the container
		 *
		 * @param mixed $factory the callable to load as the key
		 * @param bool  $shared  is the binding a shared instance?
		 */
		public function __construct(
			public mixed $factory,
			public bool $shared
		) {
		}
	}
}