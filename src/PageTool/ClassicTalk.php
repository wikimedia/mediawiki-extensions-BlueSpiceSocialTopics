<?php

namespace BlueSpice\Social\Topics\PageTool;

use BlueSpice\PageTool\IconBase;


class ClassicTalk extends IconBase {

	protected function getIconClass() {
		return 'bs-icon-swap';
	}

	protected function getToolTip() {
		return new \Message( 'bs-socialtopics-switch-classsicdiscussion-tooltip' );
	}

	protected function getUrl() {
		$url = $this->getTitle()->getLocalURL( [ 'classicdiscussion' => 'true' ] );
		return $url;
	}

	protected function skipProcessing() {
		$isTalk = $this->getTitle()->isTalkPage();
		$isClassic = $this->context->getRequest()->getBool( 'classicdiscussion' ) !== false;
		return !( $isTalk && !$isClassic );
	}
}