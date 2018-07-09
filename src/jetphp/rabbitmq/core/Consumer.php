<?php

namespace jetphp\rabbitmq\core;

use jetphp\rabbitmq\channel\Channel;

interface Consumer {

	public function bind( Channel $channel );

	/**
	 * @return Message|null
	 */
	public function get();

}
