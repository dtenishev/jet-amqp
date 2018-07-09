<?php

namespace jetphp\rabbitmq\channel;

use PhpAmqpLib\Wire\AMQPTable;

class PointToPointChannel extends DefaultChannel {

	/**
	 * @param bool $forced
	 */
	public function bind( $forced = false ) {
		if ( $this->binded && !$forced ) {
			return;
		}
		$this->channel->queue_declare(
			$this->qname,
			$this->getFeature()->isPassive(),
			$this->getFeature()->isDurable(),
			$this->getFeature()->isExclusive(),
			$this->getFeature()->getAutoDelete(),
			false,
			new AMQPTable( $this->queueParams )
		);
		$this->binded = true;
	}

}