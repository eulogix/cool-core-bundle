define("cool/dijit/notifiableButton",
	[
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-style",
    "dojo/dom-class",
    "dojo/Evented",

    "dojo/_base/fx",
    "dojo/fx/easing",

    "dijit/_WidgetBase",
    "dijit/_TemplatedMixin",
    'dijit/_WidgetsInTemplateMixin',

    "cool/cool",
    "cool/fx/rollover",
    "cool/translator",

    "dojo/text!./templates/notifiableButton.html"
], function(declare, dom, domStyle, domClass, Evented, baseFx, easing, _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, cool, coolRollover, ctr, template) {
 
    return declare("cool.dijit.notifiableButton", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {

        templateString: template,

        iconSrc: "",

        tipToken: "",
        tipData: null,

        translator: null,

        translatorDomain: 'COOL_GLOBAL_TRANSLATOR',

        postCreate : function() {
            this.inherited(arguments);
            this.translator = new ctr({domain:this.translatorDomain});

            this.image.src = this.iconSrc;
            coolRollover.addImgRollover(this.image, true, this.getTranslator().trans( this.tipToken, this.tipData ));
        },

        getTranslator: function() {
            return this.translator;
        },

        refreshCounter: function(newValue) {
            var counterDiv = this.counterNode;
            var currentValue = counterDiv.innerHTML;
            if(newValue != currentValue) {
                if(newValue > 0) {
                    baseFx.animateProperty({
                        easing: easing.quintOut,
                        duration: 500,
                        node: counterDiv,
                        properties: {
                            top: { start: -10, end:-2 },
                            opacity: { start: 0, end:1 }
                        }
                    }).play();
                    counterDiv.innerHTML = newValue;
                } else {
                    baseFx.animateProperty({
                        easing: easing.quintOut,
                        duration: 500,
                        node: counterDiv,
                        properties: {
                            opacity: { start: 1, end:0 }
                        }
                    }).play();
                    counterDiv.innerHTML = '';
                }
            }
        }

    });
 
});