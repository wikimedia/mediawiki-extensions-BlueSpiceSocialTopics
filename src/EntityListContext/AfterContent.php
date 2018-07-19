<?php

namespace BlueSpice\Social\Topics\EntityListContext;

use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Data\Filter\Numeric;
use BlueSpice\Social\Topics\Entity\Topic;
use BlueSpice\Services;

class AfterContent extends \BlueSpice\Social\EntityListContext {

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( \IContextSource $context, \Config $config, \User $user = null, Entity $entity = null, \Title $title = null ) {
		parent::__construct( $context, $config, $user, $entity );
		if( $title ) {
			$this->title = $title;
		}
	}

	public function getTitle() {
		return $this->title ? $this->title : $this->context->getTitle();
	}

	public function getLimit() {
		return 3;
	}

	public function getSortProperty() {
		return Topic::ATTR_TIMESTAMP_TOUCHED;
	}

	public function useEndlessScroll() {
		return false;
	}

	public function useMoreScroll() {
		return false;
	}

	public function getLockedFilterNames() {
		return array_merge(
			parent::getLockedFilterNames(),
			[ Topic::ATTR_TYPE ]
		);
	}

	public function getOutputTypes() {
		return array_merge( parent::getOutputTypes(), [ Topic::TYPE => 'Default'] );
	}

	protected function getDiscussionTitleIDFilter() {
		return (object)[
			Numeric::KEY_PROPERTY => Topic::ATTR_DISCUSSION_TITLE_ID,
			Numeric::KEY_VALUE => $this->getTitle()->getTalkPage()->getArticleID(),
			Numeric::KEY_COMPARISON => Numeric::COMPARISON_EQUALS,
			Numeric::KEY_TYPE => 'numeric'
		];
	}

	/**
	 *
	 * @return \stdClass[]
	 */
	protected function getTypeFilter() {
		return (object)[
			ListValue::KEY_PROPERTY => Topic::ATTR_TYPE,
			ListValue::KEY_VALUE => [ Topic::TYPE ],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => \BlueSpice\Data\FieldType::LISTVALUE
		];
	}

	public function getFilters() {
		return array_merge( 
			parent::getFilters(),
			[ $this->getDiscussionTitleIDFilter() ]
		);
	}

	public function getMoreLink() {
		return Services::getInstance()->getLinkRenderer()->makeKnownLink(
			$this->getTitle()->getTalkPage(),
			new \HtmlArmor( $this->getMoreLinkMessage()->text() )
		);
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
		$talkPage = $this->getTitle()->getTalkPage();
		return (object) [
			Topic::ATTR_TYPE => Topic::TYPE,
			Topic::ATTR_DISCUSSION_TITLE_ID => $talkPage->getArticleID(),
			Topic::ATTR_RELATED_TITLE => $talkPage->getFullText(),
		];
	}

	public function showEntityListMenu() {
		return false;
	}

	public function showHeadline() {
		return true;
	}

	public function getHeadlineMessageKey() {
		return 'bs-socialtopics-aftercontent-heading';
	}
}
