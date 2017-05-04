/**
 * Module containing a class to create a search box in the toolbar.
 */
define([
	'dojo/_base/declare',
	'dijit/form/ComboBox',
	'dojo/store/JsonRest',
	'dojo/when',
	"dojo/keys",
	'cool/rfe/util/stringUtil'
], function(declare, ComboBox, JsonRest, when, keys, stringUtil) {

	return declare([ComboBox], {
		value: '',
		searchAttr: 'query',
		queryExpr: "${0}",
		pageSize: 4,
		searchDelay: 100,
		highlightMatch: 'first',
		ignoreCase: true,
		autoComplete: false,
		//selectOnClick: true,
		store: null,
		rfe: null,
		target: '',
		labelType: 'text',
		hasDownArrow : false,

		constructor: function(args) {
			this.store = new JsonRest({
				target: args.target
			});
		},

		postMixInProperties: function() {
			this.inherited('postMixInProperties', arguments);
			this.baseClass = this.get('baseClass') + ' rfeSearchBox';
		},

		postCreate: function() {
			this.inherited('postCreate', arguments);

			var t = this;
			var rfe = this.rfe;

			this.on('change', function() {

			});

			this.on('input', function(evt) {
				t.query.filePath = rfe.currentTreeObject.id;
				t.query.selectionStart = t.textbox.selectionStart;
				t.query.selectionEnd = t.textbox.selectionEnd;
			});

			this.on('keydown', function(evt) {
				switch(evt.keyCode) {
					case keys.ENTER : {
						evt.preventDefault();
						evt.stopPropagation();
						rfe.search( t.get('value') );
						break;
					}
					case keys.RIGHT_ARROW : {
						t.searchTimer = t.defer("_startSearchFromInput", 1);
						break;
					}
				}
			});
		},

		labelFunc: function(obj) {
			return obj.query
		}

	});
});

