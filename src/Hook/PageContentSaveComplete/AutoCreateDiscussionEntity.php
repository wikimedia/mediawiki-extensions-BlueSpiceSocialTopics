<?php

namespace BlueSpice\Social\Topics\Hook\PageContentSaveComplete;

use BlueSpice\Hook\PageContentSaveComplete;
use BlueSpice\Social\Topics\Entity\Discussion;

class AutoCreateDiscussionEntity extends PageContentSaveComplete {

	protected function checkTitle() {
		if( !$this->wikipage->getTitle() ) {
			return false;
		}
		if( !$this->wikipage->getTitle()->isTalkPage() ) {
			return false;
		}
		if( $this->wikipage->getTitle()->getNamespace() === NS_SOCIALENTITY_TALK ) {
			return true;
		}
		if( !$this->wikipage->getTitle()->exists() ) {
			return false;
		}
		return true;
	}

	protected function doProcess() {
		if( PHP_SAPI === 'cli' ) {
			//Dont autocreate when in cmd, as the index can not be requested and
			//an existing discussion not be found
			return true;
		}
		if( !$this->checkTitle() ) {
			return true;
		}
		
		$factory = $this->getServices()->getService(
			'BSSocialDiscussionEntityFactory'
		);
		$entity = $factory->newFromDiscussionTitle(
			$this->wikipage->getTitle()
		);
		if( !$entity instanceof Discussion ) {
			return true;
		}
		if( !$entity->exists() ) {
			//TODO: Status check
			$oTMPStatus = $entity->save();
			return true;
		}
		if( $entity->exists() ) {
			$entity->invalidateCache();
			return true;
		}
		return true;
	}
}
