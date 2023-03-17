<?php

namespace BlueSpice\Social\Topics\Hook\PageSaveComplete;

use BlueSpice\Hook\PageSaveComplete;
use Title;

class AutoCreateDiscussionPage extends PageSaveComplete {

	protected function skipProcessing() {
		if ( !$this->getConfig()->get( 'SocialTopicsTalkPageAutoCreate' ) ) {
			return true;
		}
		if ( !$this->wikiPage->getTitle() ) {
			return true;
		}
		if ( $this->wikiPage->getTitle()->getNamespace() === NS_SOCIALENTITY ) {
			return true;
		}
		if ( $this->wikiPage->getTitle()->isTalkPage() ) {
			return true;
		}
		if ( !$this->wikiPage->getTitle()->exists() ) {
			return true;
		}
		$talkPageTarget = $this->getServices()->getNamespaceInfo()
			->getTalkPage( $this->wikiPage->getTitle() );
		$talkPage = Title::newFromLinkTarget( $talkPageTarget );
		if ( !$talkPage ) {
			return true;
		}
		if ( $talkPage->exists() ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$talkPageTarget = $this->getServices()->getNamespaceInfo()
			->getTalkPage( $this->wikiPage->getTitle() );
		$talkPage = Title::newFromLinkTarget( $talkPageTarget );
		$status = \BlueSpice\Social\Topics\Extension::createDiscussionPage(
			$talkPage,
			$this->user
		);
		return true;
	}
}
