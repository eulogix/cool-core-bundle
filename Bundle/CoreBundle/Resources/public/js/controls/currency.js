define("cool/controls/currency",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/cool",
    "cool/controls/_control",
    "cool/functions/waiter",
    "dijit/form/CurrencyTextBox",
    "dojo/currency",
    "dojo/number"
], function(declare, lang, array, cool, _control, coolWaiter, CurrencyTextBox, currencyLocale, numberLocale) {
 
    return declare("cool.controls.currency", [_control], {
		
        _initialized: false,

        _tempvalue: null,

        coolInit : function() {
            this.inherited(arguments);

            var control = this;
            var targetNode = dojo.doc.createElement('div');
            /*
            * this ugly hack (and the one in the setter) is needed because of that bug
            * https://bugs.dojotoolkit.org/ticket/17424
            *
            * the instantiation of CurrencyTextBox may fail until locales have been fully loaded,
            * this happens only with built versions of dojo
            * */
            var field = null;
            coolWaiter.waiter(
                function() {},

                function() {
                    try{
                        field = new CurrencyTextBox({
                            currency: "EUR",
                            invalidMessage: "Invalid amount.  Example: " + currencyLocale.format(54775.53, { currency: "EUR"}),
                            constraints:{fractional:true, places:'0,4'},
                            "class": control.cssClasses.join(' '),
                            "style": control.cssStyles.join(';')
                        }, targetNode);

                        //little hack that makes sure that a proper decimal separator is always inputed when pressing
                        //the numpad decimal key
                        field.on('keyPress', function(event){
                            if(event.code=='NumpadDecimal') {
                                var decimalSeparator = numberLocale.format(1.1).substring(1,2);
                                event.preventDefault();
                                event.stopPropagation();
                                field.set('value',field.get('value')+decimalSeparator);
                            }
                        });

                    } catch(e){ field = null; }

                    return field;
                },

                function() {
                    control.fieldNode.appendChild(field.domNode);
                    control.own(field);

                    if(control.isReadOnly()) {
                        field.set('readOnly', true);
                        field.set('disabled', true);
                    }

                    control.field = field;
                    control._initialized = true;

                    if(control.definition.value !== undefined) {
                        control.set('value', control.definition.value);
                        control.emit("valueInit", {});
                    }

                    //we can't use the initialLoad variable here
                    setTimeout(function() {
                                    field.on("change", function(){ control.emit("change", {}); });
                               }, 200);
                }
            );

        },

		_setValueAttr: function(value) {
            //store a temp value so that if getValue() is called in the small window between the first setValue() and control initialization completion, something is returned
            this._tempvalue = value;

            var control = this;
            coolWaiter.waiter(
                function() {},

                function() {
                    return control._initialized;
                },

                function() {
                    try {
                        if(value || value===0) {
                            control.field.set('value', Number(value) );
                        }
                        else control.field.set('value', null );
                    } catch(e){}
                }
            );
		},

		_getValueAttr: function() {
            if(!this._initialized)
                return this._tempvalue;

			var ret = this.field.get('value');
            return isNaN( ret ) ? null : ret;
		}


    });
 
});