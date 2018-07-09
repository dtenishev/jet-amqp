<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Message\AMQPMessage;

class Listener extends AbstractConsumer implements \jetphp\rabbitmq\core\Listener {

	protected $prefetchCount;
	protected $noLocal;
	protected $exclusive;
	protected $consumerTag;

	public function __construct( MessageBuilder $messageBuilder, $prefetchCount = 1, $autoAck = false, $noLocal = false, $exclusive = false ) {
		parent::__construct( $messageBuilder, $autoAck );
		$this->prefetchCount = $prefetchCount;
		$this->noLocal = $noLocal;
		$this->exclusive = $exclusive;
		$this->consumerTag = null;
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

	public function stop() {
		$this->channel->getChannel()->basic_cancel( $this->consumerTag, false, true );
	}

}
