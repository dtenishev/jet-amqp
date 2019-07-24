<?php

namespace jetphp\rabbitmq\tests\integration;

use jetphp\rabbitmq\channel\PointToPointChannel;
use jetphp\rabbitmq\Listener;
use jetphp\rabbitmq\util\MessageBuilder;
use jetphp\signals\Handler;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class ListenerWithSignalHandlingTest extends TestCase {

	protected function getStreamConnection() {
		return new AMQPStreamConnection(
			\jetphp\rabbitmq\tests\HOST,
			\jetphp\rabbitmq\tests\PORT,
			\jetphp\rabbitmq\tests\USER,
			\jetphp\rabbitmq\tests\PASS,
			\jetphp\rabbitmq\tests\VHOST
		);
	}

	protected function getListener( $messageBuilder, $prefetchCount = 1, $autoAck = true, $noLocal = false, $exclusive = false ) {
		return new Listener( $messageBuilder, $prefetchCount, $autoAck, $noLocal, $exclusive );
	}

	protected function getMessageBuilder() {
		return new MessageBuilder();
	}

	protected function getPointToPointChannel( AMQPStreamConnection $connection, $channelId, $qname, $xname, array $queueParams = array() ) {
		return new PointToPointChannel(
			$connection->channel( $channelId ),
			$qname,
			$xname,
			$queueParams
		);
	}

	public function testListenerWithSignalHandling() {
		if ( !extension_loaded( 'pcntl' ) ) {
			$this->markTestSkipped( 'pcntl extension not loaded' );
		}
		if ( !extension_loaded( 'posix' ) ) {
			$this->markTestSkipped( 'posix extension not loaded' );
		}
		$qname = 'jetphp.rabbitmq.tests.functional.listenerWithSignalHandling';
		$waitTimeout = 1;
		$stopSigno = \SIGINT;
		$caughtSigno = null;
		$connection = $this->getStreamConnection();
		$signalHandler = $this->getSignalHandler();
		$pointToPointChannel = $this->getPointToPointChannel( $connection, 1, $qname, '' );
		$listener = $this->getListener( $this->getMessageBuilder(), 1 );
		$listener->attach( $pointToPointChannel );
		$signalHandler->handle( $stopSigno, function( $signo ) use ( &$caughtSigno, $listener ) {
			$caughtSigno = $signo;
			$listener->stop();
		} );
		$parendPid = getmypid();
		$childPid = pcntl_fork();
		if ( !$childPid ) {
			usleep( 50000 );
			posix_kill( $parendPid, $stopSigno );
			posix_kill( posix_getpid(), \SIGTERM );
		} else {
			pcntl_signal( \SIGCHLD, \SIG_IGN, false );
			$interrupted = $listener->wait( $waitTimeout );
			pcntl_waitpid( $childPid, $status );
			$this->assertTrue( $interrupted, 'Listener wasn\'t interrupted' );
			$this->assertEquals( $stopSigno, $caughtSigno, 'Listener wasn\'t interrupted by proper signal' );
		}
	}

	private function getSignalHandler() {
		return new Handler();
	}

}
