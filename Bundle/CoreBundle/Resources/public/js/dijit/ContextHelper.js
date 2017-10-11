define("cool/dijit/ContextHelper",
    [
        "dojo/_base/declare",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/dom-class",
        "dojo/Evented",
        "dojo/Deferred",

        "dojo/_base/fx",
        "dojo/fx/easing",

        "dijit/_WidgetBase",
        "dijit/_TemplatedMixin",
        'dijit/_WidgetsInTemplateMixin',

        "cool/cool",
        "cool/fx/rollover",
        "cool/dijit/iconButton",
        "cool/dialog/manager",

        "dojo/text!./templates/ContextHelper.html"
], function(declare, dom, domStyle, domClass, Evented, Deferred, baseFx, easing, _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin,
            cool, coolRollover, iconButton, dialogManager, template) {

    return declare("cool.dijit.ContextHelper", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {

        templateString: template,

        iconSrc: "",

        retrieveTipFunction : function() {
            var d = new Deferred();
            r.resolve('[no tip function set]');
            return d;
        },

        postCreate : function() {
            this.inherited(arguments);

            var t = this;

            var Button = new iconButton({
                iconSrc: this.iconSrc,
                showLabel: false
            });

            dialogManager.trackMouseOver(Button.domNode);

            Button.placeAt(this.domNode);

            Button.on('MouseOver', function () {
                if(!Button.tipContent) {
                    var functionReturnvalue = t.retrieveTipFunction();
                    if(functionReturnvalue instanceof Deferred)
                        functionReturnvalue.then(function(data){
                            Button.tipContent = data;
                            dialogManager.bindTooltip(Button.domNode, Button.tipContent);
                        });
                    else {
                        Button.tipContent = functionReturnvalue;
                        dialogManager.bindTooltip(Button.domNode, Button.tipContent);
                    }
                }
            });

            //coolRollover.addAlphaRollover(this.image, 0.5, "asss");
        }

    });

});