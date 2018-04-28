<?php

/**
 * Topic class for BSSocial
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
 * @subpackage BlueSpiceSocialTopics
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */
namespace BlueSpice\Social\Topics\Entity;
use BlueSpice\Social\Entity\Text;
use BlueSpice\Social\Parser\Input;
use BlueSpice\Social\Topics\Entity\Discussion;

/**
 * Topic class for BSSocial extension
 * @package BlueSpiceSocial
 * @subpackage BSSocialMicroBlog
 */
class Topic extends Text {
	const TYPE = 'topic';

	const ATTR_DISCUSSION_TITLE_ID = 'discussiontitleid';
	const ATTR_TOPIC_TITLE = 'topictitle';

	protected $iDiscussionTitleID = 0;
	protected $sTopicTitle = '';

	/**
	 * Gets the attributes formated for the api
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
				static::ATTR_TOPIC_TITLE => $this->get(
					static::ATTR_TOPIC_TITLE,
					''
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

	public function getTopicTitle() {
		return $this->sTopicTitle;
	}

	public function setTopicTitle( $sTopicTitle ) {
		$this->sTopicTitle = $sTopicTitle;
		return $this;
	}

	public function setValuesByObject( \stdClass $o ) {
		if( !empty( $o->{static::ATTR_DISCUSSION_TITLE_ID} ) ) {
			$this->set(
				static::ATTR_DISCUSSION_TITLE_ID,
				$o->{static::ATTR_DISCUSSION_TITLE_ID}
			);
		}
		if( !empty( $o->{static::ATTR_TOPIC_TITLE} ) ) {
			$this->set(
				static::ATTR_TOPIC_TITLE,
				$o->{static::ATTR_TOPIC_TITLE}
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
			$this->get( static::ATTR_TOPIC_TITLE, '' ),
			$this->getRelatedTitle()->getFullText(),
			$this->getRelatedContentTitle()->getFullText(),
		]);
	}

	public function getRelatedTitle() {
		$oTitle = \Title::newFromID(
			$this->get( static::ATTR_DISCUSSION_TITLE_ID, 0 )
		);
		return $oTitle instanceof \Title ? $oTitle : parent::getRelatedTitle();
	}

	public function getRelatedContentTitle() {
		$oTitle = $this->getRelatedTitle();
		$oContentTitle = \Title::newFromText(
			$oTitle->getText(),
			$oTitle->getNamespace()-1
		);
		return $oContentTitle;
	}

	public function save( \User $oUser = null, $aOptions = array() ) {
		$oParser = new Input();
		$this->set( static::ATTR_TOPIC_TITLE,
			$oParser->parse( $this->get( static::ATTR_TOPIC_TITLE, '' ) )
		);
		if( empty( $this->get( static::ATTR_TOPIC_TITLE, '' ) ) ) {
			return \Status::newFatal( wfMessage(
				'bs-social-entity-fatalstatus-save-emptyfield',
				$this->getVarMessage( static::ATTR_TOPIC_TITLE )->plain()
			));
		}
		if( empty( $this->get( static::ATTR_DISCUSSION_TITLE_ID, 0 ) ) ) {
			return \Status::newFatal( wfMessage(
				'bs-social-entity-fatalstatus-save-emptyfield',
				$this->getVarMessage( static::ATTR_DISCUSSION_TITLE_ID )->plain()
			));
		}
		$oTitle = \Title::newFromID(
			$this->get( static::ATTR_DISCUSSION_TITLE_ID, 0 )
		);
		if( !$oTitle|| !$oTitle->exists() || !$oTitle->isTalkPage() ) {
			return \Status::newFatal( wfMessage(
				'bs-socialtopics-entity-fatalstatus-save-notalkpage'
			));
		}
		return parent::save( $oUser, $aOptions );
	}

	public function invalidateCache() {
		parent::invalidateCache();
		$oEntity = Discussion::newFromTitle(
			$this->getRelatedTitle()
		);
		if( $oEntity && $oEntity->exists() ) {
			$oEntity->invalidateCache();
		}
	}

}