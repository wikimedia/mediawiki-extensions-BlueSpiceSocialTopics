<?php

namespace BlueSpice\Social\Topics\Hook\BSMigrateSettingsFromDeviatingNames;

use BlueSpice\Hook\BSMigrateSettingsFromDeviatingNames;

class MigrateSettings extends BSMigrateSettingsFromDeviatingNames {

	protected function skipProcessing() {
		if( in_array( $this->oldName, $this->getSkipSettings() ) ) {
			return false;
		}
		return true;
	}

	protected function doProcess() {
		$this->newName = 'SocialTopicsTalkPageAutoCreate';
	}

	protected function getSkipSettings() {
		return [
			'MW::BSSocial::enTalkpageAutoCreate',
		];
	}
}
