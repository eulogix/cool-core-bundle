define("cool/window",
	[
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-style",
    "dojo/dom-class",
    "dojo/dom-geometry",
    "dijit/layout/ContentPane",
    "dijit/TitlePane",
    "dijit/_WidgetBase",
    "dojo/Evented",
    "dijit/_OnDijitClickMixin",
    "dijit/_TemplatedMixin",
    "dijit/_DialogMixin",
    "dojo/text!./templates/window.html"
], function(declare, dom, domStyle, domClass, domGeometry, ContentPane, TitlePane, _WidgetBase, Evented, _OnDijitClickMixin, _TemplatedMixin, _DialogMixin, template) {
 
    return declare("cool.window", [_WidgetBase, _TemplatedMixin, Evented/*TitlePane, _DialogMixin*/], {
        templateString: template,

        closeable: false,
        transparent: false,
        fillContent: false,

        postCreate : function() {
            this.inherited(arguments);
            if(this.closeable) {
                domStyle.set(this.closeButtonNode, "display", "table");
            } else domStyle.set(this.closeButtonNode, "display", "none");
            if(!this.transparent)
                this.bodyNode.className = "widgetBodySolid";
        },

		onTitleClick: function(){
			// summary:
			//		Handler when user clicks the title bar
			// tags:
			//		private
			if(this.toggleable){
				this.toggle();
			}
		},

		onClose: function() {
			this.destroyRecursive();
		},

        colorOrange: function() {
            this._addTitleClass("widgetTitleOrange");
        },

        colorGray: function() {
            this._addTitleClass("widgetTitleGray");
        },

        resetColor: function() {
            domClass.remove(this.titleBarNode);
            this._addTitleClass("widgetTitle");
        },

        _addTitleClass: function(className) {
            if(!domClass.contains(this.titleBarNode, className))
                domClass.add(this.titleBarNode, className);
        },

        resize: function() {
            this.inherited(arguments);
            if(this.fillContent) {
                var nodeBox = domGeometry.getContentBox(this.domNode.parentNode);
                var titleBox = domGeometry.getContentBox(this.titleBarNode);
                domStyle.set(this.domNode, {
                    "height": nodeBox.h+"px"
                });
                domStyle.set(this.bodyNode, {
                    "height": "100%"
                });
                domStyle.set(this.containerNode, {
                    "height": (nodeBox.h - titleBox.h -1)+"px",
                    "overflow": "auto"
                });
            }
            this.emit('resize');
        }


    });
 
});