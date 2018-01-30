define("cool/widget/message",
	[
        "dojo/_base/lang",
        "dojo/_base/declare",
        "dojo/_base/array",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/dom-class",

        "dojo/fx",
        "dojo/fx/easing",
        "dojo/_base/fx",
        "dojo/Deferred",
        "dojo/Evented",

        "dojox/fx",
        "dojox/fx/_core",
        "dojox/fx/scroll",
        "dojox/fx/Shadow",

        "dijit/_WidgetBase",
        "dijit/_TemplatedMixin",
        'dijit/_WidgetsInTemplateMixin',

        "dojo/text!./templates/message.html"
], function(lang, declare, array, dom, domStyle, domClass,
            coreFx, easing, baseFx, Deferred, Evented, Fx, FxCore, Scroll, Shadow,
            _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin,
            template) {
 
    return declare("cool/widget/message", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {

        templateString: template,

        type: 'info',

        text: '-',

        canClose: true,

        postCreate : function() {
            this.inherited(arguments);
            var t = this;

            var icon = "";

            switch(this.type) {
                case 'error'    : icon = "/bower_components/fugue/icons/exclamation-red.png"; break;
                case 'warning'  : icon = "/bower_components/fugue/icons/exclamation-circle.png"; break;
                case 'info'     : icon = "/bower_components/fugue/icons/tick-circle.png"; break;
                case 'info2'    : icon = "/bower_components/fugue/icons/information.png"; break;
            }

            this.messageIcon.src = icon;
            this.setText(this.text);
            this.containerNode.className = "widgetMessage "+this.type;

            if(this.canClose) {
                this.domNode.onclick = function() {
                    t.close();
                };
            }

            coreFx.wipeIn({
                node: this.domNode
            }).play();

            baseFx.animateProperty({
                node: this.domNode,
                properties: {
                    opacity: {
                        start: 0,
                        end: 1
                    }
                },
                easing: easing.quadOut,
                onEnd: function(){
                    t.emit('changeRender');
                }
            }).play();

        },

        close: function() {
            var t = this;
            coreFx.wipeOut({
                node: this.domNode,
                onEnd: function(){
                    t.emit('close');
                    t.emit('changeRender');
                    t.destroyRecursive();
                }
            }).play();
        },

        setText: function(message) {
            this.text = message;
            this.messageNode.innerHTML = this.text;
        },

        setProgressBarVisibility: function(onOrOff) {
            domStyle.set(this.progressBarContainer, 'display', onOrOff ? 'inline' : 'none');
        }

    });
 
});