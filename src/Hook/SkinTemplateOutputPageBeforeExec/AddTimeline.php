<?php
/**
 * Hook handler base class for MediaWiki hook SkinTemplateOutputPageBeforeExec
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
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
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Social\Topics\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\Context;
use BlueSpice\Renderer\Params;
use BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;
use BlueSpice\Social\Topics\EntityListContext\AfterContent;

class AddTimeline extends SkinTemplateOutputPageBeforeExec {

	protected function skipProcessing() {
		if( !$this->skin->getTitle()->exists() ) {
			return true;
		}
		if( !$this->getConfig()->get( 'SocialTopicsTimelineAfterContentShow' ) ) {
			return true;
		}
		$namespace = $this->skin->getTitle()->getNamespace();
		$nsBlackList = $this->getConfig()->get(
			'SocialTopicsTimelineAfterContentNamespaceBlackList'
		);

		if( in_array( $namespace, $nsBlackList ) ) {
			return true;
		}

		if( $this->skin->getTitle()->isTalkPage() ) {
			return true;
		}

		$action = $this->getContext()->getRequest()->getVal( 'action', 'view' );
		if( $action != 'view' && $action != 'submit' ) {
			return true;
		}

		$prop = $this->getServices()->getBSUtilityFactory()
			->getPagePropHelper( $this->skin->getTitle() )
			->getPageProp( 'bs_nodiscussion' );
		if( !is_null( $prop ) ) {
			return true;
		}

		return false;
	}

	protected function doProcess() {
		$renderer = $this->getTimeLineRenderer();

		$this->mergeSkinDataArray(
			\BlueSpice\SkinData::AFTER_CONTENT,
			[ 'socialtopics' => $renderer->render() ]
		);
		return true;
	}

	protected function getContext() {
		$factory = $this->getServices()->getService(
			'BSSocialDiscussionEntityFactory'
		);
		$entity = $factory->newFromDiscussionTitle(
			$this->skin->getTitle()->getTalkPage()
		);
		return new AfterContent(
			new Context(
				parent::getContext(),
				$this->getConfig()
			),
			$this->getConfig(),
			parent::getContext()->getUser(),
			$entity,
			$this->skin->getTitle()
		);
	}

	protected function getTimeLineRenderer() {
		return $this->getServices()->getBSRendererFactory()->get(
			$this->getContext()->getRendererName(),
			new Params( [ 'context' => $this->getContext() ] )
		);
	}

}