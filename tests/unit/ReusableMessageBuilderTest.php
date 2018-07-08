<?php

namespace jetphp\rabbitmq\tests\unit;

use jetphp\rabbitmq\util\ReusableMessageBuilder;
use PHPUnit\Framework\TestCase;

class ReusableMessageBuilderTest extends TestCase {

	public function testBuiltMessage() {
		$builder = new ReusableMessageBuilder();
		$message1 = $builder->build();
		$message2 = $builder->build();
		$this->assertEquals( spl_object_hash( $message1 ), spl_object_hash( $message2 ), 'Message object wasn\'t reused' );
	}

}
