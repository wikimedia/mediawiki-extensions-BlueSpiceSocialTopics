<?php

namespace BlueSpice\Social\Topics\EntityListContext;

use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Social\Topics\Entity\Discussion;

class SpecialDiscussions extends \BlueSpice\Social\EntityListContext {

	public function getLimit() {
		return 10;
	}

	public function getLockedFilterNames() {
		return array_merge(
			parent::getLockedFilterNames(),
			[ Discussion::ATTR_TYPE ]
		);
	}

	public function getSortProperty() {
		return Discussion::ATTR_TIMESTAMP_CREATED;
	}

	protected function getTypeFilter() {
		return (object)[
			ListValue::KEY_PROPERTY => Discussion::ATTR_TYPE,
			ListValue::KEY_VALUE => [ Discussion::TYPE ],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => \BlueSpice\Data\FieldType::LISTVALUE
		];
	}

	/**
	 *
	 * @return boolean
	 */
	public function showEntitySpawner() {
		return false;
	}
}
