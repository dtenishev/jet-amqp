<?php

namespace jetphp\rabbitmq\channel;

use PhpAmqpLib\Wire\AMQPTable;

class PointToPointChannel extends DefaultChannel {

	public function bind() {
		$this->channel->queue_declare(
			$this->qname,
			$this->getFeature()->isPassive(),
			$this->getFeature()->isDurable(),
			$this->getFeature()->isExclusive(),
			$this->getFeature()->getAutoDelete(),
			false,
			new AMQPTable( $this->queueParams )
		);
	}

}