<?php

namespace jetphp\rabbitmq\core;

class Message {

	protected $issuerId;
	protected $messageId;
	protected $isPersistent;
	protected $deliveryTag;
	protected $consumerTag;
	protected $redelivered;
	protected $routingKey;
	protected $priority;
	protected $body;

	public function __construct( $body = '' ) {
		$this->issuerId = null;
		$parts = explode( ' ', microtime() );
		$this->messageId = $parts[1].substr( $parts[0], 2 );
		$this->isPersistent = false;
		$this->setBody( $body );
	}

	/**
	 * @return int|null
	 */
	public function getIssuerId() {
		return $this->issuerId;
	}

	/**
	 * @param int|null $issuerId
	 */
	public function setIssuerId( $issuerId ) {
		$this->issuerId = $issuerId;
	}

	/**
	 * @return string
	 */
	public function getMessageId() {
		return $this->messageId;
	}

	/**
	 * @param string $messageId
	 */
	public function setMessageId( $messageId ) {
		$this->messageId = $messageId;
	}

	/**
	 * @return bool
	 */
	public function isPersistent() {
		return $this->isPersistent;
	}

	/**
	 * @param bool $isPersistent
	 */
	public function setIsPersistent( $isPersistent ) {
		$this->isPersistent = $isPersistent;
	}

	/**
	 * @return mixed
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param mixed $body
	 */
	public function setBody( $body ) {
		$this->body = $body;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryTag() {
		return $this->deliveryTag;
	}

	/**
	 * @param mixed $deliveryTag
	 */
	public function setDeliveryTag( $deliveryTag ) {
		$this->deliveryTag = $deliveryTag;
	}

	/**
	 * @return mixed
	 */
	public function getConsumerTag() {
		return $this->consumerTag;
	}

	/**
	 * @param mixed $consumerTag
	 */
	public function setConsumerTag( $consumerTag ) {
		$this->consumerTag = $consumerTag;
	}

	/**
	 * @return mixed
	 */
	public function getRedelivered() {
		return $this->redelivered;
	}

	/**
	 * @param mixed $redelivered
	 */
	public function setRedelivered( $redelivered ) {
		$this->redelivered = $redelivered;
	}

	/**
	 * @return mixed
	 */
	public function getRoutingKey() {
		return $this->routingKey;
	}

	/**
	 * @param mixed $routingKey
	 */
	public function setRoutingKey( $routingKey ) {
		$this->routingKey = $routingKey;
	}

	/**
	 * @return mixed
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * @param mixed $priority
	 */
	public function setPriority( $priority ) {
		$this->priority = $priority;
	}

}
