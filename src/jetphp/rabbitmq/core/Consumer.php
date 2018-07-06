<?php

namespace jetphp\rabbitmq\core;

use jetphp\rabbitmq\channel\Channel;

interface Consumer {

	public function bind( Channel $channel );

	public function wait( $handler = null );

}
