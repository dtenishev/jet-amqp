<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\core\Signal;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class SignalAwareListener extends Listener {

	protected $handler;
	protected $lastSignal;

	public function __construct(
		MessageBuilder $messageBuilder,
		$prefetchCount = 1,
		$autoAck = false,
		$noLocal = false,
		$exclusive = false,
		/*callable */$handler = null
	) {
		parent::__construct( $messageBuilder, $prefetchCount, $autoAck, $noLocal, $exclusive );
		$this->handler = $handler;
		$this->lastSignal = null;
		if ( !is_null( $handler ) && !is_callable( $handler ) ) {
			throw new \InvalidArgumentException( 'Expected callable for signal handler' );
		}
		$this->configureSignalHandling();
	}

	/**
	 * @param int $timeout in seconds, 0 means no timeout
	 * @return bool
	 * @throws \RuntimeException
	 */
	public function wait( $timeout = 0 ) {
		$interrupted = parent::wait( $timeout );
		return !is_null( $this->lastSignal ) ? true : $interrupted;
	}

	public function onSignal( $signo ) {
		$this->lastSignal = $signo;
		call_user_func( $this->handler, $signo, $this );
	}

	private function configureSignalHandling() {
		if ( !is_callable( $this->handler ) || !extension_loaded( 'pcntl' ) ) {
			return;
		}
		pcntl_signal(SIGTERM, array( $this, 'onSignal' ) );
		pcntl_signal(SIGQUIT, array( $this, 'onSignal' ) );
		pcntl_signal(SIGHUP, array( $this, 'onSignal' ) );
		pcntl_signal(SIGINT, array( $this, 'onSignal' ) );
	}

}
