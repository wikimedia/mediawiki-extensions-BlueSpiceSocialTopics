<?php
namespace BlueSpice\Social\Topics\Hook\BSSocialTagsForceTags;

use BlueSpice\Social\Tags\Hook\BSSocialTagsForceTags;
use BlueSpice\Social\Topics\Entity\Topic;

class AddTopicTalkPageTag extends BSSocialTagsForceTags {
	protected function skipProcessing() {
		if( !$this->entity instanceof Topic ) {
			return true;
		}
		return parent::skipProcessing();
	}

	protected function doProcess() {
		$this->tags = array_values( array_unique( array_merge( $this->tags, [
			$this->entity->getRelatedTitle()->getOtherPage()->getFullText()
		])));
		error_log(var_export($this->tags,1));
		return true;
	}

}
