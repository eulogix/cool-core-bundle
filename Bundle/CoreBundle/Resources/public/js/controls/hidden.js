define("cool/controls/hidden",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control"
], function(declare, lang, array, _control) {
    
    return declare("cool.controls.hidden", [_control], {
		  
      needsLabel : false,

      constructor: function(params) {
          this.inherited(arguments);
          this.value = null;
      },

      coolInit: function() {
          this.inherited(arguments);

          if(this.parameters.att_labelize) {
            var div = dojo.doc.createElement('div');
            div.innerHTML = this.definition.value;
            this.fieldNode.appendChild(div);
          }

          if(this.definition.value !== undefined) {
              this.set('value', this.definition.value);
              this.emit("valueInit", {});
          }
      },

  		_setValueAttr: function(value) {
  			this.value = value;
  		},

  		_getValueAttr: function() {
  			return this.value;
  		}

    });
 
});