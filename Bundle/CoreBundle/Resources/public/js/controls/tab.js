define("cool/controls/tab",
    [
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/_base/array",
        "dojo/aspect",
        "dojo/dom-class",
        "dojo/dom-style",
        "dojo/dom-geometry",
        "dojo/Evented",

        "dijit/layout/TabContainer",
        "dijit/layout/ContentPane",

        "cool/cool",
        "cool/controls/_control",
        "cool/functions/waiter"
    ], function (declare, lang, array, aspect, domClass, domStyle, domGeom, Evented, TabContainer, ContentPane, cool, _control, coolWaiter) {

        return declare("cool.controls.tab", [_control], {

            needsLabel: false,

            contentPanes: {},

            _value: null,

            constructor: function (params) {
                this._value = null;
                this.tabContainer = null;
                this.contentPanes = {};
            },

            coolInit: function () {
                this.inherited(arguments);

                var t = this;

                if(t.definition.value !== undefined) {
                    t.set('value', t.definition.value);
                    this.emit("valueInit", {});
                }

                coolWaiter.waiter(
                    function() {},

                    function() {
                        return t.domNode.parentNode;
                    },

                    function() {
                        domStyle.set(t.containerTable, "width", "100%");
                        var parentSize = domGeom.getContentBox(t.domNode.parentNode);
                        domStyle.set(t.domNode, "width", (parentSize.w-20)+"px");

                        var containerDiv = dojo.doc.createElement('div');
                        domClass.add(containerDiv, "coolTabControl");

                        t.fieldNode.appendChild(containerDiv);

                        var field = new TabContainer({
                            doLayout: false,
                            nested: false,
                            persist: false,
                            useMenu: true,
                            useSlider: true
                        });

                        domStyle.set(field.containerNode, "display", "none");

                        t.own(field);
                        t.field = field;

                        array.forEach(t.parameters.definition.parameters.options, function(option) {
                            var cp = new ContentPane({
                                title: option.label,
                                _cvalue: option.value,
                                content:''
                            });
                            field.addChild(cp);
                            t.contentPanes[''+option.value] = cp;
                        });

                        field.placeAt(containerDiv);
                        field.startup();

                        field.watch("selectedChildWidget", function(name, oval, nval){
                            t.set('value', nval._cvalue);
                        });

                    }
                );
            },

            resize: function () {
                this.inherited(arguments);
                var parentSize = domGeom.getContentBox(this.domNode.parentNode);
                domStyle.set(this.domNode, "width", (parentSize.w-20)+"px");
            },

            _setValueAttr: function (value, dontFireOnChange) {
                var oldValue = this.get('value');
                //even if the tabview hasn't rendered yet, we have the value correctly set
                this._value = value;

                if (!this.firstLoad && (oldValue != value) && !dontFireOnChange) {
                    this.emit("change", {});
                }

                //code that deals with tabview update
                var widget = this;
                if (this.field != null) {
                    if(lang.exists( ''+value, this.contentPanes))
                        this.field.selectChild(this.contentPanes[''+value]);
                    else {
                        var firstValue = Object.keys(this.contentPanes)[0];
                        this.set('value', firstValue);
                    } //a tab control can't be null, so we select the first option
                } else {
                    setTimeout(function () {
                        widget.set('value', value);
                    }, 10); //Execute do.something() 1 second later.
                }

            },

            _getValueAttr: function () {
                return this._value;
            }

        });

    });