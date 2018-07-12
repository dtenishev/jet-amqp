<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class Listener extends AbstractConsumer implements \jetphp\rabbitmq\core\Listener {

	protected $prefetchCount;
	protected $noLocal;
	protected $exclusive;
	protected $consumerTag;
	protected $messageHandler;

	public function __construct( MessageBuilder $messageBuilder, $prefetchCount = 1, $autoAck = false, $noLocal = false, $exclusive = false ) {
		parent::__construct( $messageBuilder, $autoAck );
		$this->prefetchCount = $prefetchCount;
		$this->noLocal = $noLocal;
		$this->exclusive = $exclusive;
		$this->consumerTag = null;
	}

	/**
	 * @param callable $handler
	 * @throws \InvalidArgumentException
	 */
	public function setMessageHandler( /*callable */$handler ) {
		if ( !is_callable( $handler ) ) {
			throw new \InvalidArgumentException( 'Expected callable, got ' . gettype( $handler ) );
		}
		$this->messageHandler = $handler;
	}

	/**
	 * @param int $timeout in seconds, 0 means no timeout
	 * @return bool
	 * @throws \RuntimeException
	 */
	public function wait( $timeout = 0 ) {
		if ( !$this->channel ) {
			throw new \RuntimeException( 'No channel attached' );
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
				$this->channel->getChannel()->wait( null, $timeout > 0, $timeout );
			} catch ( AMQPTimeoutException $ex ) {
				break;
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
	public function onMessage( AMQPMessage $amqpMessage ) {
		$message = $this->buildMessage( $amqpMessage );
		if ( is_callable( $this->messageHandler ) ) {
			\call_user_func( $this->messageHandler, $message, $this );
		}
	}

	public function stop() {
		$this->channel->getChannel()->basic_cancel( $this->consumerTag, false, true );
	}

}
