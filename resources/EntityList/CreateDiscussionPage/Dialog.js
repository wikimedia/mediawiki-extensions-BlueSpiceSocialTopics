
bs.social.EntityList.CreateDiscussionPage.Dialog = function( data, sender ) {
	this.sender = sender;
	this.pageTitle = data.title;
	this.text = data.text;

	bs.social.EntityList.CreateDiscussionPage.Dialog.super.call( this, {} );
};

OO.inheritClass( bs.social.EntityList.CreateDiscussionPage.Dialog, OO.ui.ProcessDialog );

bs.social.EntityList.CreateDiscussionPage.Dialog.static.name = 'createDiscussion';
bs.social.EntityList.CreateDiscussionPage.Dialog.static.title = '';

bs.social.EntityList.CreateDiscussionPage.Dialog.static.actions = [ {
	action: 'save',
	label: mw.message( 'bs-socialtopics-dialog-creatediscussionpage-btn-label-save' ).plain(),
	flags: [ 'primary', 'constructive' ],
	disabled: false
}, {
	label: mw.message( 'bs-socialtopics-dialog-creatediscussionpage-btn-label-cancel' ).plain(),
	flags: 'safe'
} ];

bs.social.EntityList.CreateDiscussionPage.Dialog.prototype.initialize = function() {
	bs.social.EntityList.CreateDiscussionPage.Dialog.super.prototype.initialize.call(
		this
	);

	this.panel = new OO.ui.PanelLayout( {
		padded: true,
		expanded: false,
		id: 'bs-socialtopics-creatediscussionpage-dialog'
	});


	this.textField = this.makeTextInput();
	this.textField.connect( this, {
		change: function() {
			this.textField.getValidity()
				.done( function() {
					this.actions.setAbilities( { save: true, cancel: true } );
				}.bind( this ) )
				.fail( function() {
					this.actions.setAbilities( { save: false, cancel: true } );
				}.bind( this ) );
		}
	} );
	this.textFieldLayout = new OO.ui.FieldLayout( this.textField, {
		label: mw.message( 'bs-socialtopics-dialog-creatediscussionpage-label-text' ).plain(),
		align: 'top'
	} );

	this.content = new OO.ui.FieldsetLayout( { items: [ this.textFieldLayout ] } );

	this.panel.$element.append( this.content.$element );
	this.$body.append( this.panel.$element );
};

bs.social.EntityList.CreateDiscussionPage.Dialog.prototype.makeTextInput = function() {
	return new OO.ui.MultilineTextInputWidget( {
		value: this.text,
		required: true,
		disabled: false
	} );
};

bs.social.EntityList.CreateDiscussionPage.Dialog.prototype.getBodyHeight = function() {
	return this.content.$element.outerHeight() + 30;
};

bs.social.EntityList.CreateDiscussionPage.Dialog.prototype.getActionProcess = function ( action ) {
	if ( action === 'save' ) {
		 return new OO.ui.Process( function () {
			this.pushPending();
			this.sender.save( this.pageTitle, this.textField.getValue() )
				 .done( function( title ) {
				 	this.sender.loadDiscussionPage( title );
				}.bind( this ) )
				.fail( function( error ) {
					this.textFieldLayout.setErrors( [ error ] );
					this.updateSize();
					this.popPending();
				}.bind( this ) );
		}.bind( this ) );
	}

	return bs.social.EntityList.CreateDiscussionPage.Dialog.super.prototype.getActionProcess.call(
		this,
		action
	);
};
