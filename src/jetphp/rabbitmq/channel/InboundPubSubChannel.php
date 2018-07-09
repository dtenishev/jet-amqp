<?php

namespace jetphp\rabbitmq\channel;

use PhpAmqpLib\Wire\AMQPTable;

class InboundPubSubChannel extends PubSubChannel {

	/**
	 * @param bool $forced
	 */
	public function bind( $forced = false ) {
		if ( $this->binded && !$forced ) {
			return;
		}
		list ( $qname ) = $this->channel->queue_declare(
			$this->qname,
			$this->getFeature()->isPassive(),
			$this->getFeature()->isDurable(),
			$isExclusive = true,
			$this->getFeature()->getAutoDelete(),
			false,
			new AMQPTable( $this->queueParams )
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
		$this->binded = true;
	}

}
