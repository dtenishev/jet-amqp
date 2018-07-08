<?php

namespace jetphp\rabbitmq\util;

use jetphp\rabbitmq\core\Message;

class MessageBuilder {

	protected $issuerId;
	protected $messageId;
	protected $isPersistent;
	protected $deliveryTag;
	protected $consumerTag;
	protected $redelivered;
	protected $routingKey;
	protected $priority;
	protected $body;

	protected function createMessage() {
		return new Message();
	}

	public function build() {
		$obj = $this->createMessage();
		$obj->setIssuerId( $this->issuerId );
		$obj->setMessageId( $this->messageId );
		$obj->setIsPersistent( $this->isPersistent );
		$obj->setDeliveryTag( $this->deliveryTag );
		$obj->setConsumerTag( $this->consumerTag );
		$obj->setRedelivered( $this->redelivered );
		$obj->setRoutingKey( $this->routingKey );
		$obj->setPriority( $this->priority );
		$obj->setBody( $this->body );
		return $obj;
	}

	/**
	 * @param mixed $issuerId
	 * @return MessageBuilder
	 */
	public function setIssuerId( $issuerId ) {
		$this->issuerId = $issuerId;
		return $this;
	}

	/**
	 * @param mixed $messageId
	 * @return MessageBuilder
	 */
	public function setMessageId( $messageId ) {
		$this->messageId = $messageId;
		return $this;
	}

	/**
	 * @param mixed $isPersistent
	 * @return MessageBuilder
	 */
	public function setIsPersistent( $isPersistent ) {
		$this->isPersistent = $isPersistent;
		return $this;
	}

	/**
	 * @param mixed $deliveryTag
	 * @return MessageBuilder
	 */
	public function setDeliveryTag( $deliveryTag ) {
		$this->deliveryTag = $deliveryTag;
		return $this;
	}

	/**
	 * @param mixed $consumerTag
	 * @return MessageBuilder
	 */
	public function setConsumerTag( $consumerTag ) {
		$this->consumerTag = $consumerTag;
		return $this;
	}

	/**
	 * @param mixed $redelivered
	 * @return MessageBuilder
	 */
	public function setRedelivered( $redelivered ) {
		$this->redelivered = $redelivered;
		return $this;
	}

	/**
	 * @param mixed $routingKey
	 * @return MessageBuilder
	 */
	public function setRoutingKey( $routingKey ) {
		$this->routingKey = $routingKey;
		return $this;
	}

	/**
	 * @param mixed $priority
	 * @return MessageBuilder
	 */
	public function setPriority( $priority ) {
		$this->priority = $priority;
		return $this;
	}

	/**
	 * @param mixed $body
	 * @return MessageBuilder
	 */
	public function setBody( $body ) {
		$this->body = $body;
		return $this;
	}

}
