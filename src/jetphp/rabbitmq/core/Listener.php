<?php

namespace jetphp\rabbitmq\core;

use jetphp\rabbitmq\channel\Channel;

interface Listener {

	public function bind( Channel $channel );

	public function wait( $handler = null );

}
