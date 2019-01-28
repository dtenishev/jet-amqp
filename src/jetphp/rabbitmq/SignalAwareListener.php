<?php

namespace jetphp\rabbitmq;

use jetphp\rabbitmq\core\Signal;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class SignalAwareListener extends Listener {

	protected $handler;
	protected $catchSignals;
	protected $lastSignal;

	public function __construct(
		/*callable */$handler,
		array $catchSignals,
		MessageBuilder $messageBuilder,
		$prefetchCount = 1,
		$autoAck = false,
		$noLocal = false,
		$exclusive = false
	) {
		parent::__construct( $messageBuilder, $prefetchCount, $autoAck, $noLocal, $exclusive );
		if ( !is_callable( $handler ) ) {
			throw new \InvalidArgumentException( 'Expected callable for signal handler' );
		}
		$this->handler = $handler;
		$this->catchSignals = $catchSignals;
		$this->lastSignal = null;
		$this->configureSignalHandling();
	}

	/**
	 * @param int $timeout in seconds, 0 means no timeout
	 * @return bool
	 * @throws \RuntimeException
	 */
	public function wait( $timeout = 0 ) {
		$interrupted = parent::wait( $timeout );
		if ( $interrupted && extension_loaded( 'pcntl' ) && function_exists( 'pcntl_signal_dispatch' ) ) {
			pcntl_signal_dispatch();
		}
		return !is_null( $this->lastSignal ) ? true : $interrupted;
	}

	public function getLastSignal() {
		return $this->lastSignal;
	}

	public function onSignal( $signo ) {
		$this->lastSignal = $signo;
		call_user_func( $this->handler, $signo, $this );
	}

	private function configureSignalHandling() {
		if ( !is_callable( $this->handler ) || !extension_loaded( 'pcntl' ) ) {
			return;
		}
		foreach ( $this->catchSignals as $signo ) {
			pcntl_signal( $signo, array( $this, 'onSignal' ), false );
		}
	}

}
