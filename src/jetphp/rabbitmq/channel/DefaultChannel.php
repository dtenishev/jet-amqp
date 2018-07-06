<?php

namespace jetphp\rabbitmq\channel;

use PhpAmqpLib\Channel\AMQPChannel;

abstract class DefaultChannel implements Channel {

	protected $channel;
	protected $qname;
	protected $xname;
	protected $queueParams;

	public function __construct( AMQPChannel $channel, $qname, $xname, array $queueParams = array() ) {
		$this->channel = $channel;
		$this->qname = $qname;
		$this->xname = $xname;
		$this->queueParams = $queueParams;
	}

	/**
	 * @return AMQPChannel
	 */
	public function getChannel() {
		return $this->channel;
	}

	abstract public function bind();

	/**
	 * @return mixed
	 */
	public function getQname() {
		return $this->qname;
	}

	/**
	 * @return mixed
	 */
	public function getXname() {
		return $this->xname;
	}

	public function setQueueParam( $key, $value ) {
		$this->queueParams[$key] = $value;
	}

}
