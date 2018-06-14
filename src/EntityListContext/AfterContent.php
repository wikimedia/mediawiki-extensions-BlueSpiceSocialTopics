<?php

namespace BlueSpice\Social\Topics\EntityListContext;

use BlueSpice\Data\Filter\ListValue;
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
	public function __construct( \IContextSource $context, \Config $config, \User $user = null, \Title $title = null ) {
		parent::__construct( $context, $config, $user );
		$this->title = $title;
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
		return array_merge( parent::getOutputTypes(), [ Topic::TYPE => 'Short'] );
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

	public function getMoreLink() {
		return Services::getInstance()->getLinkRenderer()->makeKnownLink(
			$this->title->getTalkPage(),
			new \HtmlArmor( $this->getMoreLinkMessage()->text() )
		);
	}
}
