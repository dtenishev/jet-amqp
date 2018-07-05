<?php

namespace jet\amqp;

use jet\amqp\core\Message;
use jet\amqp\core\Producer;
use PhpAmqpLib\Message\AMQPMessage;
use jet\amqp\channel\Channel;

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
		$amqpMessage = new AMQPMessage( serialize( $message->getBody() ), array(
			'app_id' => getmypid(),
			'timestamp' => microtime( 1 ) * 1000000,
			'message_id' => $message->getMessageId(),
			'delivery_mode' => $message->isPersistent() ? AMQPMessage::DELIVERY_MODE_PERSISTENT : AMQPMessage::DELIVERY_MODE_NON_PERSISTENT,
		) );
		$this->channel->getChannel()->basic_publish(
			$amqpMessage,
			$this->channel->getXname(),
			$this->channel->getQname()
		);
	}

}
