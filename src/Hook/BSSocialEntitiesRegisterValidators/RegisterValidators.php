<?php

namespace BlueSpice\Social\Topics\Hook\BSSocialEntitiesRegisterValidators;
use BlueSpice\Social\Hook\BSSocialEntitiesRegisterValidators;

class RegisterValidators extends BSSocialEntitiesRegisterValidators {

	protected static $aValidators = [
		"BlueSpice\\Social\\Topics\\Validator\\Filter\\TitleID",
	];

	protected function doProcess() {
		$this->aFilterValidators = array_merge(
			$this->aFilterValidators,
			static::$aValidators
		);
		return true;
	}
}