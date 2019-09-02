<?php

namespace BlueSpice\Social\Topics\Hook\PageContentSaveComplete;

use BlueSpice\Hook\PageContentSaveComplete;

class AutoCreateDiscussionPage extends PageContentSaveComplete {

	protected function skipProcessing() {
		if ( !$this->getConfig()->get( 'SocialTopicsTalkPageAutoCreate' ) ) {
			return true;
		}
		if ( !$this->wikipage->getTitle() ) {
			return true;
		}
		if ( $this->wikipage->getTitle()->getNamespace() === NS_SOCIALENTITY ) {
			return true;
		}
		if ( $this->wikipage->getTitle()->isTalkPage() ) {
			return true;
		}
		if ( !$this->wikipage->getTitle()->exists() ) {
			return true;
		}
		if ( !$this->wikipage->getTitle()->getTalkPage() ) {
			return true;
		}
		if ( $this->wikipage->getTitle()->getTalkPage()->exists() ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$status = \BlueSpice\Social\Topics\Extension::createDiscussionPage(
			$this->wikipage->getTitle()->getTalkPage(),
			$this->user
		);
		return true;
	}
}
