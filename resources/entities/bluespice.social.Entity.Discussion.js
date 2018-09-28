/**
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BluespiceSocial
 * @subpackage BSSocialGroups
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */

bs.social.EntityDiscussion = function( $el, type, data ) {
	bs.social.Entity.call( this, $el, type, data );
};
OO.initClass( bs.social.EntityDiscussion );
OO.inheritClass( bs.social.EntityDiscussion, bs.social.Entity );

bs.social.EntityDiscussion.static.name = "\\BlueSpice\\Social\\Topics\\Entity\\Discussion";
bs.social.factory.register( bs.social.EntityDiscussion );