<?php
/**
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceSocial
 * @subpackage BSSocialTopic
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */
namespace BlueSpice\Social\Topics\EntityOutput;
use BlueSpice\Social\EntityOutput\Text;
use BlueSpice\Social\Entity;

/**
 * This view renders the a single item.
 * @package BlueSpiceSocial
 * @subpackage BSSocialTopic
 */
class Topic extends Text{
	/**
	 * Constructor
	 */
	public function __construct( Entity $oEntity ) {
		parent::__construct( $oEntity );
	}

	protected function render_beforecontent( $mVal, $sType = 'Default' ) {
		$sOut = parent::render_beforecontent( $mVal, $sType );
		//TODO: get real discussion entity!
		$sOut .= \Html::openElement( 'span', [
			'class' => 'bs-social-beforecontent-topic',
		]);
		$sOut .= \Html::element(
			'a',
			['href' => $this->getEntity()->getRelatedTitle()->getFullURL()],
			$this->getEntity()->getRelatedTitle()->getFullText()
		);
		$sOut .= \Html::closeElement( 'span' );
		return $sOut;
	}
}