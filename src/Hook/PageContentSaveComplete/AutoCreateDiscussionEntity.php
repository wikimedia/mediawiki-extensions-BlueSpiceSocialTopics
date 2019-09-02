<?php

namespace BlueSpice\Social\Topics\Hook\PageContentSaveComplete;

use BlueSpice\Hook\PageContentSaveComplete;
use BlueSpice\Social\Topics\Entity\Discussion;

class AutoCreateDiscussionEntity extends PageContentSaveComplete {

	protected function skipProcessing() {
		if ( !$this->wikipage->getTitle() ) {
			return true;
		}
		if ( !$this->wikipage->getTitle()->isTalkPage() ) {
			return true;
		}
		if ( $this->wikipage->getTitle()->getNamespace() === NS_SOCIALENTITY_TALK ) {
			return true;
		}
		if ( !$this->wikipage->getTitle()->exists() ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$factory = $this->getServices()->getService(
			'BSSocialDiscussionEntityFactory'
		);

		$entity = $factory->newFromDiscussionTitle(
			$this->wikipage->getTitle()
		);
		if ( !$entity instanceof Discussion ) {
			return true;
		}
		if ( !$entity->exists() ) {
			$status = $entity->save();
		}
		$entity->invalidateCache();
		return true;
	}
}
