
bs.social.EntityList.CreateDiscussion = function( $el, entityList ) {

	bs.social.El.call( this, $el );
	var me = this;
	me.BUTTON_SECTION = 'bs-socialtopics-discussion-create';
	me.entityList = entityList;
	me.data = {};
	me.makeUiID();

	me.$button = me.getEl().find( '.' + me.BUTTON_SECTION + ' a' ).first();
	if( !me.$button || me.$button.length < 1 ) {
		return;
	}

	me.$button.on( 'click', function( e ) {
		me.createDiscussion();
		e.stopPropagation();
		return false;
	});
	$(document).trigger( 'BSSocialEntityListCreateDiscussionInit', [ me, $el ] );

};
OO.initClass( bs.social.EntityList.CreateDiscussion );
OO.inheritClass( bs.social.EntityList.CreateDiscussion, bs.social.El );

bs.social.EntityList.CreateDiscussion.prototype.createDiscussion = function() {
	var dfd = $.Deferred();
	var taskData = this.makeTaskData();

	this.showLoadMask();
	var me = this;
	bs.api.tasks.execSilent( 'social', 'editEntity', taskData )
	.done( function( response ) {
		//ignore errors for now
		//me.replaceEL( response.payload.view );
		if( !response.success ) {
			if( response.message && response.message !== '' ) {
				OO.ui.alert( response.message );
			}
			dfd.resolve( me );
			return;
		}
		me.reloadPage();
		me.hideLoadMask();
		dfd.resolve( me );
	});

	return dfd;
};

bs.social.EntityList.CreateDiscussion.prototype.makeTaskData = function() {
	return [];
};

bs.social.EntityList.CreateDiscussion.prototype.reloadPage = function() {
	window.location = mw.util.getUrl(
		mw.config.get( 'wgPageName' )
	);
};
