define("cool/dijit/Highlighter",
	[
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-style",
    "dojo/dom-class",
    "dojo/dom-geometry",
    "dojo/Evented",

    "dojo/_base/fx",
    "dojo/fx/easing",

    "dijit/_WidgetBase",
    "dijit/_TemplatedMixin",
    'dijit/_WidgetsInTemplateMixin',

    "dojo/text!./templates/Highlighter.html"
], function(declare, dom, domStyle, domClass, domGeometry, Evented, baseFx, easing, _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, template) {
 
    return declare("cool.dijit.notifiableButton", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {

        templateString: template,

        overlayNode : null,

        postCreate : function() {
            this.inherited(arguments);
            var t = this;
            domStyle.set(this.domNode, {
                'position' : 'relative'
            });
            this.createOverlay();
            setTimeout(function(){t.highlight()}, 1000);
            setTimeout(function(){t.hide()}, 3000);
        },

        highlight : function() {
            this.resizeOverlay();

            baseFx.animateProperty({
                node: this.overlayNode,
                properties: {
                    opacity: { start: 0, end: 0.8 },
                    "background-color": { start:'white', end:'red' }
                },
                duration: 300,
                easing: easing.quadOut
            }).play();

        },

        hide : function() {
            baseFx.animateProperty({
                node: this.overlayNode,
                properties: {
                    opacity: { end: 0 }
                },
                duration: 300,
                easing: easing.quadOut
            }).play();

        },

        createOverlay : function() {
            var div = dojo.doc.createElement('div');

            domStyle.set(div, {
                'left' : 0,
                'top' : 0,
                'opacity' : 0,
                'position' : 'absolute',
                'pointer-events': 'none'
            });

            this.overlayNode = div;
            this.containerNode.appendChild(this.overlayNode);
        },

        resizeOverlay : function() {
            var contentBox = domGeometry.getContentBox(this.containerNode);

            domStyle.set(this.overlayNode, {
                'width' : contentBox.w+'px',
                'height' : contentBox.h+'px',
            });
        }

    });
 
});