<?php

namespace BlueSpice\Social\Topics\EntityListContext;

use BlueSpice\Services;
use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Data\Filter\Numeric;
use BlueSpice\Social\Topics\Entity\Discussion;
use BlueSpice\Social\Topics\Entity\Topic;

class DiscussionPage extends \BlueSpice\Social\EntityListContext {
	const CONFIG_NAME_OUTPUT_TYPE = 'EntityListDiscussionPageOutputType';
	const CONFIG_NAME_TYPE_ALLOWED = 'EntityListDiscussionPageTypeAllowed';
	const CONFIG_NAME_TYPE_SELECTED = 'EntityListDiscussionPageTypeSelected';

	/**
	 * Owner of the user page
	 * @var Discussion
	 */
	protected $discussion = null;

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( \IContextSource $context, \Config $config, \User $user = null, Discussion $discussion = null ) {
		parent::__construct( $context, $config, $user, $discussion );
		$this->discussion = $discussion;
		if( !$this->discussion ) {
			throw new \MWException( 'Discussion entity missing' );
		}
	}

	protected function getDiscussionTitleIDFilter() {
		return (object)[
			Numeric::KEY_PROPERTY => Topic::ATTR_DISCUSSION_TITLE_ID,
			Numeric::KEY_VALUE => $this->discussion->getRelatedTitle()->getArticleID(),
			Numeric::KEY_COMPARISON => Numeric::COMPARISON_EQUALS,
			Numeric::KEY_TYPE => 'numeric'
		];
	}

	public function getFilters() {
		return array_merge( 
			parent::getFilters(),
			[ $this->getDiscussionTitleIDFilter() ]
		);
	}

	public function getLimit() {
		return 10;
	}

	public function getLockedFilterNames() {
		return array_merge(
			parent::getLockedFilterNames(),
			[ Topic::ATTR_DISCUSSION_TITLE_ID, Topic::ATTR_TYPE ]
		);
	}

	public function getSortProperty() {
		return Topic::ATTR_TIMESTAMP_CREATED;
	}

	protected function getTypeFilter() {
		return (object)[
			ListValue::KEY_PROPERTY => Topic::ATTR_TYPE,
			ListValue::KEY_VALUE => [ Topic::TYPE ],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => \BlueSpice\Data\FieldType::LISTVALUE
		];
	}

	public function getPreloadedEntities() {
		$preloaded = parent::getPreloadedEntities();
		$topic = Services::getInstance()->getBSEntityFactory()->newFromObject(
			$this->getRawTopic()
		);
		if( !$topic instanceof Topic ) {
			return $preloaded;
		}

		$status = $topic->userCan( 'create', $this->getUser() );
		if( !$status->isOK() ) {
			return $preloaded;
		}

		$preloaded[] = $this->getRawTopic();
		return $preloaded;
	}

	protected function getRawTopic() {
		$title = $this->discussion->getRelatedTitle();
		return (object) [
			Topic::ATTR_TYPE => Topic::TYPE,
			Topic::ATTR_DISCUSSION_TITLE_ID => (int) $title->getArticleID(),
			Topic::ATTR_RELATED_TITLE => $title->getFullText(),
		];
	}

	/**
	 *
	 * @return boolean
	 */
	public function showEntitySpawner() {
		return false;
	}

	public function getParent() {
		return $this->discussion;
	}
}
