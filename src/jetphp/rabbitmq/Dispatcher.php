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

	public function attach( Channel $channel ) {
		$this->channel = $channel;
	}

	public function send( Message $message ) {
		if ( !$this->channel ) {
			throw new \RuntimeException( 'No channel attached' );
		}
		$this->channel->bind();
		$amqpMessageProperties = array(
			'app_id' => $message->getProperties()->getAppId(),
			'timestamp' => microtime( 1 ) * 1000000,
			'message_id' => $message->getProperties()->getMessageId(),
			'delivery_mode' => $message->getProperties()->isPersistent() ? AMQPMessage::DELIVERY_MODE_PERSISTENT : AMQPMessage::DELIVERY_MODE_NON_PERSISTENT,
			'priority' => $message->getProperties()->getPriority(),
		);
		if ( $message->getProperties()->getReplyTo() ) {
			$amqpMessageProperties['reply_to'] = $message->getProperties()->getReplyTo();
		}
		if ( $message->getProperties()->getCorrelationId() ) {
			$amqpMessageProperties['correlation_id'] = $message->getProperties()->getCorrelationId();
		}
		$amqpMessage = new AMQPMessage( serialize( $message->getBody() ), $amqpMessageProperties );
		$this->channel->getChannel()->basic_publish(
			$amqpMessage,
			$this->channel->getXname(),
			$this->channel->getQname()
		);
	}

}
