<?php

namespace jet\amqp\channel;

class OutboundPubSubChannel extends PubSubChannel {

	public function bind() {
		$this->channel->exchange_declare(
			$this->xname,
			$type = 'fanout',
			$isPassive = false,
			$isDurable = false,
			$autoDelete = false
		);
	}

}
