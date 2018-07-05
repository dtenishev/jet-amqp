<?php

namespace jet\amqp\channel;

use PhpAmqpLib\Channel\AMQPChannel;

abstract class Channel {

	protected $channel;
	protected $qname;
	protected $xname;

	public function __construct( AMQPChannel $channel, $qname, $xname ) {
		$this->channel = $channel;
		$this->qname = $qname;
		$this->xname = $xname;
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

}
