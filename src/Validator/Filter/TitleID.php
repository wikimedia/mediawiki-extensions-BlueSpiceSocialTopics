<?php

/**
 * TitleID class for BSSocial
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
 * @subpackage BSSocial
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */
namespace BlueSpice\Social\Topics\Validator\Filter;
use BlueSpice\Social\Validator\Filter;

/**
 * TitleID class for BSSocial extension
 * @package BlueSpiceSocial
 * @subpackage BSSocial
 */
abstract class TitleID extends Filter {
	const KEY = 'discussiontitleid';
	const NEEDS_DEFAULT = false;
	const TYPE = 'integer';
	const MULTIVALUE = true;
	const DEFINEDVALUES = false;

	public static function validate( &$aOptions, &$aLastErrors ) {
		if( is_numeric($aOptions[self::KEY]) ) {
			$aOptions[self::KEY] = [ (int)$aOptions[self::KEY] ];
		}
		if( is_string($aOptions[self::KEY]) ) {
			$aOptions[self::KEY] = explode(',', $aOptions[self::KEY]);
		}
		$aOptions[self::KEY] = \BsCore::sanitizeArrayEntry(
			$aOptions,
			self::KEY,
			self::getDefault(),
			\BsPARAMTYPE::ARRAY_INT
		);
	}
	public static function getDefault() {
		return [];
	}
}