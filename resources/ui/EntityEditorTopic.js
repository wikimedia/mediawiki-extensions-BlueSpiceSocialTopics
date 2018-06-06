bs.social.EntityEditorTopic = function ( config, entity ) {
	bs.social.EntityEditorText.call( this, config, entity );
};
OO.initClass( bs.social.EntityEditorTopic );
OO.inheritClass( bs.social.EntityEditorTopic, bs.social.EntityEditorText );

bs.social.EntityEditorTopic.prototype.makeFields = function() {
	var fields = bs.social.EntityEditorTopic.super.prototype.makeFields.apply(
		this
	);

	this.topictitle = new OO.ui.TextInputWidget( {
		placeholder: this.getVarLabel( 'topictitle' ),
		autosize: false,
		multiline: false,
		value: this.getEntity().data.get( 'topictitle', '' )
	});
	fields.topictitle = this.topictitle;

	var discussiontitleid = 0, titleText = '', disabled = false;
	if( this.getEntity().exists() ) {
		discussiontitleid = this.getEntity().data.get(
			'discussiontitleid',
			0
		);
		titleText = this.getEntity().data.get(
			'relatedtitle',
			''
		);
		disabled = true;
	} else {
		var ns = mw.config.get( 'wgNamespaceNumber', 0 );
		if( ns > 0 && (ns%2) === 1 ) {
			disabled = true;
			discussiontitleid = mw.config.get( 'wgArticleId', 0 );
			titleText = mw.config.get( 'wgPageName', '' );
		}
	}
	var option = '', localData = [];
	if( discussiontitleid > 0 ) {
		option = '<option selected="selected" value="' + discussiontitleid + '">' + titleText + '</option>';
		localData.push({
			text: titleText,
			id: discussiontitleid
		});
	}
	//fake oojs item - use working js
	this.discussiontitleid = {
		select2: true,
		$element: $(
			'<div class="bs-social-field">'
				+ '<label>'
					/*+ this.getVarLabel( 'discussiontitleid' )*/
					+ '<select style="width:100%">'
						+ option
					+ '</select>'
				+ '</label>'
			+ '</div>'
		),
		setElementGroup: function(){}
	};
	var namespaces = mw.config.get('wgNamespaceIds');
	var talkns = [];
	for( var i in namespaces ) {
		if( namespaces[i] < 1 || namespaces[i]%2 === 0 ) {
			continue;
		}
		talkns.push( namespaces[i] );
	}
	this.discussiontitleid.$element.find( 'select' ).select2({
		data: localData,
		placeholder: this.getVarLabel( 'discussiontitleid' ),
		label: this.getVarLabel( 'discussiontitleid' ),
		allowClear: true,
		disabled: disabled,
		ajax: {
			url: mw.util.wikiScript( 'api' ),
			dataType: 'json',
			tape: 'POST',
			data: function (params) {
				return {
					action: 'bs-socialtitlequery-store',
					query: params.term,
					options: JSON.stringify({
						namespaces: talkns
					})
				};
			},
			processResults: function (data) {
				var results = [];
				$.each(data.results, function (index, result) {
					results.push({
						id: result.page_id,
						text: result.prefixedText
					});
				});
				return {
					results: results
				};
			}
		},
		initSelection: function(element, callback) {
			return callback( localData );
		},
		minimumInputLength: 1
	});
	fields.discussiontitleid = this.discussiontitleid;

	return fields;
};
bs.social.EntityEditorTopic.prototype.addContentFieldsetItems = function() {
	this.contentfieldset.addItems( [
		new OO.ui.FieldLayout( this.topictitle, {
			label: this.getVarLabel( 'topictitle' ),
			align: 'top'
		}),
		new OO.ui.FieldLayout( this.text, {
			label: this.getVarLabel( 'text' ),
			align: 'top'
		}),
		this.discussiontitleid
	]);
	bs.social.EntityEditorTopic.super.prototype.addContentFieldsetItems.apply(
		this
	);
};