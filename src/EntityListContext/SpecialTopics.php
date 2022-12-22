<?php

namespace BlueSpice\Social\Topics\EntityListContext;

use BlueSpice\Social\Topics\Entity\Topic;
use MWStake\MediaWiki\Component\DataStore\FieldType;
use MWStake\MediaWiki\Component\DataStore\Filter\ListValue;

class SpecialTopics extends \BlueSpice\Social\EntityListContext {

	/**
	 *
	 * @return int
	 */
	public function getLimit() {
		return 15;
	}

	/**
	 *
	 * @return array
	 */
	public function getLockedFilterNames() {
		return array_merge(
			parent::getLockedFilterNames(),
			[ Topic::ATTR_TYPE ]
		);
	}

	/**
	 *
	 * @return string
	 */
	public function getSortProperty() {
		return Topic::ATTR_TIMESTAMP_CREATED;
	}

	/**
	 *
	 * @return \stdClass
	 */
	protected function getTypeFilter() {
		return (object)[
			ListValue::KEY_PROPERTY => Topic::ATTR_TYPE,
			ListValue::KEY_VALUE => [ Topic::TYPE ],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => FieldType::LISTVALUE
		];
	}

	/**
	 *
	 * @return bool
	 */
	public function showEntitySpawner() {
		return true;
	}

	/**
	 *
	 * @return bool
	 */
	public function useEndlessScroll() {
		return true;
	}
}
