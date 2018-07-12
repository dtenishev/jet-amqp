<?php

namespace jetphp\rabbitmq\tests\functional;

use jetphp\rabbitmq\channel\InboundPubSubChannel;
use jetphp\rabbitmq\channel\OutboundPubSubChannel;
use jetphp\rabbitmq\Consumer;
use jetphp\rabbitmq\Dispatcher;
use jetphp\rabbitmq\Listener;
use jetphp\rabbitmq\util\MessageBuilder;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

class PubSubChannelTest extends TestCase {

	protected function getStreamConnection() {
		return new AMQPStreamConnection(
			\jetphp\rabbitmq\tests\HOST,
			\jetphp\rabbitmq\tests\PORT,
			\jetphp\rabbitmq\tests\USER,
			\jetphp\rabbitmq\tests\PASS,
			\jetphp\rabbitmq\tests\VHOST
		);
	}

	protected function getInboundPubSubChannel( AMQPStreamConnection $connection, $channelId, $qname, $xname, array $queueParams = array() ) {
		return new InboundPubSubChannel(
			$connection->channel( $channelId ),
			$qname,
			$xname,
			$queueParams
		);
	}

	protected function getOutboundPubSubChannel( AMQPStreamConnection $connection, $channelId, $qname, $xname, array $queueParams = array() ) {
		return new OutboundPubSubChannel(
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

	public function testPubSubChannel() {
		$qname = '';
		$xname = 'jetphp.rabbitmq.tests.functional.pub_sub_channel';
		$connection = $this->getStreamConnection();
		$messageBuilder = new MessageBuilder();
		$outboundChannel = $this->getOutboundPubSubChannel( $connection, 1, $qname, $xname );
		$outboundChannel->getFeature()->setAutoDelete( true );
		$dispatcher = $this->getDispatcher();
		$dispatcher->attach( $outboundChannel );
		$inboundChannel1 = $this->getInboundPubSubChannel( $connection, 1, $qname, $xname );
		$inboundChannel1->getFeature()->setExclusive( true )->setAutoDelete( true );
		$inboundChannel2 = $this->getInboundPubSubChannel( $connection, 1, $qname, $xname );
		$inboundChannel2->getFeature()->setExclusive( true )->setAutoDelete( true );
		$consumer1 = $this->getConsumer( $messageBuilder );
		$consumer2 = $this->getConsumer( $messageBuilder );
		$consumer1->attach( $inboundChannel1 );
		$consumer2->attach( $inboundChannel2 );

		$inboundChannel1->bind();
		$inboundChannel2->bind();

		$dispatcher->send( $messageBuilder->setBody( 'PubSub Message' )->setIsPersistent( false )->build() );

		$message1 = $consumer1->get();
		$this->assertInstanceOf( 'jetphp\\rabbitmq\\core\\Message', $message1, 'Subscriber1 got bad message from the channel' );
		$message2 = $consumer2->get();
		$this->assertInstanceOf( 'jetphp\\rabbitmq\\core\\Message', $message2, 'Subscriber2 got bad message from the channel' );
	}

}
