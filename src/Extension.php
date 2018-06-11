<?php
/**
 * BlueSpiceSocial base extension for BlueSpice
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

namespace BlueSpice\Social\Topics;

use BlueSpice\Social\Topics\Entity\Discussion;
use BlueSpice\Social\Topics\Content\Discussion as DiscussionContent;
use BlueSpice\Services;

class Extension extends \BlueSpice\Extension {

	public static function onRegistration() {
		global $wgContentHandlers, $wgNamespaceContentModels;

		if( !defined('CONTENT_MODEL_BSSOCIALDISCUSSION') ) {
			define( 'CONTENT_MODEL_BSSOCIALDISCUSSION', 'BSSocialDiscussion' );
			$wgContentHandlers[CONTENT_MODEL_BSSOCIALDISCUSSION]
				= "\\BlueSpice\\Social\\Topics\\Content\\DiscussionHandler";
			$wgNamespaceContentModels[NS_TALK] = CONTENT_MODEL_BSSOCIALDISCUSSION;
		}
	}

	/**
	 * @param \Title $oTitle
	 * @param \User $oUser
	 * @return type
	 */
	public static function createDiscussionPage( \Title $oTitle, \User $oUser = null ) {
		/*if( !$oUser instanceof User ) {
			$oUser = RequestContext::getMain()->getUser();
		}*/
		
		$oUser = Services::getInstance()->getBSUtilityFactory()
			->getMaintenanceUser()->getUser();
		if( !$oTitle->isTalkPage() || $oTitle->getNamespace() === NS_BSSOCIAL_TALK ) {
			return \Status::newFatal( wfMessage(
				'bs-socialtopics-entity-fatalstatus-save-notalkpage' //wrong msg
			));
		}
		if( $oTitle->exists() ) {
			return \Status::newGood( $oTitle );
		}
		$oWikiPage = \WikiPage::factory( $oTitle );
		$oRelatedTitle = \Title::makeTitle(
			$oTitle->getNamespace()-1,
			$oTitle->getText()
		);
		$sRelatedTitleFullText = $oRelatedTitle->getNamespace() === NS_FILE
			? ":File:{$oRelatedTitle->getText()}"
			: $oRelatedTitle->getFullText()
		;
		$oMsg = wfMessage(
			$oRelatedTitle->getNamespace() !== NS_FILE
				? 'bs-socialtopics-autocreated-discussionpage'
				: 'bs-socialtopics-autocreated-discussionpagefile'
		);
		$oMsg->params( [
			$oTitle->getFullText(),
			$sRelatedTitleFullText,
			$oRelatedTitle->getFullText(),
		]);

		try {
			$oStatus = $oWikiPage->doEditContent(
				new \WikitextContent( $oMsg->plain() ),
				"",
				0,
				0,
				$oUser,
				null
			);
		} catch( \Exception $e ) {
			return \Status::newFatal( $e->getMessage() );
		}
		if ( !$oStatus->isOK() ) {
			return $oStatus;
		}
		return \Status::newGood( $oWikiPage->getTitle() );
	}

	/**
	 *
	 * @param \Title $title
	 * @param \Content $model
	 * @return boolean
	 */
	public static function onContentHandlerDefaultModelFor( \Title $title, &$model ) {
		if( !$title->exists() || !$title->isTalkPage() ) {
			return true;
		}
		$classic = \RequestContext::getMain()->getRequest()->getBool(
			'classicdiscussion',
			false
		);
		if( $classic ) {
			return true;
		}
		$oEntity = Discussion::newFromDiscussionTitle(
			$title
		);
		if( !$oEntity ) {
			return true;
		}
		$model = CONTENT_MODEL_BSSOCIALDISCUSSION;
		return true;
	}

	/**
	 * This is so hacky i cant breathe ^^
	 * @param Article $oArticle
	 * @param boolean $outputDone
	 * @param boolen $useParserCache
	 */
	public static function onArticleViewHeader( &$oArticle, &$outputDone, &$useParserCache ) {
		$oTitle = $oArticle->getTitle();
		if( !$oTitle->exists() || !$oTitle->isTalkPage() ) {
			return true;
		}
		$classic = \RequestContext::getMain()->getRequest()->getBool(
			'classicdiscussion',
			false
		);
		if( $classic ) {
			return true;
		}
		$oEntity = Discussion::newFromDiscussionTitle(
			$oTitle
		);
		if( !$oEntity ) {
			return true;
		}
		$useParserCache = false;
		$oContentModel = new DiscussionContent(
			' ',
			CONTENT_MODEL_BSSOCIALGROUP
		);
		$outputDone = $oContentModel->getParserOutput(
			$oArticle->getTitle()
		);
		$oArticle->getContext()->getOutput()->addParserOutput($outputDone);
		return true;
	}
}