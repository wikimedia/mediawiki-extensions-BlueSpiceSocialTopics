<?php

namespace BlueSpice\Social\Topics\Hook\PageContentSaveComplete;
use BlueSpice\Hook\PageContentSaveComplete;

class AutoCreateDiscussionPage extends PageContentSaveComplete {

	protected function checkTitle() {
		if( !$this->wikipage->getTitle() ) {
			return false;
		}
		if( $this->wikipage->getTitle()->getNamespace() === NS_SOCIAL_ENTITY ) {
			return false;
		}
		if( $this->wikipage->getTitle()->isTalkPage() ) {
			return false;
		}
		if( !$this->wikipage->getTitle()->exists() ) {
			return false;
		}
		if( !$this->wikipage->getTitle()->getTalkPage() ) {
			return false;
		}
		if( $this->wikipage->getTitle()->getTalkPage()->getNamespace() === NS_SOCIAL_ENTITY_TALK ) {
			return false;
		}
		if( $this->wikipage->getTitle()->getTalkPage()->exists() ) {
			return false;
		}
		return true;
	}

	protected function doProcess() {
		if( !$this->getConfig()->get( 'BSSocialenTalkpageAutoCreate' ) ) {
			return true;
		}
		if( !$this->checkTitle() ) {
			return true;
		}
		$oTMPStatus = \BlueSpice\Social\Topics\Extension::createDiscussionPage(
			$this->wikipage->getTitle()->getTalkPage(),
			$this->user
		);
		return true;
	}
}
