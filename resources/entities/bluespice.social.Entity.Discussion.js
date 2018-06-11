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

bs.social.EntityDiscussion.prototype.makeActionMenu = function() {
	bs.social.EntityDiscussion.super.prototype.makeActionMenu.apply( this );
	if( this.getData().outputtype !== 'Page' ) {
		return;
	}
	if( parseInt( this.data.get( 'discussiontitleid', 0 ) ) !== mw.config.get( 'wgArticleId' ) ) {
		return;
	}
	var $actions = this.getContainer( this.ACTIONS_CONTAINER ).find(
		'.bs-social-entity-actions-content'
	);
	$actions.find( 'a.bs-social-entity-classicdiscussion' ).html(
		mw.message( "bs-socialtopics-entityaction-classicdiscussion" ).plain()
	);
	$actions.find( 'a.bs-social-entity-classicdiscussion' ).click( function( e ) {
		e.preventDefault();
		window.location.href = mw.util.getUrl(
			mw.config.get( 'wgPageName' ),
			{'classicdiscussion': true }
		);
		return false;
	});
};

bs.social.EntityDiscussion.static.name = "\\BlueSpice\\Social\\Topics\\Entity\\Discussion";
bs.social.factory.register( bs.social.EntityDiscussion );