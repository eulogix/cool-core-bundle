
define("cool/widget/_ActionsMixin",
        [
            "dojo/_base/declare",
            "dojo/_base/lang",
            "dojo/dom",

            "dijit/form/Button",
            "dijit/form/DropDownButton",
            "dijit/DropDownMenu"
        ], 

function(declare, lang,
         dom,
         Button, DropDownButton, DropDownMenu) {
  
    return declare("cool.widget._ActionsMixin", [], {

        actionButtons: {},
        actionNodes: {},
        systemMenu: {},

        constructor: function() {
            this.systemMenu = null;
            this.actionNodes = {};
            this.actionButtons = {};
        },

        /**
         * renders an action from a definition object
         *
         */
        addAction: function(name, action) {

            var label = action.label!==undefined ? action.label : name;

            if(action.icon) {
                label = '<img src="'+action.icon+'" class="icon"/>' + (label ? '&nbsp;'+label : '');
            }

            //determine which widget to create
            if(action.menu) {
                //button with an attached menu
                var button = new DropDownButton({
                    optionsTitle: "Save Options",
                    //iconClass: "dijitIconFile",
                    label: label,
                    style: {"float":"right"},
                    dropDown: this.createMenu(action.menu.children),
                    onClick: this.createFunction( action.onClick )
                });

                if(action.readOnly)
                    button.set('disabled', true);

            } else {
                //default standard button
                var button = new Button({
                    label: label,
                    onClick: this.createFunction( action.onClick ),
                    style: {"float":"right"}
                });

                if(action.readOnly)
                    button.set('disabled', true);
            }


            this.addActionButton(name, button, action.group);

            return button;
        },

        /**
         * helper, also allows adding buttons js side
         */
        addActionButton: function(name, button, group) {
            if(group && this.actionNodes[ group ])
                    this.actionNodes[ group ].appendChild(button.domNode);
            else this.getToolbar().addChild(button);
            this.actionButtons[name] = button;
        },

        getActionButton: function(name) {
            return this.actionButtons[name] || false;
        },

        /**
         * renders the widget actions
         *
         */
        renderActions: function() {
            var widget = this;
            var actions = this.definition.actions;

            if(actions!=undefined) {
                for(var actionName in actions) {
                    this.addAction(actionName, actions[actionName]);
                }
            }

            var m = this._getSystemMenuItems();
            if(m.length > 0) {
                this.addAction('SYSTEM', {
                    label: null,
                    //style: {"float":"right"},
                    menu: {children:m},
                    icon: '/bower_components/fugue/icons/gear.png',
                    group: 'TITLE'
                });
            }

        },

        clearActions: function() {
            for(var nodeCategory in this.actionNodes) {
                this.actionNodes[nodeCategory].innerHTML = '';
            }
            this.actionButtons = {};
        },

        _getSystemMenuItems: function() {
            var widget = this;
            var p = [];
            if(lang.exists('definition.attributes._configurable', this) && this.definition.attributes._configurable) {
                p.push({
                    label: this.getCommonTranslator().trans("DEBUG"),
                    onClick: function() {
                        console.log(widget);
                    },
                    icon: '/bower_components/fugue/icons/bug.png'
                });
            }

            return p;
        }

    });
  
});
