<?php

namespace jetphp\rabbitmq\tests\unit;

use jetphp\rabbitmq\channel\PointToPointChannel;
use jetphp\rabbitmq\Dispatcher;
use jetphp\rabbitmq\Listener;
use jetphp\rabbitmq\util\ReusableMessageBuilder;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class PointToPointChannelTest extends TestCase {

	protected function getStreamConnection() {
		return new AMQPStreamConnection(
			JETPHP_RABBITMQ_TESTS_HOST,
			JETPHP_RABBITMQ_TESTS_PORT,
			JETPHP_RABBITMQ_TESTS_USER,
			JETPHP_RABBITMQ_TESTS_PASS,
			JETPHP_RABBITMQ_TESTS_VHOST
		);
	}

	protected function getPointToPointChannel( AMQPStreamConnection $connection, $channelId, $qname, $xname, array $queueParams = array() ) {
		return new PointToPointChannel(
			$connection->channel( $channelId ),
			$qname,
			$xname,
			$queueParams
		);
	}

	protected function getDispatcher() {
		return new Dispatcher();
	}

	protected function getListener( $messageBuilder, $prefetchCount = 1, $autoAck = true, $noLocal = false, $exclusive = false ) {
		return new Listener( $messageBuilder, $prefetchCount, $autoAck, $noLocal, $exclusive );
	}

	public function testPriority() {
		$maxMessages = 10;
		$qname = 'jetphp.rabbitmq.tests.unit.point_to_point_channel';
		$connection = $this->getStreamConnection();
		$dispatcher = $this->getDispatcher();
		$messageBuilder = new ReusableMessageBuilder();
		$listener = $this->getListener( $messageBuilder );
		$pointToPointChannel = $this->getPointToPointChannel( $connection, 1, $qname, '' );
		$pointToPointChannel->getFeature()->setExclusive( true );
		$dispatcher->bind( $pointToPointChannel );
		$listener->bind( $pointToPointChannel );
		$sent = 0;
		for ( $n = 0; $n < $maxMessages; $n++ ) {
			$dispatcher->send( $messageBuilder
				->setBody( 'Message #' . ($n+1) )
				->build() );
			$sent++;
		}

		$recv = 0;
		for ( $n = 0; $n < $maxMessages; $n++ ) {
			$message = $listener->directGet();
			$recv++;
		}
		$this->assertEquals( $sent, $recv, 'Sent/recv message count mismatch' );
	}

}
