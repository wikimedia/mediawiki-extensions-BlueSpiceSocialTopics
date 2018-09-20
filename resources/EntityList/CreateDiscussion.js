
bs.social.EntityList.CreateDiscussion = function( $el, entityList ) {

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
		me.showCreateDiscussion();
		e.stopPropagation();
		return false;
	});
	$(document).trigger( 'BSSocialEntityListCreateDiscussionInit', [ me, $el ] );

};
OO.initClass( bs.social.EntityList.CreateDiscussion );
OO.inheritClass( bs.social.EntityList.CreateDiscussion, bs.social.El );

bs.social.EntityList.CreateDiscussion.prototype.showCreateDiscussion = function() {
	var factory = new OO.Factory();
	var windowManager = new OO.ui.WindowManager( {
		factory: factory
	} );
	factory.register( bs.social.EntityList.CreateDiscussion.Dialog );
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


