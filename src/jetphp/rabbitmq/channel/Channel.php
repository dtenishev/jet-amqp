<?php

namespace jetphp\rabbitmq\channel;

use jetphp\rabbitmq\core\ChannelFeature;
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

	/**
	 * @return ChannelFeature
	 */
	public function getFeature();

}
