<?php

namespace jet\amqp\channel;

use PhpAmqpLib\Wire\AMQPTable;

class PointToPointChannel extends DefaultChannel {

	protected $isPassiveQueue = false;
	protected $isDurableQueue = false;
	protected $isExclusiveQueue = false;
	protected $autoDeleteQueue = false;

	/**
	 * @return mixed
	 */
	public function isPassiveQueue() {
		return $this->isPassiveQueue;
	}

	/**
	 * @param mixed $isPassiveQueue
	 */
	public function setIsPassiveQueue( $isPassiveQueue ) {
		$this->isPassiveQueue = $isPassiveQueue;
	}

	/**
	 * @return mixed
	 */
	public function isDurableQueue() {
		return $this->isDurableQueue;
	}

	/**
	 * @param mixed $isDurableQueue
	 */
	public function setIsDurableQueue( $isDurableQueue ) {
		$this->isDurableQueue = $isDurableQueue;
	}

	/**
	 * @return mixed
	 */
	public function isExclusiveQueue() {
		return $this->isExclusiveQueue;
	}

	/**
	 * @param mixed $isExclusiveQueue
	 */
	public function setIsExclusiveQueue( $isExclusiveQueue ) {
		$this->isExclusiveQueue = $isExclusiveQueue;
	}

	/**
	 * @return mixed
	 */
	public function getAutoDeleteQueue() {
		return $this->autoDeleteQueue;
	}

	/**
	 * @param mixed $autoDeleteQueue
	 */
	public function setAutoDeleteQueue( $autoDeleteQueue ) {
		$this->autoDeleteQueue = $autoDeleteQueue;
	}

	public function bind() {
		$this->channel->queue_declare(
			$this->qname,
			$this->isPassiveQueue,
			$this->isDurableQueue,
			$this->isExclusiveQueue,
			$this->autoDeleteQueue,
			false,
			new AMQPTable( $this->queueParams )
		);
	}

}