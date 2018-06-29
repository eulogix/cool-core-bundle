define("cool/controls/chosenSelect",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "dojo/dom-construct",
    "cool/controls/_control",
    "dojo/Evented",
    "dijit/form/MultiSelect"

], function(declare, lang, array, domConstruct, _control, Evented, MultiSelect) {
 
    return declare("cool.controls.chosenSelect", [_control], {

        nilToken : '[{NIL}]',

        _isMultiple : function() {
            return this.getParameter('multiple');
        },

        coolInit : function() {
            this.inherited(arguments);

            var control = this;
            var options = this._getDefinitionOptions();

            if(options) {
                var select = dojo.doc.createElement('select');
                select.multiple = this._isMultiple();
                select.cssText = this.cssStyles.join(';');

                this.fieldNode.appendChild(select);
                this.field = select;

                this.filterOnFunction(function() {return true});

                var chosenInit = {
                    width: '100%',
                    placeholder_text_multiple: GlobalTranslator.trans('chosenSelectPlaceHolderMultiple'),
                    allow_single_deselect:true
                };

                var w = this.getParameter('width');
                if(w) {
                    chosenInit.width = w;
                }

                jQuery(select).chosen(chosenInit);

                if(this.isReadOnly()) {
                    select.disabled = true;
                }

                jQuery(select).on('change', function(evt, params) {
                    if(!control.firstLoad)
                        control.emit("change", {});
                });

                if(this.definition.hasOwnProperty('value')) {
                    this.set('value', this.definition.value);
                    this.emit("valueInit", {});
                }
            }
        },

        _getDefinitionOptions : function() {
            var options = false;
            try {
                options = this.parameters.definition.parameters.options.slice(0);
                if(!this._isMultiple()) {
                    options.unshift({label:'-', value:this.nilToken});
                }
                //ensure that we have only string labels
                array.forEach(options,function(option){ option.label+=''});
            } catch(e) {}
            return options;
        },

		_setValueAttr: function(value) {
            var valueArr;
            if(this._isMultiple()) {
                valueArr = value == this.nilToken ? []  : (JSON.parse(value) || []);
            } else valueArr = [value];

            var select = this.field;

            for(var j=0;j<select.options.length;j++) {
                select.options[j].selected = false;
                for(var i=0;i<valueArr.length;i++) {
                    if(select.options[j].value == valueArr[i])
                        select.options[j].selected = true;
                }
            }

            jQuery(select).trigger("chosen:updated");
            if(!this.firstLoad)
                this.emit("change", {});
		},

		_getValueAttr: function() {
            if(this._isMultiple()) {
                var selectedOptionsValues = array.map(this.getSelectedOptions(), function(item){
                    return item.value
                });
                return JSON.stringify(selectedOptionsValues);
            }

            var op = this.getSelectedOption();
            if(op) {
                var ret = this.getSelectedOption().value;
                return ret == this.nilToken ? null : ret;
            } else return null;
		},

        getSelectedOptions: function() {
            var select = this.field;
            var selected = [];
            for(var i=0;i<select.options.length;i++) {
                if(select.options[i].selected)
                    selected.push(select.options[i]);
            }
            return selected;
        },

        getSelectedOption: function() {
            if(!this._isMultiple())
                return this.getSelectedOptions().pop();
            return {};
        },

        filterOnFunction: function(f) {
            var options = this._getDefinitionOptions();
            var filteredOptions = [];
            var nilToken = this.nilToken;
            var setToNil = true;
            var currentSelectedValue = this.get('value');

            array.forEach(options,function(option){
                if(option.value == nilToken || f(option)) {
                    filteredOptions.push(option);
                    if(option.value == currentSelectedValue)
                        setToNil = false;
                }
            });

            var select = this.field;
            domConstruct.empty(select);
            for(var j=0;j<filteredOptions.length;j++) {
                var optElem = lang.mixin(dojo.doc.createElement('option'), filteredOptions[j]);
                optElem.innerHTML = filteredOptions[j].label;
                select.appendChild(optElem);
            }
            this.set('value', setToNil ? nilToken : currentSelectedValue);
            jQuery(select).trigger("chosen:updated");
        }

    });
 
});