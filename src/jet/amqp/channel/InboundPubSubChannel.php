<?php

namespace jet\amqp\channel;

class InboundPubSubChannel extends PubSubChannel {

	public function bind() {
		/**
		 * @var bool $isExclusive
		 *      true for private (accessible only by current connection and will be deleted after connection closes)
		 *      false for shared
		 */
		list ( $qname ) = $this->channel->queue_declare(
			'',
			$isPassive = false,
			$isDurable = false,
			$isExclusive = true,
			$autoDelete = false
		);
		$this->channel->exchange_declare(
			$this->xname,
			$type = 'fanout',
			$isPassive = false,
			$isDurable = false,
			$autoDelete = false
		);
		$this->qname = $qname;
		$this->channel->queue_bind( $this->qname, $this->xname );
	}

}
