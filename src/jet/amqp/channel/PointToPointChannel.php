<?php

namespace jet\amqp\channel;

class PointToPointChannel extends Channel {

	public function bind() {
		$this->channel->queue_declare(
			$this->qname,
			$isPassive = false,
			$isDurable = true,
			$isExclusive = false,
			$autoDelete = false
		);
	}

}