<?php

namespace jetphp\rabbitmq\core;

use jetphp\rabbitmq\channel\Channel;

interface Producer {

	public function attach( Channel $channel );

	public function send( Message $message );

}
