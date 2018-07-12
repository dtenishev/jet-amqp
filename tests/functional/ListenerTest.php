<?php

namespace jetphp\rabbitmq\tests\functional;

use jetphp\rabbitmq\channel\PointToPointChannel;
use jetphp\rabbitmq\Listener;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class ListenerTest extends TestCase {

	protected function getStreamConnection() {
		return new AMQPStreamConnection(
			JETPHP_RABBITMQ_TESTS_HOST,
			JETPHP_RABBITMQ_TESTS_PORT,
			JETPHP_RABBITMQ_TESTS_USER,
			JETPHP_RABBITMQ_TESTS_PASS,
			JETPHP_RABBITMQ_TESTS_VHOST
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

	public function testListenerWaitTimeout() {
		$qname = 'jetphp.rabbitmq.tests.functional.listener';
		$waitTimeout = .5;
		$connection = $this->getStreamConnection();
		$pointToPointChannel = $this->getPointToPointChannel( $connection, 1, $qname, '' );
		$listener = $this->getListener( $this->getMessageBuilder(), 1 );
		$listener->attach( $pointToPointChannel );
		$beginWait = microtime( true );
		$listener->wait( $waitTimeout );
		$endWait = microtime( true );
		$timeout = $endWait - $beginWait;
		$this->assertGreaterThan( .3, $timeout, 'Listener did not wait ' . $waitTimeout . ' sec' );
		$this->assertLessThan( .7, $timeout, 'Listener did not wait ' . $waitTimeout . ' sec' );
	}

}
