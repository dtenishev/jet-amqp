<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\core\Message;
use jetphp\rabbitmq\core\Producer;
use jetphp\rabbitmq\channel\Channel;
use PhpAmqpLib\Message\AMQPMessage;

class Dispatcher implements Producer {

	/** @var Channel */
	protected $channel;

	public function __construct() {
		$this->channel = null;
	}

	public function bind( Channel $channel ) {
		$this->channel = $channel;
	}

	public function send( Message $message ) {
		if ( !$this->channel ) {
			throw new \RuntimeException( 'No Channel' );
		}
		$this->channel->bind();
		$amqpMessageProperties = array(
			'app_id' => getmypid(),
			'timestamp' => microtime( 1 ) * 1000000,
			'message_id' => $message->getMessageId(),
			'delivery_mode' => $message->isPersistent() ? AMQPMessage::DELIVERY_MODE_PERSISTENT : AMQPMessage::DELIVERY_MODE_NON_PERSISTENT,
		);
		if ( is_numeric( $message->getPriority() ) ) {
			$amqpMessageProperties['priority'] = $message->getPriority();
		}
		$amqpMessage = new AMQPMessage( serialize( $message->getBody() ), $amqpMessageProperties );
		$this->channel->getChannel()->basic_publish(
			$amqpMessage,
			$this->channel->getXname(),
			$this->channel->getQname()
		);
	}

}
