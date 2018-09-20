
bs.social.EntityList.CreateDiscussion.Dialog = function( config ) {
	var me = this;
	me.data = null;
	bs.social.EntityList.CreateDiscussion.Dialog.super.call( this, config );
};

OO.inheritClass( bs.social.EntityList.CreateDiscussion.Dialog, OO.ui.ProcessDialog );
OO.initClass( bs.social.EntityList.CreateDiscussion.Dialog );

bs.social.EntityList.CreateDiscussion.Dialog.static.name = 'createDiscussion';
bs.social.EntityList.CreateDiscussion.Dialog.static.title = mw.message(
	'bs-socialtopics-dialog-creatediscussion-title'
).plain();

bs.social.EntityList.CreateDiscussion.Dialog.static.actions = [ {
	action: 'save',
	label: mw.message( 'bs-socialtopics-dialog-creatediscussion-btn-label-save' ).plain(),
	flags: [ 'primary', 'constructive' ],
	disabled: false
}, {
	action: 'cancel',
	label: mw.message( 'bs-socialtopics-dialog-creatediscussion-btn-label-cancel' ).plain(),
	flags: 'safe'
} ];

bs.social.EntityList.CreateDiscussion.Dialog.static.data = {};

bs.social.EntityList.CreateDiscussion.Dialog.prototype.setup = function( cfg ) {
	this.data = cfg;
	this.textField.setValue( this.data['text'] );
	return bs.social.EntityList.CreateDiscussion.Dialog.super.prototype.setup.apply(
		this,
		arguments
	);
};

bs.social.EntityList.CreateDiscussion.Dialog.prototype.initialize = function() {
	bs.social.EntityList.CreateDiscussion.Dialog.super.prototype.initialize.call(
		this
	)

	this.panel = new OO.ui.PanelLayout( {
		padded: true,
		expanded: false,
		id: 'bs-socialtopics-creatediscussion-dialog'
	});
	this.content = new OO.ui.FieldsetLayout();
	this.errorSection = new OO.ui.Layout();
	this.errorSection.$element.css( 'color', 'red' );
	this.errorSection.$element.css( 'font-weight', 'bold' );
	this.errorSection.$element.css( 'text-align', 'center' );

	this.textField = this.makeTextInput();

	this.content.addItems( [
		this.errorSection,
		new OO.ui.FieldLayout( this.textField, {
			label: mw.message( 'bs-socialtopics-dialog-creatediscussion-label-text' ).plain(),
			align: 'top'
		} )
	] );

	this.panel.$element.append( this.content.$element );
	this.$body.append( this.panel.$element );
};

bs.social.EntityList.CreateDiscussion.Dialog.prototype.makeTextInput = function() {
	return new OO.ui.MultilineTextInputWidget( {
		value: '',
		required: true,
		disabled: false
	});
}

bs.social.EntityList.CreateDiscussion.Dialog.prototype.save = function() {
	var api = new mw.Api();
	return api.postWithToken( 'csrf', {
		action: 'edit',
		format: 'json',
		title: '',//this.data.title,
		text: this.textField.getValue()
	});
};

bs.social.EntityList.CreateDiscussion.Dialog.prototype.getActionProcess = function ( action ) {
	return bs.social.EntityList.CreateDiscussion.Dialog.super.prototype.getActionProcess.call( this, action )
	.next( function () {
		return 1000;
	}, this )
	.next( function () {
		var closing;
		if ( action === 'save' ) {
			if ( this.broken ) {
				this.broken = false;
				return new OO.ui.Error( 'Server did not respond' );
			}
			var me = this;
			var dfd = $.Deffered();
			me.save().fail( function( data ){
				console.log('here');
				if( data.error && data.error.length > 0 ) {
					dfd.reject( new OO.ui.Error( data.error ) );
					//me.showRequestErrors( [data.error] );
					//return;
				}
			} ).done( function( data ) {
				console.log('there');
				if( data.error && data.error.length > 0 ) {
					return new OO.ui.Error( data.error );
					//me.showRequestErrors( [data.error] );
					//return;
				}
				closing = me.close( { action: action } );
				me.reloadPage();
				return closing;				
			});
			return dfd.promise();
		} else if ( action === 'cancel' ) {
			closing = this.close( { action: action } );
			return closing;
		}

		return bs.social.EntityList.CreateDiscussion.Dialog.super.prototype.getActionProcess.call(
			this,
			action
		);
	}, this );
};

bs.social.EntityList.CreateDiscussion.Dialog.prototype.showRequestErrors = function( errors ) {
	var errors = errors || {};

	var error = '';
	for( var i in errors ) {
		error += errors[i] + "<br />";
	}

	this.errorSection.$element.html( error );
};

bs.social.EntityList.CreateDiscussion.Dialog.prototype.reloadPage = function() {
	window.location = mw.util.getUrl(
		mw.config.get( 'wgPageName' )
	);
};