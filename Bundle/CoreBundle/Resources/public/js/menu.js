define("cool/menu",
	[
        "dojo/_base/declare",
        "dojo/request",

        "dijit/_WidgetBase",
        "dijit/MenuBar",
        "dijit/DropDownMenu",
        "dijit/PopupMenuItem",
        "dijit/PopupMenuBarItem",
        "dijit/Menu",
        "dijit/MenuItem",
        "dijit/MenuBarItem",
        "dojo/domReady!"
], function(declare, request, _WidgetBase, MenuBar, DropDownMenu, PopupMenuItem, PopupMenuBarItem, Menu, MenuItem, MenuBarItem  ) {
 
    return declare("cool.menu",  [_WidgetBase], {

        params: [],

        definition: [],

        pMenuBar: {},

        constructor: function(params) {
            this.params = params;
        },

        postCreate: function(){
            this.inherited(arguments);
            if(this.params.definition) {
                this.definition = this.params.definition;
                this.refresh();
            } else this.refetch();
        },

        refetch: function(){
            var t = this;
            request(this.params.url,{
                handleAs: 'json'
            }).then(
                function(data){
                    t.definition = data;
                    t.refresh();
                },
                function(error){
                    console.log("An error occurred: " + error);
                }
            );
        },

        refresh: function(){
            try{
                this.pMenuBar.destroyRecursive();
            } catch(e) {}
            this.createMenu(this.definition);
        },

        createFunction: function (js) {
            //necessary because if declared without eval, closure compiler modifies the name of the variable, which would later be unaccessible in the created function!
            eval('var menu = this;');
            eval('var widget = this;');
            return function() {
                if(js!=undefined) {
                    eval(js);
                }
            }
        },

        createMenu: function(items, level, parentMenu) {

            if(items == undefined || items.length == 0) {
                return;
            }

            if(level == undefined) {
                //at the first level, we create a menubar
                this.pMenuBar = new MenuBar({});
                this.createMenu(items.children, 1, this.pMenuBar);
                this.pMenuBar.placeAt(this.domNode);
                this.pMenuBar.startup();

                return;

            } else {

                if(level==1) {
                    var subMenuClass = PopupMenuBarItem;
                    var itemClass = MenuBarItem;
                } else {
                    var subMenuClass = PopupMenuItem;
                    var itemClass = MenuItem;
                }

                for(var i = 0; i< items.length; i++) {
                    var o = {
                        label: items[i].label,
                        //iconClass: "dijitEditorIcon dijitEditorIconSave",
                        onClick: this.createFunction( items[i].onClick )
                    };

                    if(items[i].children.length > 0) {
                        var subItem = new DropDownMenu({});
                        this.createMenu(items[i].children, level+1, subItem);
                        o.popup = subItem;
                        var menuItem = new subMenuClass(o);
                    } else {
                        var menuItem = new itemClass(o);
                    }

                    parentMenu.addChild(menuItem);
                }

            }
        }

    });
 
});