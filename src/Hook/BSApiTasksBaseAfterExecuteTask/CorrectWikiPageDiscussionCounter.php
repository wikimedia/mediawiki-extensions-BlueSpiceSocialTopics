<?php

namespace BlueSpice\Social\Topics\Hook\BSApiTasksBaseAfterExecuteTask;
use BlueSpice\Hook\BSApiTasksBaseAfterExecuteTask;
use BlueSpice\Social\Topics\Entity\Discussion;
use BlueSpice\Social\Entities;

class CorrectWikiPageDiscussionCounter extends BSApiTasksBaseAfterExecuteTask {
	protected function makeTitle() {
		$oTitle = null;
		if ( isset( $this->taskData->page_id ) ) {
			$oTitle = \Title::newFromID( $this->taskData->page_id );
		}
		if ( $oTitle instanceof \Title === false && isset( $this->taskData->page_title ) ) {
			$oTitle = \Title::newFromText( $this->taskData->page_title );
		}
		if ( $oTitle instanceof \Title === false ) {
			$oTitle = $this->taskApi->getTitle();
		}
		if ( !$oTitle ) {
			return $oTitle;
		}
		if( !$oTitle->isTalkPage() ) {
			$oTitle = $oTitle->getTalkPage();
		}
		return $oTitle;
	}

	protected function doProcess() {
		if( !$this->taskApi instanceof \BSApiWikiPageTasks ) {
			return true;
		}
		if( !$this->taskKey == 'getDiscussionCount' ) {
			return true;
		}
		$oTitle = $this->makeTitle();

		if ( !$oTitle instanceof \Title ) {
			throw new MWException(
				wfMessage( 'bs-wikipage-tasks-error-page-not-valid' )->plain()
			);
		}
		if( !$oTitle->exists() ) {
			return true;
		}
		$oEntity = Discussion::newFromDiscussionTitle( $oTitle );
		if( !$oEntity || !$oEntity->exists() ) {
			return true;
		}
		$oStatus = Entities::get(
			[ 'limit' => 0 ],
			[
				'type' => ['topic'],
				'discussiontitleid' => $oTitle->getArticleID(),
			]
		);
		if( !$oStatus->isOK() ) {
			throw new \MWException(
				$oStatus->getHTML()
			);
		}
		$this->result->payload = 0;
		foreach( $oStatus->getValue() as $oEntity ) {
			$this->result->payload += 1;
		}
		return true;
	}
}