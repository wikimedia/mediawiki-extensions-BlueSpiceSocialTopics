<?php

namespace BlueSpice\Social\Topics\Hook\BSUsageTrackerRegisterCollectors;

use BlueSpice\Social\Data\Entity\Store;
use BlueSpice\Social\Topics\Entity\Discussion;
use BS\UsageTracker\Hook\BSUsageTrackerRegisterCollectors;
use MWStake\MediaWiki\Component\DataStore\FieldType;
use MWStake\MediaWiki\Component\DataStore\Filter\StringValue;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;

class NoOfDiscussionPages extends BSUsageTrackerRegisterCollectors {

	protected function doProcess() {
		$store = new Store;
		$res = $store->getReader( $this->getContext() )
			->read(	new ReaderParams( $this->getParams() ) );

		$noOfDiscussionPages = count( $res->getRecords() );

		$this->collectorConfig['no-of-discussion-pages'] = [
			'class' => 'Basic',
			'config' => [
				'identifier' => 'no-of-discussion-pages',
				'internalDesc' => 'Number of Pages with Discussions',
				'count' => $noOfDiscussionPages
			]
		];
	}

	/**
	 * @return array
	 */
	protected function getParams(): array {
		return [
			ReaderParams::PARAM_LIMIT => ReaderParams::LIMIT_INFINITE,
			ReaderParams::PARAM_FILTER => $this->getFilter()
		];
	}

	/**
	 * @return array
	 */
	protected function getFilter(): array {
		return [ [
			StringValue::KEY_PROPERTY => Discussion::ATTR_TYPE,
			StringValue::KEY_TYPE => FieldType::STRING,
			StringValue::KEY_COMPARISON => StringValue::COMPARISON_EQUALS,
			StringValue::KEY_VALUE => Discussion::TYPE,
		] ];
	}
}
