<?php
namespace BlueSpice\Social\Topics\Hook\BSEntityRegister;
use BlueSpice\Hook\BSEntityRegister;

class RegisterEntities extends BSEntityRegister {
	protected static $aEntities = array(
		'topic' => "\\BlueSpice\\Social\\Topics\\EntityConfig\\Topic",
		'discussion' => "\\BlueSpice\\Social\\Topics\\EntityConfig\\Discussion",
	);

	protected function doProcess() {
		$this->entityRegistrations = array_merge(
			$this->entityRegistrations,
			self::$aEntities
		);
		return true;
	}
}

