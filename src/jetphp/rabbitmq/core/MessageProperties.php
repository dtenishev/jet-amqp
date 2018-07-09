<?php

namespace jetphp\rabbitmq\core;

class MessageProperties {

	protected $contentType;
	protected $contentEncoding;
	protected $headers;
	protected $isPersistent;// delivery mode
	protected $priority;
	protected $correlationId;
	protected $replyTo;
	protected $expiration;
	protected $messageId;
	protected $timestamp;
	protected $type;
	protected $userId;
	protected $appId;

	public function __construct() {
		$this->priority = 0;
		$this->isPersistent = false;
		$this->appId = getmypid();
	}

	/**
	 * @return mixed
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * @param mixed $contentType
	 */
	public function setContentType( $contentType ) {
		$this->contentType = $contentType;
	}

	/**
	 * @return mixed
	 */
	public function getContentEncoding() {
		return $this->contentEncoding;
	}

	/**
	 * @param mixed $contentEncoding
	 */
	public function setContentEncoding( $contentEncoding ) {
		$this->contentEncoding = $contentEncoding;
	}

	/**
	 * @return mixed
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @param mixed $headers
	 */
	public function setHeaders( $headers ) {
		$this->headers = $headers;
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
	 * @return int
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority( $priority ) {
		$this->priority = $priority;
	}

	/**
	 * @return string
	 */
	public function getCorrelationId() {
		return $this->correlationId;
	}

	/**
	 * @param string $correlationId
	 */
	public function setCorrelationId( $correlationId ) {
		$this->correlationId = $correlationId;
	}

	/**
	 * @return mixed
	 */
	public function getReplyTo() {
		return $this->replyTo;
	}

	/**
	 * @param mixed $replyTo
	 */
	public function setReplyTo( $replyTo ) {
		$this->replyTo = $replyTo;
	}

	/**
	 * @return mixed
	 */
	public function getExpiration() {
		return $this->expiration;
	}

	/**
	 * @param mixed $expiration
	 */
	public function setExpiration( $expiration ) {
		$this->expiration = $expiration;
	}

	/**
	 * @return mixed
	 */
	public function getMessageId() {
		return $this->messageId;
	}

	/**
	 * @param mixed $messageId
	 */
	public function setMessageId( $messageId ) {
		$this->messageId = $messageId;
	}

	/**
	 * @return mixed
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}

	/**
	 * @param mixed $timestamp
	 */
	public function setTimestamp( $timestamp ) {
		$this->timestamp = $timestamp;
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType( $type ) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @param mixed $userId
	 */
	public function setUserId( $userId ) {
		$this->userId = $userId;
	}

	/**
	 * @return mixed
	 */
	public function getAppId() {
		return $this->appId;
	}

	/**
	 * @param mixed $appId
	 */
	public function setAppId( $appId ) {
		$this->appId = $appId;
	}

}
