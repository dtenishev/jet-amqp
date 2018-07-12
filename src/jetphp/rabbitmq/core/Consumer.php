<?php

namespace jetphp\rabbitmq\core;

use jetphp\rabbitmq\channel\Channel;

interface Consumer {

	public function attach( Channel $channel );

	/**
	 * @return Message|null
	 */
	public function get();

}
