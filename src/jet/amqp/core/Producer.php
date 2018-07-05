<?php

namespace jet\amqp\core;

use jet\amqp\channel\Channel;

interface Producer {

	public function bind( Channel $channel );

	public function send( Message $message );

}
