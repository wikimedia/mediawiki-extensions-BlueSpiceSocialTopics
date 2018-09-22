
bs.social.EntityList.CreateDiscussionPage = function( $el, entityList ) {

	bs.social.El.call( this, $el );
	var me = this;
	me.BUTTON_SECTION = 'bs-socialtopics-discussionpage-create';
	me.entityList = entityList;
	me.data = {};
	me.makeUiID();

	me.$button = me.getEl().find( '.' + me.BUTTON_SECTION + ' a' ).first();
	if( !me.$button || me.$button.length < 1 ) {
		return;
	}
	me.$button.on( 'click', function( e ) {
		me.showCreateDiscussionPage();
		e.stopPropagation();
		return false;
	});
	$(document).trigger( 'BSSocialEntityListCreateDiscussionPageInit', [ me, $el ] );

};
OO.initClass( bs.social.EntityList.CreateDiscussionPage );
OO.inheritClass( bs.social.EntityList.CreateDiscussionPage, bs.social.El );

bs.social.EntityList.CreateDiscussionPage.prototype.showCreateDiscussionPage = function() {
	var factory = new OO.Factory();
	var windowManager = new OO.ui.WindowManager( {
		factory: factory
	} );
	factory.register( bs.social.EntityList.CreateDiscussionPage.Dialog );
	$( 'body' ).append( windowManager.$element );

	var nsText = bs.util.getNamespaceText(
		mw.config.get( 'wgNamespaceNumber', 0 ) +1
	);
	var nsFile = mw.config.get( 'wgNamespaceIds' )['file'];
	var msg = mw.config.get( 'wgNamespaceNumber', 0 ) === nsFile
		? "bs-socialtopics-autocreated-discussionpagefile"
		: "bs-socialtopics-autocreated-discussionpage";

	windowManager.openWindow( 'createDiscussion', {
		text: mw.message( msg ).plain(),
		title: nsText + ':' + mw.config.get( 'wgTitle' ),
		entityList: this.entityList
	} );
	return true;
};


