<?php

namespace jetphp\rabbitmq\core;

class Message {

	protected $deliveryTag;
	protected $consumerTag;
	protected $redelivered;
	protected $routingKey;
	protected $properties;
	protected $body;

	public function __construct( $body = '', MessageProperties $messageProperties = null ) {
		$this->setBody( $body );
		if ( is_null( $messageProperties ) ) {
			$messageProperties = new MessageProperties();
		}
		$this->properties = $messageProperties;
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
	 * @return MessageProperties
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * @param MessageProperties $properties
	 */
	public function setProperties( MessageProperties $properties ) {
		$this->properties = $properties;
	}

}
