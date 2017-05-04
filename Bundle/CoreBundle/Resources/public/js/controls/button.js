define("cool/controls/button",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "dojo/Deferred",
    "cool/controls/_control",
    "cool/dijit/Button",
    "dojo/on"
], function(declare, lang, array, Deferred, _control, Button, on) {
 
    return declare("cool.controls.button", [_control], {
		
        needsLabel : false,

    	coolInit : function() {
            this.inherited(arguments);

            var props = {
                type: "button", //"submit" submits the whole page
                label: this.definition.label
            };

            array.forEach(['iconSrc', 'iconSrcRight'], function(p){
                var pv;
                if (pv = this.getParameter(p))
                    props[p] = pv;
            }, this);

            if(this.getParameter('onClick')) {
                props.onClick = this._buildOnClickFunction();
            }

            var field = new Button(props, dojo.doc.createElement('div'));

            this.fieldNode.appendChild(field.domNode);
            this.own(field);
            this.field = field;

            if(this.isReadOnly()) {
                this.disable();
            }

            if(this.definition.value !== undefined) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
		},

        click: function() {
          this.field.onClick();
        },

        _buildOnClickFunction: function() {

            var self = this;
            var confirmMessage = this.getParameter('confirmMessage');
            var disabledOnClick = this.getParameter('disabledOnClick');

            var onClick = this.getContainerWidget().createFunction(this.getParameter('onClick'));

            return function() {
                if(disabledOnClick)
                    self.set('disabled', true);

                var proceed = confirmMessage ? confirm(confirmMessage) : true;

                var ret = null;
                if(proceed) {
                    ret = onClick();
                }

                if(disabledOnClick) {
                    if (ret instanceof Deferred) {
                        ret.then(function(){
                            try {
                                self.set('disabled',false);
                            } catch(e) {}   //the control may not exist anymore
                        });
                    } try {
                        self.set('disabled',false);
                    } catch(e) {}   //the control may not exist anymore
                }
            };
        },

		_setValueAttr: function(value) {
			this.field.set('value', value);
		},

		_getValueAttr: function() {
			return this.field.get('value');
		},

        _setDisabledAttr: function(value) {
            this.field.set('disabled', value);
        },

        _getDisabledAttr: function() {
            return this.field.get('disabled');
        }

    });
 
});