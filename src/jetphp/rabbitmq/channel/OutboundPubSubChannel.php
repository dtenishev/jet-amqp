<?php

namespace jetphp\rabbitmq\channel;

class OutboundPubSubChannel extends PubSubChannel {

	public function bind() {
		$this->channel->exchange_declare(
			$this->xname,
			$type = 'fanout',
			$this->getFeature()->isPassive(),
			$this->getFeature()->isDurable(),
			$this->getFeature()->getAutoDelete()
		);
	}

}
