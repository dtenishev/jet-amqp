<?php

namespace jetphp\rabbitmq\channel;

class OutboundPubSubChannel extends PubSubChannel {

	/**
	 * @param bool $forced
	 */
	public function bind( $forced = false ) {
		if ( $this->binded && !$forced ) {
			return;
		}
		$this->channel->exchange_declare(
			$this->xname,
			$type = 'fanout',
			$this->getFeature()->isPassive(),
			$this->getFeature()->isDurable(),
			$this->getFeature()->getAutoDelete()
		);
		$this->binded = true;
	}

}
