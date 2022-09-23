<?php
namespace BlueSpice\Social\Topics\Hook\BSSocialTagsBeforeSetTags;

use BlueSpice\Social\Tags\Hook\BSSocialTagsBeforeSetTags;
use BlueSpice\Social\Topics\Entity\Topic;
use MediaWiki\MediaWikiServices;

class AddTopicTalkPageTag extends BSSocialTagsBeforeSetTags {
	protected function skipProcessing() {
		if ( !$this->entity instanceof Topic ) {
			return true;
		}
		if ( !$this->entity->getRelatedTitle()->exists() ) {
			return true;
		}

		return parent::skipProcessing();
	}

	protected function doProcess() {
		$services = MediaWikiServices::getInstance();
		$associatedPage = $services->getNamespaceInfo()
			->getAssociatedPage( $this->entity->getRelatedTitle() );
		$fullText = $services->getTitleFormatter()->getFullText( $associatedPage );
		$this->tags = array_values( array_unique( array_merge(
			$this->tags,
			[ $fullText ]
		) ) );
		return true;
	}

}
