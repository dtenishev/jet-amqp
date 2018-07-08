<?php

namespace jetphp\rabbitmq\channel;

class InboundPubSubChannel extends PubSubChannel {

	public function bind() {
		list ( $qname ) = $this->channel->queue_declare(
			'',
			$this->getFeature()->isPassive(),
			$this->getFeature()->isDurable(),
			$isExclusive = true,
			$this->getFeature()->getAutoDelete()
		);
		$this->channel->exchange_declare(
			$this->xname,
			$type = 'fanout',
			$this->getFeature()->isPassive(),
			$this->getFeature()->isDurable(),
			$this->getFeature()->getAutoDelete()
		);
		$this->qname = $qname;
		$this->channel->queue_bind( $this->qname, $this->xname );
	}

}
