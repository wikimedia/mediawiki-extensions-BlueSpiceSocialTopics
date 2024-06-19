<?php

namespace BlueSpice\Social\Topics\Event;

use BlueSpice\EntityFactory;
use BlueSpice\Social\Entity;
use BlueSpice\Social\Event\SocialTextEvent;
use MediaWiki\Permissions\GroupPermissionsLookup;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\User\UserFactory;
use MediaWiki\User\UserIdentity;
use Message;
use MWStake\MediaWiki\Component\Events\Delivery\IChannel;
use MWStake\MediaWiki\Component\Events\EventLink;
use stdClass;
use Title;
use Wikimedia\Rdbms\ILoadBalancer;

class SocialTopicsEvent extends SocialTextEvent {

	/** @var SpecialPageFactory */
	protected $specialPageFactory;

	/**
	 * @param ILoadBalancer $lb
	 * @param UserFactory $userFactory
	 * @param GroupPermissionsLookup $gpl
	 * @param EntityFactory $entityFactory
	 * @param SpecialPageFactory $specialPageFactory
	 * @param UserIdentity $agent
	 * @param stdClass $entityData
	 * @param string $action
	 */
	public function __construct(
		ILoadBalancer $lb, UserFactory $userFactory, GroupPermissionsLookup $gpl, EntityFactory $entityFactory,
		SpecialPageFactory $specialPageFactory, UserIdentity $agent,
		stdClass $entityData, string $action = self::ACTION_EDIT
	) {
		parent::__construct( $lb, $userFactory, $gpl, $entityFactory, $agent, $entityData, $action );
		$this->specialPageFactory = $specialPageFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function getLinks( IChannel $forChannel ): array {
		return [
			new EventLink(
				$this->getLinkTargetTitle()->getFullURL(),
				Message::newFromKey( 'bs-social-notification-topic-primary-link-label' )
			)
		];
	}

	/**
	 * @return Message
	 */
	public function getKeyMessage(): Message {
		return Message::newFromKey( "bs-social-topic-event-$this->action-desc" );
	}

	/**
	 * @inheritDoc
	 */
	public function getMessage( IChannel $forChannel ): Message {
		return Message::newFromKey( "bs-social-topic-event-$this->action" )->params( $this->getAgent()->getName() );
	}

	/**
	 *
	 * @return Title
	 */
	protected function getWatchedTitle() {
		return $this->entity->getRelatedTitle();
	}

	/**
	 * @inheritDoc
	 */
	public function getLinkTargetTitle(): Title {
		$title = $this->entity->getTitle();
		if ( $title instanceof Title && $title->exists() ) {
			$topicsSP = $this->specialPageFactory->getPage( 'Topics' );
			return $topicsSP->getPageTitle( $this->entity->get( Entity::ATTR_ID ) );
		}

		return parent::getTitle();
	}

	/**
	 * @return string
	 */
	public function getKey(): string {
		return 'bs-social-topics-event';
	}
}
