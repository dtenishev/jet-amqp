<?php

namespace jetphp\rabbitmq\util;

class ReusableMessageBuilder extends MessageBuilder {

	private $message = null;

	protected function createMessage() {
		if ( is_null( $this->message ) ) {
			$this->message = parent::createMessage();
		}
		return $this->message;
	}

}
