<?php

namespace BlueSpice\Social\Topics\Renderer\Entity;

use Html;

class Topic extends \BlueSpice\Social\Renderer\Entity\Text{

	protected function render_beforecontent( $val ) {
		$sOut = parent::render_beforecontent( $val );
		//TODO: get real discussion entity!
		$sOut .= Html::openElement( 'span', [
			'class' => 'bs-social-beforecontent-topic',
		]);
		$sOut .= Html::element(
			'a',
			['href' => $this->getEntity()->getRelatedTitle()->getFullURL()],
			$this->getEntity()->getRelatedTitle()->getFullText()
		);
		$sOut .= Html::closeElement( 'span' );
		return $sOut;
	}
}