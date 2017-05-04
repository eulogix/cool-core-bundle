define("cool/controls/multiSelect",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dojo/Evented",
    "dijit/form/MultiSelect"

], function(declare, lang, array, _control, Evented, MultiSelect) {
 
    return declare("cool.controls.multiSelect", [_control], {

        coolInit : function() {
            this.inherited(arguments);

            var control = this;
            try {
                var options = this.parameters.definition.parameters.options;
            } catch(e) {
                var options = false;
            }
            if(options) {

                var field = new MultiSelect({
                    "class": this.cssClasses.join(' '),
                    "style": this.cssStyles.join(';')
                }, dojo.doc.createElement('div'));

                //MultiSelect does not allow passing options as a parameter in the constructor..
                for(var o in options) {
                    var optElem = dojo.doc.createElement('option');
                    optElem.innerHTML = options[o].label;
                    optElem.value = options[o].value;
                    field.domNode.appendChild(optElem);
                }

                this.fieldNode.appendChild(field.domNode);
                this.own(field);
                this.field = field;

                if(this.isReadOnly()) {
                    field.set('readOnly', true);
                    this.disable();
                }

                field.on("change", function(){
                    if(!control.firstLoad)
                        control.emit("change", {});
                });

                if(this.definition.value !== undefined) {
                    this.set('value', this.definition.value);
                    this.emit("valueInit", {});
                }
            }
        },

		_setValueAttr: function(value) {
            this.field.set('value', JSON.parse(value) );
		},

		_getValueAttr: function() {
            return JSON.stringify( this.field.get('value') );
		},

        getSelectedItems: function() {
            return this.field.get('value');
        },

        _setRawValueAttr: function(items) {
            console.log(items);
            this.field.set('value', items);
        }

    });
 
});