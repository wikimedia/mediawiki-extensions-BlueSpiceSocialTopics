<?php

namespace BlueSpice\Social\Topics\Hook\LoadExtensionSchemaUpdates;

class AddRatedCommentsMigrationMaintenanceScript extends \BlueSpice\Hook\LoadExtensionSchemaUpdates {

	protected function doProcess() {
		$this->updater->addPostDatabaseUpdateMaintenance(
			'BSMigrateRatedComments'
		);
		return true;
	}

}
