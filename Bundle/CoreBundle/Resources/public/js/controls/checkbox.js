define("cool/controls/checkbox",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dijit/form/CheckBox"
], function(declare, lang, array, _control, CheckBox) {
 
    return declare("cool.controls.checkbox", [_control], {
		
    	coolInit : function() {
            this.inherited(arguments);
            var t = this;

    		var field = new CheckBox(lang.mixin(this.parameters,{
                    "class": this.cssClasses.join(' '),
                    "style": this.cssStyles.join(';')
                }), dojo.doc.createElement('div'));

           	this.fieldNode.appendChild(field.domNode);
           	this.own(field);
            this.field = field;

            if(this.isReadOnly()) {
                field.set('readOnly', true);
                this.disable();
            }


            field.on("change", function(){
                if(!t.firstLoad)
                    t.emit("change", {});
            });

            if(this.definition.value !== undefined) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
		},

        /**
         * FROM DIJIT DOCS (for checkbox):
         * During initialization, just saves as attribute to the `<input type=checkbox>`.

         After initialization,
         when passed a boolean, controls whether or not the CheckBox is checked.
         If passed a string, changes the value attribute of the CheckBox (the one
         specified as "value" when the CheckBox was constructed
         (ex: `<input data-dojo-type="dijit/CheckBox" value="chicken">`).

         `widget.set('value', string)` will check the checkbox and change the value to the
         specified string.

         `widget.set('value', boolean)` will change the checked state.

         * @param value
         * @private
         */
		_setValueAttr: function(value) {
            if(value == '' || value==null || value==undefined)
                value = false;
            else if(value == 'false' || value == false || value == '0' || value == 'off' || value == 0)
                value = false;
            else /*if(value == 'true' || value == true || value == '1')*/
                value = true;

			this.field.set('checked', value);
		},

		_getValueAttr: function() {
            return this.field.get('checked')==true;
		}


    });
 
});