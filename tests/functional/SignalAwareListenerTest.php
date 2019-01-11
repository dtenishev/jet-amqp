<?php

namespace jetphp\rabbitmq\tests\functional;

use jetphp\rabbitmq\channel\PointToPointChannel;
use jetphp\rabbitmq\Listener;
use jetphp\rabbitmq\SignalAwareListener;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class SignalAwareListenerTest extends TestCase {

	protected function setUp() {
		if ( !extension_loaded( 'pcntl' ) ) {
			$this->markTestSkipped( 'pcntl extension not loaded' );
		}
		if ( !extension_loaded( 'posix' ) ) {
			$this->markTestSkipped( 'posix extension not loaded' );
		}
	}

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
		return new SignalAwareListener( $messageBuilder, $prefetchCount, $autoAck, $noLocal, $exclusive, array( $this, 'cancelListener' ) );
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

	public function testSignalAwareListener() {
		$this->configureAlarmHandler();
		$qname = 'jetphp.rabbitmq.tests.functional.signalAwareListener';
		$waitTimeout = 2;
		$connection = $this->getStreamConnection();
		$pointToPointChannel = $this->getPointToPointChannel( $connection, 1, $qname, '' );
		$listener = $this->getListener( $this->getMessageBuilder(), 1 );
		$listener->attach( $pointToPointChannel );
		$interrupted = $listener->wait( $waitTimeout );
		$this->assertTrue( $interrupted === true, 'Listener wasn\'t interrupted' );
	}

	private function configureAlarmHandler() {
		pcntl_signal( \SIGALRM, function() {
			posix_kill( getmypid(), \SIGINT );
		} );
		pcntl_alarm( 1 );
	}

	public function cancelListener( $signo, Listener $listener ) {
		$listener->stop();
	}

}
