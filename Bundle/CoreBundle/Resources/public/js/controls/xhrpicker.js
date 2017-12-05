define("cool/controls/xhrpicker",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "cool/dijit/iconButton",

    "dojo/json",
    "dojo/store/Memory",
    "dojo/store/JsonRest",
    "cool/store/xhrstore",
    "dojo/dom-construct",

    "dojo/_base/fx", "dojo/fx",
    "dojo/fx/Toggler",
    "dijit/form/FilteringSelect"


], function(declare, lang, array, _control,
            iconButton,
            JSON,
            Memory,
            JsonRest,
            xhrStore,
            domConstruct,
            fx, coreFx, Toggler,
            FilteringSelect) {
 
    return declare("cool.controls.xhrpicker", [_control], {

        nilToken : '[{NIL}]',

        /**
         * used to temporarily store (and return) a proper value while the filtering select performs the server lookup
         * without it, the control would return null while the select is querying the store!
         * This is harmful when for instance a form is used as a lister filter, the raw parameters would not get passed fully
         */
        tempValue: null,

        initCall: true,

        coolInit : function() {
            this.inherited(arguments);

            var t = this;
            var storeUrl = this.parameters.definition.storeUrl || null;
            var storeParameters = lang.mixin({}, this.parameters.definition.storeParameters || {});
            var placeHolder = this.parameters.definition.placeholder || "Select an item.";

            var restStore = new xhrStore({
                idProperty: "value",
                target: storeUrl,
                postVars: storeParameters
            });

            var field = new FilteringSelect({
                "class": this.cssClasses.join(' '),
                "style": this.cssStyles.join(';'),
                store: restStore,
                searchAttr: "label",
                labelAttr:  "label",
                value: this.nilToken,
                //html may do, but highlighting wouldn't work then
                labelType: "text",
                //query: storeParameters,
                autoComplete: false,
                selectOnClick: true,
                placeholder: placeHolder,
                onChange: function() {
                    t._refreshToggles();
                }
            }, dojo.doc.createElement('div'));

            var clearButton = new iconButton({
                label: GlobalTranslator.trans('xhrPickerClearLabel'),
                onClick: function() {
                    field.reset()
                },
                iconSrc: "/bower_components/fugue/icons/cross-white.png",
                showLabel: false,
                tooltip: GlobalTranslator.trans('xhrPickerClearTooltip')
            });

            this.buttonToggler = new Toggler({
                node: clearButton.domNode,
                showFunc: fx.fadeIn,
                hideFunc: fx.fadeOut,
                onEnd: function(node){
                    clearButton.domNode.style.display = t._isEmptyTogglerVisible() ? 'inline' : 'none';
                }
            });

            this.fieldNode.appendChild(field.domNode);

            this.own(field);
            this.field = field;

            field.on("change", function(){
                t.tempValue = t._valueToInternalFieldValue(field.get('value'));
                t._refreshToggles();
                if(!t.initCall) {
                    t.emit("change", {});
                }
                t.initCall = false;
            });

            if(this.isReadOnly()) {
                field.set('readOnly', true);
                this.disable();
            }

            this._appendButtonNode(clearButton.domNode);

            if(this.definition.value !== undefined) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
        },

        setQueryParameter: function(parameter, value) {
            if(value && value != this.nilToken)
                this.field.query[parameter] = value;
            else delete this.field.query[parameter];
        },

        _setValueAttr: function(value) {
            this.tempValue = this._valueToInternalFieldValue(value);
            try {
                this.field.set('value', this.tempValue);
            } catch(e){}
            this._refreshToggles();
        },

        _getValueAttr: function() {
            return this._internalFieldValueToValue(this.tempValue);
        },

        _valueToInternalFieldValue: function(value) {
            return value ? value : this.nilToken;
        },
        _internalFieldValueToValue: function(value) {
            return value == this.nilToken ? null : value;
        },

        _refreshToggles: function() {
            if(this._isEmptyTogglerVisible())
                 this.buttonToggler.show();
            else this.buttonToggler.hide();
        },

        _isEmptyTogglerVisible: function() {
            return !this.isReadOnly() && this.get('value');
        }

    });
 
});