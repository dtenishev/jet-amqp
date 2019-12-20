<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\channel\Channel;
use jetphp\rabbitmq\core\Message;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractConsumer {

	/** @var Channel */
	protected $channel;
	protected $autoAck;
	/** @var MessageBuilder */
	protected $messageBuilder;

	protected function __construct( MessageBuilder $messageBuilder, $autoAck = false ) {
		$this->messageBuilder = $messageBuilder;
		$this->autoAck = $autoAck;
	}

	public function attach( Channel $channel ) {
		$this->channel = $channel;
	}

	public function ackMessage( Message $message ) {
		if ( $this->autoAck ) {
			return;
		}
		$this->channel->getChannel()->basic_ack( $message->getDeliveryTag() );
	}

	public function nackMessage( Message $message, $multiple = false, $requeue = false ) {
		if ( $this->autoAck ) {
			return;
		}
		$this->channel->getChannel()->basic_nack(
			$message->getDeliveryTag(),
			$multiple, $requeue
		);
	}

	public function rejectMessage( Message $message, $requeue = false ) {
		if ( $this->autoAck ) {
			return;
		}
		$this->channel->getChannel()->basic_reject(
			$message->getDeliveryTag(),
			$requeue
		);
	}

	/**
	 * @param AMQPMessage $amqpMessage
	 * @return Message
	 */
	protected function buildMessage( AMQPMessage $amqpMessage ) {
		$this->messageBuilder
			->setBody( unserialize( $amqpMessage->getBody() ) )
			->setDeliveryTag( $amqpMessage->get( 'delivery_tag' ) )
			->setRedelivered( $amqpMessage->get( 'redelivered' ) )
			->setRoutingKey( $amqpMessage->get( 'routing_key' ) )
		;
		if ( $amqpMessage->has( 'priority' ) ) {
			$this->messageBuilder
				->setPriority( $amqpMessage->get( 'priority' ) );
		}
		if ( $amqpMessage->has( 'reply_to' ) ) {
			$this->messageBuilder
				->setReplyTo( $amqpMessage->get( 'reply_to' ) );
		}
		if ( $amqpMessage->has( 'correlation_id' ) ) {
			$this->messageBuilder
				->setCorrelationId( $amqpMessage->get( 'correlation_id' ) );
		}
		return $this->messageBuilder->build();
	}

}
