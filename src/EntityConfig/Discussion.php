<?php

/**
 * Discussion class for BSSocial
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
 * @filesource
 */
namespace BlueSpice\Social\Topics\EntityConfig;
use BlueSpice\Social\EntityConfig\Page;
use BlueSpice\Social\Data\Entity\Schema;
use BlueSpice\Data\FieldType;
use BlueSpice\Social\Topics\Entity\Discussion as Entity;

/**
 * Discussion class for BSSocial extension
 * @package BlueSpiceSocial
 * @subpackage BlueSpiceSocialTopics
 */
class Discussion extends Page {
	public function addGetterDefaults() {
		return array();
	}
	public function get_EntityClass() {
		return "\\BlueSpice\\Social\\Topics\\Entity\\Discussion";
	}

	protected function get_Renderer() {
		return 'socialentitydiscussion';
	}

	protected function get_EntityTemplateDefault() {
		return 'BlueSpiceSocialTopics.Entity.Discussion.Default';
	}
	protected function get_EntityTemplatePage() {
		return 'BlueSpiceSocialTopics.Entity.Discussion.Page';
	}
	protected function get_ModuleStyles() {
		return array_merge( parent::get_ModuleStyles(), [
			'ext.bluespice.social.topics.styles'
		]);
	}
	protected function get_ModuleScripts() {
		return array_merge( parent::get_ModuleScripts(), [
			'ext.bluespice.social.entity.discussion',
		]);
	}
	protected function get_CanHaveChildren() {
		return false;
	}
	protected function get_TypeMessageKey() {
		return 'bs-socialdiscussion-discussiontype';
	}
	protected function get_HeaderMessageKey() {
		return 'bs-socialtopics-entitydiscussion-header';
	}
	protected function get_HeaderWithTitleMessageKey() {
		return 'bs-social-entitydiscussion-withtitleheader';
	}
	protected function get_HeaderMessageKeyCreateNew() {
		return 'bs-socialtopics-entitydiscussion-header-create';
	}
	protected function get_IsTagable() {
		return false;
	}
	protected function get_IsEditable() {
		return false;
	}
	protected function get_IsDeleteable() {
		return false; //for now. TODO: make backgroud jobs for deleteing like
		//in BSSocialGroups
	}
	protected function get_AttributeDefinitions() {
		return array_merge(
			parent::get_AttributeDefinitions(),
			[
				Entity::ATTR_DISCUSSION_TITLE_ID => [
					Schema::FILTERABLE => true,
					Schema::SORTABLE => true,
					Schema::TYPE => FieldType::INT,
					Schema::INDEXABLE => true,
					Schema::STORABLE => true,
				],
			]
		);
	}
}