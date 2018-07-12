<?php

namespace jetphp\rabbitmq\tests\unit;

use jetphp\rabbitmq\core\Message;
use jetphp\rabbitmq\Dispatcher;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase {

	public function testDispatcher() {
		$dispatcher = $this->getDispatcher();
		$exception = null;
		try {
			$dispatcher->send( new Message() );
		} catch ( \RuntimeException $exception ) {
		}
		$this->assertInstanceOf( 'RuntimeException', $exception,  'Expected RuntimeException' );
	}

	private function getDispatcher() {
		return new Dispatcher();
	}

}
