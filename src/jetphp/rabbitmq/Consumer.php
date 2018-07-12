<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\core\Message;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends AbstractConsumer implements \jetphp\rabbitmq\core\Consumer {

	protected $prefetchCount;

	public function __construct( MessageBuilder $messageBuilder, $autoAck = false ) {
		parent::__construct( $messageBuilder, $autoAck );
		$this->consumerTag = null;
	}

	/**
	 * @return Message|null
	 */
	public function get() {
		if ( !$this->channel ) {
			throw new \RuntimeException( 'No channel attached' );
		}
		$this->channel->bind();
		/** @var AMQPMessage $amqpMessage */
		$amqpMessage = $this->channel->getChannel()->basic_get( $this->channel->getQname(), $this->autoAck );
		if ( !$amqpMessage ) {
			return null;
		}
		return $this->buildMessage( $amqpMessage );
	}

}
