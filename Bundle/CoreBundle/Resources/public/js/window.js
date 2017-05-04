define("cool/window",
	[
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-style",
    "dojo/dom-class",
    "dijit/layout/ContentPane",
    "dijit/TitlePane",
    "dijit/_WidgetBase",
    "dijit/_OnDijitClickMixin",
    "dijit/_TemplatedMixin",
    "dijit/_DialogMixin",
    "dojo/text!./templates/window.html"
], function(declare, dom, domStyle, domClass, ContentPane, TitlePane, _WidgetBase, _OnDijitClickMixin, _TemplatedMixin, _DialogMixin, template) {
 
    return declare("cool.window", [TitlePane, _DialogMixin], {
        templateString: template,
        closeable: false,

        postCreate : function() {
            if(this.closeable) {
                domStyle.set(this.closeButtonNode, "display", "table");
            }
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
        }

    });
 
});