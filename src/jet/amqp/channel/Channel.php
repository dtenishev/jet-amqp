<?php

namespace jet\amqp\channel;

use PhpAmqpLib\Channel\AMQPChannel;

interface Channel {

	/**
	 * @return AMQPChannel
	 */
	public function getChannel();

	/**
	 * @return string
	 */
	public function getQname();

	/**
	 * @return string
	 */
	public function getXname();

	/**
	 * @return void
	 */
	public function bind();

}
