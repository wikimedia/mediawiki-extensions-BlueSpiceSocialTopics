<?php

namespace BlueSpice\Social\Topics\HookHandler;

use BlueSpice\Social\Topics\Component\AfterContent;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class CommonUserInterface implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ): void {
		$registry->register(
			'DataAfterContent',
			[
				'social-topics' => [
					'factory' => static function () {
						return new AfterContent();
					},
					'position' => 10
				]
			]
		);
	}
}
