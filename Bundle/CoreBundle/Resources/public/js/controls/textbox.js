define("cool/controls/textbox",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dijit/form/ValidationTextBox",
    "dijit/form/_TextBoxMixin"
], function(declare, lang, array, _control, ValidationTextBox, _TextBoxMixin) {
 
    return declare("cool.controls.textbox", [_control], {

        coolInit : function() {
            this.inherited(arguments);

            var control=this;

            var field = new ValidationTextBox(lang.mixin(this.parameters,{
                "class": this.cssClasses.join(' '),
                "style": this.cssStyles.join(';'),
                "type": this._isPassword() ? 'password' : 'text',
                "trim": true
            }), dojo.doc.createElement('div'));

            this.fieldNode.appendChild(field.domNode);
            this.own(field);
            this.field = field;

            if(this.isReadOnly()) {
                this.field.set('readOnly', true);
                this.disable();
            }

            this.field.on("change", function(){
                if(!control.firstLoad)
                    control.emit("change", {});
            });

            if(this.definition.value !== undefined) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
		},

		_setValueAttr: function(value) {
			this.field.set('value', value);
		},

		_getValueAttr: function() {
			return this.field.get('value');
		},

        _isPassword: function() {
            return this.getParameter('isPassword') == true;
        },

        select: function() {
            this.inherited(arguments);
            this.field.focus();
            _TextBoxMixin.selectInputText(this.field.textbox);
        }

    });
 
});