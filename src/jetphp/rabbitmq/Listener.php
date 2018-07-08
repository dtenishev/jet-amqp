<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\channel\Channel;
use jetphp\rabbitmq\core\Consumer;
use jetphp\rabbitmq\core\Message;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Message\AMQPMessage;

class Listener implements Consumer {

	/** @var Channel */
	protected $channel;
	protected $messageBuilder;
	protected $prefetchCount;
	protected $autoAck;
	protected $noLocal;
	protected $exclusive;
	protected $consumerTag;

	public function __construct( MessageBuilder $messageBuilder, $prefetchCount = 1, $autoAck = false, $noLocal = false, $exclusive = false ) {
		$this->channel = null;
		$this->messageBuilder = $messageBuilder;
		$this->prefetchCount = $prefetchCount;
		$this->autoAck = $autoAck;
		$this->noLocal = $noLocal;
		$this->exclusive = $exclusive;
		$this->consumerTag = null;
	}

	public function bind( Channel $channel ) {
		$this->channel = $channel;
	}

	/**
	 * @param callable|null $handler
	 * @return bool
	 * @throws \RuntimeException
	 */
	public function wait( $handler = null ) {
		if ( !$this->channel ) {
			throw new \RuntimeException( 'No Channel' );
		}
		$this->channel->bind();
		// setup quality of service
		$this->channel->getChannel()->basic_qos(
			null,
			$this->prefetchCount,
			null
		);
		$self = $this;
		$this->consumerTag = $this->channel->getChannel()->basic_consume(
			$this->channel->getQname(),
			$consumerTag = '',// auto-generate
			$this->noLocal,
			$this->autoAck,
			$this->exclusive,
			$noWait = false,
			array( $this, 'onMessage' )
		);
		$interrupted = false;
		while ( count( $this->channel->getChannel()->callbacks ) ) {
			try {
				$this->channel->getChannel()->wait();
			} catch ( AMQPExceptionInterface $ex ) {
				$interrupted = true;
				break;
			}
		}
		return $interrupted;
	}

	/**
	 * @param AMQPMessage $amqpMessage
	 * @param callable|null $handler
	 */
	public function onMessage( AMQPMessage $amqpMessage, $handler ) {
		$message = $this->buildMessage( $amqpMessage );
		if ( is_callable( $handler ) ) {
			\call_user_func( $handler, $message, $this );
		}
	}

	/**
	 * @return Message|null
	 */
	public function directGet() {
		$this->channel->bind();
		/** @var AMQPMessage $amqpMessage */
		$amqpMessage = $this->channel->getChannel()->basic_get( $this->channel->getQname(), $this->autoAck );
		if ( !$amqpMessage ) {
			return null;
		}
		return $this->buildMessage( $amqpMessage );
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

	public function stop() {
		$this->channel->getChannel()->basic_cancel( $this->consumerTag, false, true );
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
			$this->messageBuilder->setPriority( $amqpMessage->get( 'priority' ) );
		}
		return $this->messageBuilder->build();
	}

}
