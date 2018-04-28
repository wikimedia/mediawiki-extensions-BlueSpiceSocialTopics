<?php

/**
 * BSSociaEntityDiscussion class for BSSocial
 *
 * add desc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceSocial
 * @subpackage BSSocialDiscussion
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */
namespace BlueSpice\Social\Topics\Entity;
use BlueSpice\Social\Entity\Page;
use BlueSpice\Social\Entities;
use BlueSpice\Social\Entity;

/**
 * BSSociaEntityDiscussion class for BSSocial extension
 * @package BlueSpiceSocial
 * @subpackage BSSocialDiscussion
 */
class Discussion extends Page {
	const TYPE = 'discussion';

	const ATTR_DISCUSSION_TITLE_ID = 'discussiontitleid';

	protected $sBaseTitleContent = null;

	/**
	 * @param \Title $oTitle
	 * @return Discussion | null
	 */
	public static function newFromDiscussionTitle( $oTitle ) {
		if( !$oTitle instanceof \Title || empty( $oTitle->getArticleID() ) ) {
			return null;
		}

		$oStatus = Entities::get(
			['limit' => 1],
			[
				static::ATTR_TYPE => static::TYPE,
				static::ATTR_DISCUSSION_TITLE_ID => $oTitle->getArticleID()
			],
			0,
			false
		);
		if( !$oStatus->isOK() ) {
			return null;
		}
		$aEntities = $oStatus->getValue();

		if( empty($aEntities[0]) ) {
			return Entity::newFromObject( (object) [
				static::ATTR_TYPE => static::TYPE,
				static::ATTR_DISCUSSION_TITLE_ID => $oTitle->getArticleID(),
			]);
		}
		return $aEntities[0];
	}

	public function getBaseTitleContent() {
		if( $this->sBaseTitleContent ) {
			return $this->sBaseTitleContent;
		}
		$this->sBaseTitleContent = '';

		if( !$this->getRelatedTitle()->exists() ) {
			return $this->sBaseTitleContent;
		}
		$oWikiPage = \WikiPage::factory( $this->getRelatedTitle() );
		try {
			$oOutput = $oWikiPage->getContent()->getParserOutput(
				$this->getRelatedTitle(),
				null,
				\ParserOptions::newFromContext( \RequestContext::getMain() ),
				true,
				true
			);
		} catch( \Exception $e ) {
			//sometimes parser recursion - unfortunately this can not be solved
			//due to the randomnes of the content model -.-
			$oOutput = null;
		}

		if( !$oOutput ) {
			return $this->sBaseTitleContent;
		}
		$this->sBaseTitleContent = $oOutput->getText();
		return $this->sBaseTitleContent;
	}

	/**
	 * Gets the BSSociaEntityPage attributes formated for the api
	 * @return object
	 */
	public function getFullData( $a = [] ) {
		return parent::getFullData( array_merge(
			$a,
			[
				static::ATTR_DISCUSSION_TITLE_ID => $this->get(
					static::ATTR_DISCUSSION_TITLE_ID,
					0
				),
			]
		));
	}

	/**
	 * Returns the discussiontitleid attribute
	 * @deprecated since version 3.0.0 - use get( $attrName, $default ) instead
	 * @return integer
	 */
	public function getDiscussionTitleID() {
		wfDeprecated( __METHOD__, '3.0.0' );
		return $this->get( static::ATTR_DISCUSSION_TITLE_ID, 0 );
	}

	/**
	 * Sets the discussiontitleid attribute
	 * @deprecated since version 3.0.0 - use set( $attrName, $value ) instead
	 * @param integer $iID
	 * @return integer
	 */
	public function setDiscussionTitleID( $iID ) {
		wfDeprecated( __METHOD__, '3.0.0' );
		return $this->set( static::ATTR_DISCUSSION_TITLE_ID, $iID );
	}

	public function setValuesByObject( \stdClass $o ) {
		if( !empty( $o->{static::ATTR_DISCUSSION_TITLE_ID} ) ) {
			$this->set(
				static::ATTR_DISCUSSION_TITLE_ID,
				$o->{static::ATTR_DISCUSSION_TITLE_ID}
			);
		}
		parent::setValuesByObject( $o );
	}

	public function getHeader( $oMsg = null ) {
		$oMsg = parent::getHeader( $oMsg );
		return $oMsg->params([
			$this->getRelatedTitle()->getText(),
			$this->getRelatedTitle()->getNamespace(),
			\BsNamespaceHelper::getNamespaceName(
				$this->getRelatedTitle()->getNamespace()
			),
			$this->getRelatedTitle()->getFullText(),
		]);
	}

	public function getRelatedTitle() {
		$oTitle = \Title::newFromID(
			$this->get( static::ATTR_DISCUSSION_TITLE_ID, 0 )
		);
		return $oTitle instanceof \Title ? $oTitle : parent::getRelatedTitle();
	}

	public function save( \User $oUser = null, $aOptions = array() ) {
		if( empty( $this->get( static::ATTR_DISCUSSION_TITLE_ID, 0 ) ) ) {
			return \Status::newFatal( wfMessage(
				'bs-social-entity-fatalstatus-save-emptyfield',
				$this->getVarMessage( static::ATTR_DISCUSSION_TITLE_ID )->plain()
			));
		}
		$oTitle = \Title::newFromID( $this->get( static::ATTR_DISCUSSION_TITLE_ID, 0 ) );
		if( !$oTitle|| !$oTitle->exists() || !$oTitle->isTalkPage() ) {
			return \Status::newFatal( wfMessage(
				'bs-socialtopics-entity-fatalstatus-save-notalkpage'
			));
		}
		return parent::save( $oUser, $aOptions );
	}
}