<?php

namespace jetphp\rabbitmq\tests\unit;

use jetphp\rabbitmq\channel\PointToPointChannel;
use jetphp\rabbitmq\channel\ChannelWithPriorities;
use jetphp\rabbitmq\Consumer;
use jetphp\rabbitmq\core\Message;
use jetphp\rabbitmq\Dispatcher;
use jetphp\rabbitmq\Listener;
use jetphp\rabbitmq\util\ReusableMessageBuilder;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class ChannelWithPrioritiesTest extends TestCase {

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

	protected function getConsumer( $messageBuilder, $autoAck = true ) {
		return new Consumer( $messageBuilder, $autoAck );
	}

	public function testPriority() {
		$maxPriority = 3;
		$maxMessages = 10;
		$qname = 'jetphp.rabbitmq.tests.unit.channel_with_priorities';
		$connection = $this->getStreamConnection();
		$dispatcher = $this->getDispatcher();
		$messageBuilder = new ReusableMessageBuilder();
		$consumer = $this->getConsumer( $messageBuilder );
		$pointToPointChannel = $this->getPointToPointChannel( $connection, 1, $qname, '' );
		$pointToPointChannel->getFeature()->setExclusive( true );
		$channelWithPriorities = new ChannelWithPriorities( $pointToPointChannel, $maxPriority );
		$dispatcher->bind( $channelWithPriorities );
		$consumer->bind( $channelWithPriorities );
		$sent = 0;
		for ( $n = 0; $n < $maxMessages; $n++ ) {
			$messagePriority = rand( 1, $maxPriority );
			$dispatcher->send( $messageBuilder
				->setBody( 'Message with priority ' . $messagePriority )
				->setPriority( $messagePriority )
				->build() );
			$sent++;
		}

		$recentPriority = $maxPriority;
		$recv = 0;
		for ( $n = 0; $n < $maxMessages; $n++ ) {
			$message = $consumer->get();
			$this->assertInstanceOf( 'jetphp\\rabbitmq\\core\\Message', $message, 'Invalid message received, got ' . gettype( $message ) );
			$messagePriority = $message->getProperties()->getPriority();
			$this->assertLessThanOrEqual( $recentPriority, $messagePriority, 'Invalid message received, expected priority=' . $recentPriority . ', got priority=' . $messagePriority );
			$recentPriority = $messagePriority;
			$recv++;
		}
		$this->assertEquals( $sent, $recv, 'Sent/recv message count mismatch' );
	}

}
