<?php

namespace BlueSpice\Social\Topics\PageTool;

use BlueSpice\PageTool\IconBase;


class ModernTalk extends IconBase {

	protected function getIconClass() {
		return 'bs-icon-swap';
	}

	protected function getToolTip() {
		return new \Message( 'bs-socialtopics-switch-moderndiscussion-tooltip' );
	}

	protected function getUrl() {
		$url = $this->getTitle()->getLocalURL();
		return $url;
	}

	protected function skipProcessing() {
		$isTalk = $this->getTitle()->isTalkPage();
		$isClassic = $this->context->getRequest()->getBool( 'classicdiscussion' ) !== false;

		return !( $isTalk && $isClassic );
	}
}
