<?php

namespace jetphp\rabbitmq\core;

class ChannelFeature {

	protected $isPassive;
	protected $isDurable;
	protected $exclusive;
	protected $autoDelete;

	/**
	 * ChannelFeature constructor
	 * @param $isPassive
	 * @param $isDurable
	 * @param $exclusive
	 * @param $autoDelete
	 */
	public function __construct( $isPassive = false, $isDurable = false, $exclusive = false, $autoDelete = false ) {
		$this->isPassive = $isPassive;
		$this->isDurable = $isDurable;
		$this->exclusive = $exclusive;
		$this->autoDelete = $autoDelete;
	}

	/**
	 * @return mixed
	 */
	public function isPassive() {
		return $this->isPassive;
	}

	/**
	 * @param mixed $isPassive
	 */
	public function setIsPassive( $isPassive ) {
		$this->isPassive = $isPassive;
	}

	/**
	 * @return mixed
	 */
	public function isDurable() {
		return $this->isDurable;
	}

	/**
	 * @param mixed $isDurable
	 * @return ChannelFeature
	 */
	public function setIsDurable( $isDurable ) {
		$this->isDurable = $isDurable;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function isExclusive() {
		return $this->exclusive;
	}

	/**
	 * @param mixed $exclusive
	 * @return ChannelFeature
	 */
	public function setExclusive( $exclusive ) {
		$this->exclusive = $exclusive;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAutoDelete() {
		return $this->autoDelete;
	}

	/**
	 * @param mixed $autoDelete
	 * @return ChannelFeature
	 */
	public function setAutoDelete( $autoDelete ) {
		$this->autoDelete = $autoDelete;
		return $this;
	}

}
