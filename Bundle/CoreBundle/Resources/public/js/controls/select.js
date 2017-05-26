define("cool/controls/select",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dojo/Evented",
    "dijit/form/Select",
    "cool/dialog/manager"
], function(declare, lang, array, _control, Evented, Select, dialogManager) {
 
    return declare("cool.controls.select", [_control], {

        options : {},
        nilToken : '[{NIL}]',

        coolInit : function() {
            this.inherited(arguments);

            var control = this;

            var options = this._getDefinitionOptions();
            if(options) {
                var field = new Select({
                    "options"   :   options,
                    "maxHeight" :   350,
                    "class"     :   this.cssClasses.join(' '),
                    "style"     :   this.cssStyles.join(';')
                }, dojo.doc.createElement('div'));

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

                this.options = options;

                if(this._hasTooltip()) {
                    dialogManager.trackMouseOver(field.domNode);
                    if(this.definition.tooltip.url) {
                        this.on('tooltipUrlChanged', function(newTooltipUrl){
                            dialogManager.bindTooltip(field.domNode, null, this.definition.tooltip.maxWidth, newTooltipUrl);
                        });
                    } else dialogManager.bindTooltip(field.domNode, this.definition.tooltip.content, this.definition.tooltip.maxWidth);
                }

                if(this.definition.value !== undefined) {
                    this.set('value', this.definition.value);
                    this.emit("valueInit", {});
                }
            }
        },

        _getDefinitionOptions : function() {
            var options = false;
            try {
                //clone the array to avoid modifications to definition in case a widget action returns a
                options = this.parameters.definition.parameters.options.slice(0);
                options.unshift({label:'-', value:this.nilToken});
                //ensure that we have only string labels
                array.forEach(options,function(option){ option.label+=''});
            } catch(e) {}
            return options;
        },

		_setValueAttr: function(value) {
            try {
			    if(value !== null) {
                    this.field.set('value', value);
                } else {
                    this.field.set('value', this.nilToken);
                }
            } catch(e){
                console.log(e);
            }
		},

		_getValueAttr: function() {
			var ret = undefined;
            try { ret = this.field.get('value'); } catch(e) {}
            return ret == this.nilToken ? null : ret;
		},

        /**
         * returns the array
         * @returns {*|null}
         */
        getSelectedOption: function() {
            return this.field.getOptions({ selected: true });
        },

        getSelectedProperty: function( propertyName ) {
            var selectedOption = this.getSelectedOption() || {};
            if(selectedOption.hasOwnProperty( propertyName ))
                return selectedOption[ propertyName ];
            return null;
        },

        filterOnFunction: function(f) {
            var options = this._getDefinitionOptions();
            var filteredOptions = [];
            var nilToken = this.nilToken;
            var setToNil = true;
            var currentSelectedValue = this.field.get('value');
            array.forEach(options,function(option){
                if(option.value == nilToken || f(option)) {
                    filteredOptions.push(option);
                    if(option.value == currentSelectedValue)
                        setToNil = false;
                }
            });
            this.field.set('options', filteredOptions);
            if(setToNil)
                this.field.set('value', nilToken);
        },

        setOptions: function(options) {
            this.options = options;
            this.field.set('options', options);
            this.set('value',this.definition.value);
        }

    });
 
});