define([
	'dojo/_base/lang',
	'dojo/_base/declare',
	'dijit/Menu',
	'dijit/MenuItem',
	'dijit/MenuSeparator',
	'dijit/PopupMenuItem'
], function(lang, declare, Menu, MenuItem, MenuSeparator, PopupMenuItem) {

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

		postCreate: function() {
			this.inherited('postCreate', arguments);

			var menu, menuItems = this.menuItems = {};

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

			this.addChild(new MenuSeparator());
			menuItems.properties = new MenuItem({
				label: this.rfe.translator.trans("properties"),
				onClick: lang.hitch(this.rfe, this.rfe.showFileProperties),
				disabled: true
			});
			this.addChild(menuItems.properties);

		},

		_openMyself: function() {
			// note: handle enabling of contextmenu items after selecting and not on mousedown since we need to now if an item is selected or deselected
			this.enableMenuItems(this.rfe.context);
			this.inherited('_openMyself', arguments);
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