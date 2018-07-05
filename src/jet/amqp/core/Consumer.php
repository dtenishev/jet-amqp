<?php

namespace jet\amqp\core;

use jet\amqp\channel\Channel;

interface Consumer {

	public function bind( Channel $channel );

	public function wait( $handler = null );

}
