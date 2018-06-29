define("cool/controls/JSONEditor",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control"
], function(declare, lang, array, _control) {
 
    return declare("cool.controls.JSONEditor", [_control], {
		
    	constructor: function(params) {
  			this.inherited(arguments);
  			this.editor = {};
    	},

    	coolInit : function() {
			this.inherited(arguments);

		    // Initialize the editor with a JSON schema
			this.editor = new JSONEditor(this.fieldNode,
			{
				disable_edit_json : true,
				no_additional_properties : true,
				disable_properties_selector : true,
				schema: this._getSchema()
			});

			if(this.definition.hasOwnProperty('value')) {
				this.set('value', this.definition.value);
			}
		},

		_setValueAttr: function(value) {
			this.editor.setValue( JSON.parse(value) );
		},

		_getValueAttr: function() {
			return JSON.stringify( this.editor.getValue() );
		},

		_getSchema: function() {
            var ret;
            try {
                ret = JSON.parse( this.parameters.definition.parameters.json_schema );
            }
            catch(err) {
                ret = null;
            }
            return ret;
		}


    });
 
});