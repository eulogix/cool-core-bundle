
define("cool/widget/_HelpMixin",
        [
            "dojo/_base/declare",
            "dojo/_base/lang",
            "dojo/_base/array",
            "dojo/dom",

            "dijit/Menu",
            "dijit/MenuItem",
            "dijit/form/ComboButton",

            "cool/cool"

        ],

function(declare, lang, array, dom, Menu, MenuItem, ComboButton, cool
         ) {
  
    return declare("cool.widget._HelpMixin", [], {

        helpersState: false,

        constructor: function() {
        },

        processHelpItems: function() {
            var t = this;

            if(!this.hasHelpers() && !this.hasHelpItems())
                return;

            var menu = new Menu({ style: "display: none;"});

            array.forEach(this.definition.helpItems, function(helpItem) {
                var menuItem = new MenuItem({
                    label: helpItem.label,
                    onClick: t._createOnClickFunctionForHelpItem(helpItem)
                });
                menu.addChild(menuItem);
            });

            var button = new ComboButton({
                label: '<img src="/bower_components/fugue/icons/question.png" class="icon"/>',
                dropDown: menu,
                style: {"float":"right"}
            });

            if(this.hasHelpers()) {
                button.on('click', function() { t.toggleHelpers() });
            }

            this.addActionButton("Help", button, 'TITLE');

        },

        toggleHelpers: function(onOrOff) {
            this.helpersState = onOrOff === undefined ? !this.helpersState : onOrOff;
        },

        hasHelpers: function() {
            return false;
        },

        hasHelpItems: function() {
            return this.definition.helpItems.length > 0;
        },

        _createOnClickFunctionForHelpItem: function(helpItem) {
            switch(helpItem.type) {
                case 'SIMPLE' : return this._createSimpleOnClickFunction(helpItem);
            }
            return function(){};
        },

        _createSimpleOnClickFunction: function(helpItem) {
            var dialog;

            switch(helpItem.display_mode) {
                case 'BROWSER_TAB' : {
                    //we suppose content is a URL
                    return function() {
                        var win = window.open(helpItem.content, '_blank');
                        win.focus();
                    }
                }
                case 'MODAL_POPUP' : {
                    switch(helpItem.content_type) {
                        case 'URL' : {
                            return function() {
                                dialog = cool.getDialogManager()._getModalDialog(helpItem.label, helpItem.content, 80 );
                                dialog.startup();
                                dialog.show();
                            }
                        }
                        case 'HTML' : {
                            return function() {
                                dialog = cool.getDialogManager()._getModalDialog(helpItem.label, null, 80, {
                                    content: helpItem.content
                                });
                                dialog.startup();
                                dialog.show();
                            }
                        }
                    }
                }
            }
        }

    });
  
});
