define("cool/controls/textarea",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dijit/form/SimpleTextarea",
    "dojo/on"
], function(declare, lang, array, _control, Textarea, on) {
 
    return declare("cool.controls.textarea", [_control], {

    	coolInit : function() {
            this.inherited(arguments);

            var control=this;

            var field = new Textarea({
                "class": this.cssClasses.join(' '),
                "style": this.cssStyles.join(';')
            }, dojo.doc.createElement('div'));

            this.fieldNode.appendChild(field.domNode);
           	this.own(field);
            this.field = field;

            field.on("change", function(){
                if(!control.firstLoad)
                    control.emit("change", {});
            });

            if(this.isReadOnly()) {
                field.set('readOnly', true);
                this.disable();
            }

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

        insertAtCursor: function(text) {

    	    var textarea = this.field.textbox;
            var newValue = this.get('value');

            if (textarea.selectionStart || textarea.selectionStart === '0') {
                var startPos = textarea.selectionStart;
                var endPos = textarea.selectionEnd;
                newValue = textarea.value.substring(0, startPos)
                    + text
                    + textarea.value.substring(endPos, textarea.value.length);
            } else {
                newValue += text;
            }

            this.set('value', newValue);
        }

    });
 
});