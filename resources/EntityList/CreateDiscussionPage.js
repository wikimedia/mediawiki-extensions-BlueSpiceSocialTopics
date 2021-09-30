
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
	var skip = mw.user.options.get( 'bs-social-topics-skipcreatedialog', false );
	me.$button.on( 'click', function( e ) {
		if ( me.entityList.getData().usercanedit !== true ) {
			console.log('no edit');
			me.createDiscussionPageWithoutEdit();
			e.stopPropagation();
			return false;
		}
		!skip ? me.showCreateDiscussionPage() : me.createDiscussionPage();
		e.stopPropagation();
		return false;
	});
	$(document).trigger( 'BSSocialEntityListCreateDiscussionPageInit', [ me, $el ] );

};
OO.initClass( bs.social.EntityList.CreateDiscussionPage );
OO.inheritClass( bs.social.EntityList.CreateDiscussionPage, bs.social.El );

bs.social.EntityList.CreateDiscussionPage.prototype.showCreateDiscussionPage = function() {
	var windowManager = OO.ui.getWindowManager();

	var cfg = {
		text: this.getText(),
		title: this.getTitle()
	};

	var dialog = new bs.social.EntityList.CreateDiscussionPage.Dialog( cfg, this );

	windowManager.addWindows( [ dialog ] );
	windowManager.openWindow( dialog );
};

bs.social.EntityList.CreateDiscussionPage.prototype.createDiscussionPage = function() {
	this.entityList.showLoadMask();
	this.save( this.getTitle(), this.getText() )
		.done( function( title ) {
			this.loadDiscussionPage( title );
		}.bind( this ) );
};

bs.social.EntityList.CreateDiscussionPage.prototype.createDiscussionPageWithoutEdit = function() {
	this.entityList.showLoadMask();
	var me = this;
	var taskData = {
		page: this.getTitle()
	};
	bs.api.tasks.execSilent( 'socialtopics', 'createPage', taskData )
		.done( function( response ) {
		me.loadDiscussionPage( me.getTitle() );
	} );
};

bs.social.EntityList.CreateDiscussionPage.prototype.getText = function() {
	var nsFile = mw.config.get( 'wgNamespaceIds' )['file'];
	var msg = mw.config.get( 'wgNamespaceNumber', 0 ) === nsFile
		? "bs-socialtopics-autocreated-discussionpagefile"
		: "bs-socialtopics-autocreated-discussionpage";
	return mw.message( msg ).plain();
};

bs.social.EntityList.CreateDiscussionPage.prototype.getTitle = function() {
	var nsText = bs.util.getNamespaceText(
		mw.config.get( 'wgNamespaceNumber', 0 ) +1
	);
	return nsText + ':' + mw.config.get( 'wgTitle' );
};

bs.social.EntityList.CreateDiscussionPage.prototype.save = function( title, text ) {
	var api = new mw.Api(),
		dfd = $.Deferred();

	api.postWithToken( 'csrf', {
		action: 'edit',
		format: 'json',
		title: title,
		text: text
	} ).done( function( response ) {
		if (
			response.hasOwnProperty( 'edit' ) &&
			response.edit.hasOwnProperty( 'result' ) &&
			response.edit.result === 'Success'
		) {
			dfd.resolve( title );
		}
		dfd.reject();
	} ).fail( function( code, response ) {
		if ( response.hasOwnProperty( 'error' ) ) {
			dfd.reject( response.error.info || response.error.code );
		}
		dfd.reject( code );
	} );

	return dfd.promise();
};

bs.social.EntityList.CreateDiscussionPage.prototype.loadDiscussionPage = function( title ) {
	window.location = mw.util.getUrl( title );
};
