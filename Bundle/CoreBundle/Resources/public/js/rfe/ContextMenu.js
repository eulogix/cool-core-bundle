define([
	'dojo/_base/lang',
	'dojo/_base/declare',
	"dojo/_base/array",
	'dijit/Menu',
	'dijit/MenuItem',
	'dijit/MenuSeparator',
	'dijit/PopupMenuItem'
], function(lang, declare, array, Menu, MenuItem, MenuSeparator, PopupMenuItem) {

	/**
	 * Provides a context menu with items to edit files and folders.
	 * @class
	 * @name rfe.ContextMenu
	 * @extends {dijit.Menu}
	 * @property {rfe} rfe reference to remoteFileExplorer
	 * @property {number} popUpDelay
	 */
	return declare([Menu], /** @lends rfe.ContextMenu.prototype */ {

		rfe: null,
		popUpDelay: 10,
		menuItems: null,

		contextualMenus: [],

		postCreate: function() {
			var rfe = this.rfe;
			this.inherited('postCreate', arguments);

			var menu, menuItems = this.menuItems = {};

			menuItems.newFolder = new MenuItem({
				label: this.rfe.translator.trans("new folder"),
				onClick: function() { rfe.createRename({dir:true}); },
				iconClass: "dijitIconFile",
				disabled: false
			});
			this.addChild(menuItems.newFolder);

			menuItems.download = new MenuItem({
				label: this.rfe.translator.trans("download"),
				onClick: lang.hitch(this.rfe, this.rfe.download),
				iconClass: "dijitIconFile",
				disabled: false
			});
			this.addChild(menuItems.download);

			menuItems.rename = new MenuItem({
				label: this.rfe.translator.trans("rename"),
				onClick: lang.hitch(this.rfe, this.rfe.rename),
				disabled: true
			});
			this.addChild(menuItems.rename);

			menuItems.del = new MenuItem({
				label: this.rfe.translator.trans("delete"),
				onClick: lang.hitch(this.rfe, this.rfe.del),
				disabled: true
			});
			this.addChild(menuItems.del);

			menuItems.properties = new MenuItem({
				label: this.rfe.translator.trans("properties"),
				onClick: lang.hitch(this.rfe, this.rfe.showFileProperties),
				disabled: true
			});
			this.addChild(menuItems.properties);

		},

		_openMyself: function() {
			var self = this;
			var prevArgs = arguments;

			// note: handle enabling of contextmenu items after selecting and not on mousedown since we need to know if an item is selected or deselected
			this.enableMenuItems(this.rfe.context);

			array.forEach(this.contextualMenus, function(tempMenuItem){
				self.removeChild(tempMenuItem);
			});
			this.contextualMenus = [];

			var selection = this.rfe.context.isOnGrid ? this.rfe.grid.selection : this.rfe.tree.dndSource.selection;

			if(Object.keys(selection).length == 1) {
				var ms = new MenuSeparator();
				self.addChild(ms);
				self.contextualMenus.push(ms);

				var filePath = Object.keys(selection)[0];
				this.rfe.coolFileRepo.getContextualMenuFor( filePath ).then(function(contextMenu){

					self.createMenu(contextMenu.children, self, {
						repository : self.rfe.coolFileRepo,
						filePath : filePath
					});

					self.inherited('_openMyself', prevArgs);
				});
			} else self.inherited('_openMyself', prevArgs);
		},

		createMenu: function(items, menu, functionContext) {

			if(items == undefined || items.length == 0) {
				return;
			}

			for(var i = 0; i<items.length; i++) {
				var menuItemClass = MenuItem;

				var prefix = items[i]['icon'] ? "<img src='"+items[i]['icon']+"' class='icon'>&nbsp;" : '';
				var o = {
					label: prefix + items[i].label,
					//iconClass: "dijitEditorIcon dijitEditorIconSave",
					onClick: this.createFunction( items[i].onClick, false, functionContext )
				};

				if(items[i].children && items[i].children.length > 0) {
					var subMenu = new Menu({});
					this.createMenu(items[i].children, subMenu, functionContext);
					o.popup = subMenu;

					menuItemClass = PopupMenuItem;
				}

				var menuItem = new menuItemClass(o);

				menu.addChild(menuItem);
				menu.contextualMenus.push(menuItem);
			}

		},

		createFunction: function (js, raw, preserveObjects) {

			if(js instanceof Function)
				return js;

			//necessary because if declared without eval, closure compiler modifies the name of the variables, which would later be unaccessible in the created function!
			preserveObjects = preserveObjects || {};

			//use this temporary variable to safely store the content of the preserveObjects object
			this['_preserveObjects'] = preserveObjects;
			eval('var preserveObjects = this[\'_preserveObjects\'];');

			for(var objectName in preserveObjects) {
				eval('var '+objectName+' = preserveObjects.'+objectName+';');
			}

			if(js!=undefined) {
				if(raw) {
					eval('var f = '+js);
					return eval('f');
				} else return function() {
					return eval('(function() {'+js+'}());');
				}
			}
			return function() {};
		},


		/**
		 * Enables or disables context menu items depending on the context.
		 * @param {object} context
		 */
		enableMenuItems: function(context) {
			var id, selected = false,
				selection = context.isOnGrid ? this.rfe.grid.selection : this.rfe.tree.dndSource.selection;

			// set file properties menu depending on if at least one file object is selected
			if (selection && context.isOnGrid) {
				// note: disable here if not in selection
				for(id in selection) {
					selected = true;
					break;
				}
				this.menuItems.rename.set('disabled', !selected);
			}

			this.menuItems.del.set('disabled', !selected);
			this.menuItems.properties.set('disabled', !selected);
		}

	});
});