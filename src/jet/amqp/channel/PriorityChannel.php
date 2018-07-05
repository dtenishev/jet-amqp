<?php

namespace jet\amqp\channel;

use PhpAmqpLib\Channel\AMQPChannel;

class PriorityChannel implements Channel {

	protected $parentChannel;
	protected $maxPriority;

	public function __construct( DefaultChannel $channel ) {
		$this->parentChannel = $channel;
	}

	public function setMaxPriority( $maxPriority ) {
		$this->maxPriority = $maxPriority;
	}

	/**
	 * @return AMQPChannel
	 */
	public function getChannel() {
		return $this->parentChannel->getChannel();
	}

	/**
	 * @return string
	 */
	public function getQname() {
		return $this->parentChannel->getQname();
	}

	/**
	 * @return string
	 */
	public function getXname() {
		return $this->parentChannel->getXname();
	}

	public function bind() {
		if ( !$this->maxPriority ) {
			throw new \RuntimeException( 'Undefined max priority' );
		}
		$this->parentChannel->setQueueParam( 'x-max-priority', $this->maxPriority );
		$this->parentChannel->bind();
	}

}
