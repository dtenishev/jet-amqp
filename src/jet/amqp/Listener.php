<?php

namespace jet\amqp;

use jet\amqp\core\Consumer;
use jet\amqp\core\Message;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Message\AMQPMessage;
use jet\amqp\channel\Channel;

class Listener implements Consumer {

	/** @var Channel */
	protected $channel;
	protected $prefetchCount;
	protected $autoAck;
	protected $noLocal;
	protected $exclusive;
	protected $consumerTag;

	public function __construct( $prefetchCount = 1, $autoAck = false, $noLocal = false, $exclusive = false ) {
		$this->channel = null;
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
		$this->consumerTag = $this->channel->getChannel()->basic_consume(
			$this->channel->getQname(),
			$consumerTag = '',// auto-generate
			$this->noLocal,
			$this->autoAck,
			$this->exclusive,
			$noWait = false,
			function ( AMQPMessage $amqpMessage ) use ( $handler ) {
				$message = new Message( unserialize( $amqpMessage->getBody() ) );
				$message->setDeliveryTag( $amqpMessage->delivery_info['delivery_tag'] );
				$message->setRedelivered( $amqpMessage->delivery_info['redelivered'] );
				$message->setRoutingKey( $amqpMessage->delivery_info['routing_key'] );
				if ( is_callable( $handler ) ) {
					\call_user_func( $handler, $message, $this );
				}
			}
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

}
