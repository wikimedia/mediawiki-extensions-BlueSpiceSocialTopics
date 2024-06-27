<?php

namespace BlueSpice\Social\Topics\Event;

use MediaWiki\User\UserIdentity;
use Message;
use MWStake\MediaWiki\Component\Events\Delivery\IChannel;

class SocialTopicsForUserEvent extends SocialTopicsEvent {

	/**
	 * @return Message
	 */
	public function getKeyMessage(): Message {
		return Message::newFromKey( "bs-social-topic-event-for-user-$this->action-desc" );
	}

	/**
	 * @inheritDoc
	 */
	public function getMessage( IChannel $forChannel ): Message {
		return Message::newFromKey( "bs-social-topic-event-for-user-$this->action" )
			->params(
				$this->getAgent()->getName(),
				$this->getTitleAnchor(
					$this->doGetRelevantTitle(),
					$forChannel,
					Message::newFromKey( 'bs-social-notification-user-page-generic' )->text()
				)
			);
	}

	/**
	 * @return array|UserIdentity[]|null
	 */
	public function getPresetSubscribers(): ?array {
		$related = $this->doGetRelevantTitle();
		if ( $related && $related->getNamespace() === NS_USER ) {
			return [ $this->userFactory->newFromName( $related->getBaseText() ) ];
		}
		return [];
	}

	/**
	 * @return string
	 */
	public function getKey(): string {
		return 'bs-social-topics-for-user-event';
	}
}
