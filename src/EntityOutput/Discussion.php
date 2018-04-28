<?php
/**
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceSocial
 * @subpackage BSSocialDiscussion
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */
namespace BlueSpice\Social\Topics\EntityOutput;
use BlueSpice\Social\EntityOutput;
use BlueSpice\Social\Entities;
use BlueSpice\Social\Entity;

/**
 * This view renders the a single item.
 * @package BlueSpiceSocial
 * @subpackage BSSocialDiscussion
 */
class Discussion extends EntityOutput {
	/**
	 * Constructor
	 */
	public function __construct( Entity $oEntity ) {
		parent::__construct( $oEntity );
		#$this->aArgs['basetitlecontent'] = $oEntity->getBaseTitleContent();
		$this->aArgs['basetitlecontent'] = '';
	}

	protected function render_children( $mVal, $sType = 'Default' ) {
		if( $sType !== 'Page' ) {
			return '';//parent::render_children( $mVal, $sType );
		}

		if( !$this->getEntity()->exists() ) {
			return '';
		}

		$sOut = Entities::makeList(
			[],
			[
				'type' => ['topic'],
				'discussiontitleid' => $this->getEntity()->getRelatedTitle()->getArticleID(),
			],
			0,
			[],
			$this->getEntity()
		);
		return $sOut;
	}

	public function render_basetitlecontent( $mVal, $sType = 'Default' ) {
		if( $sType != 'Page' ) {
			return '';
		}

		return $this->oEntity->getBaseTitleContent();
	}
}