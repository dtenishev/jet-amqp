<?php

namespace jetphp\rabbitmq\core;

use jetphp\rabbitmq\channel\Channel;

interface Listener {

	public function attach( Channel $channel );

	/**
	 * @param $handler
	 * @throws \InvalidArgumentException
	 */
	public function setMessageHandler( /*callable */$handler );

	/**
	 * @param int $timeout in seconds, 0 means no timeout
	 * @return mixed
	 */
	public function wait( $timeout = 0 );

}
