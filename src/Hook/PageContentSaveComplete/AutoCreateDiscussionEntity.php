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
		if( $this->wikipage->getTitle()->getNamespace() === NS_SOCIAL_ENTITY_TALK ) {
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
		
		$oEntity = Discussion::newFromDiscussionTitle(
			$this->wikipage->getTitle()
		);
		if( !$oEntity instanceof Discussion ) {
			return true;
		}
		if( !$oEntity->exists() ) {
			//TODO: Status check
			$oTMPStatus = $oEntity->save();
			return true;
		}
		if( $oEntity->exists() ) {
			$oEntity->invalidateCache();
			return true;
		}
		return true;
	}
}
