define([
	'dojo/_base/lang',
	'dojo/_base/declare',
	'dojo/cookie',
	"dojo/_base/array",
	'dojo/dom-construct',
	'dojo/query',
	'cool/rfe/Tree',
	'cool/rfe/Grid',
	'cool/rfe/dnd/GridSource',
	'dijit/registry',
	'dijit/form/CheckBox',
	'dijit/Dialog',
	'cool/rfe/layout/Toolbar',
	'cool/rfe/layout/Panes',
	'cool/rfe/ContextMenu',
	'cool/rfe/config/fileObject',
	"dgrid/editor"
], function(lang, declare, cookie, array, domConstruct, query, Tree, Grid, GridSource,
				registry, CheckBox, Dialog, Toolbar, Panes, ContextMenu, fileObject, editor) {

	/**
	 * @class
	 * @name rfe.Layout
	 * @property {rfe.layout.Panes} panes
	 * @property {rfe.layout.Toolbar} toolbar
	 * @property {rfe.Grid} tree
	 * @property {dijit.Tree} grid
	 */
	return declare(null, /** @lends rfe.Layout.prototype */ {

		_cnDialogSettingsFolderState: 'DialogSettingsFolderState', // name of cookie

		panes: null,
		toolbar: null,

		tree: null,
		grid: null,


        settingsDialog : null,

		/** @constructor */
		constructor: function(props) {
			lang.mixin(this, props || {});
		},

        resize: function() {
            this.inherited(arguments);
            this.panes.resize();
            this.grid.resize();
        },

		init: function() {
            var t = this;
			this.panes = new Panes({
				view: 'horizontal',
                style: "width: 100%; height: 100%;"
			}, this.domNode);

			this.toolbar = new Toolbar({
				rfe: this,
				tabIndex: 10
			}, domConstruct.create('div'));

			this.panes.treePane.set('tabIndex', 20);
			this.panes.gridPane.set('tabIndex', 30);
			this.toolbar.placeAt(this.panes.menuPane.domNode);
			this.panes.startup();
            this.own(this.panes);

			setTimeout(function() {
                t.editContextMenu = new ContextMenu({
                    rfe: t,
                    targetNodeIds: [t.panes.gridPane.id]
                });
				t.resize(); //sometimes needed because the UI may look screwed on the first initialization of RFE
            }, 1000);

			this.initTree();
			this.initGrid();
			this.initDialogs();
		},

		/**
		 * Initializes the tree and tree dnd.
		 */
		initTree: function() {
			this.tree = new Tree({
				rfe: this,
				model: this.store,
				childrenAttrs: [this.store.childrenAttr],
				persist: cookie(this._cnDialogSettingsFolderState) || true
			});
			this.tree.placeAt(this.panes.treePane);
            this.own(this.tree);
		},

		_getGridColumns: function() {

			var columns = {
				name: editor({
					editor: 'textarea',
					editOn: 'dummyEvent',	// lets have an editor, but do not turn it on
					sortable: true, // lets us apply own header click sort
					autoSave: false,
					label: this.translator.trans(fileObject.label.name)//,
					//width:700 -- no width makes this expand
				}),
				ext: {
					sortable: true, // lets us apply own header click sort
					label: this.translator.trans(fileObject.label.ext),
					width:70
				},
				size: {
					sortable: true, // lets us apply own header click sort
					label: this.translator.trans(fileObject.label.size),
					formatter: fileObject.formatter.size,
					width:100
				},
				dir: {
					sortable: true, // lets us apply own header click sort
					label: this.translator.trans(fileObject.label.dir),
					formatter: fileObject.formatter.dir,
					width:70
				},
				cre: {
					sortable: true, // lets us apply own header click sort
					label: this.translator.trans(fileObject.label.cre),
					formatter: fileObject.formatter.cre,
					width:120
				},
				mod: {
					sortable: true, // lets us apply own header click sort
					label: this.translator.trans(fileObject.label.mod),
					formatter: fileObject.formatter.mod,
					width:120
				}
			};

			return columns;
		},

		_refreshGridColumns: function(path, isSearch) {

			var self = this;
			path = path || this.currentPath;
			isSearch = isSearch || false;

			var columns = self._getGridColumns();

			if(isSearch) {

				columns['parId'] = {
					sortable: true,
					field: 'parId',
					label: self.translator.trans("parent_folder"),
					formatter: function(object){
						return '<span style="cursor:pointer; text-decoration: underline; color: #0f6ab4;">'+ object +'</span>';
					}
				};

				self.grid.set('columns', columns);
				self.grid.set('view', self.grid.view);

				if(self.onClickParId)
					self.onClickParId.remove();

				self.onClickParId = self.grid.on(".dgrid-content .dgrid-cell:click", function (evt) {
					var cell = self.grid.cell(evt);
					var file = cell.row.data;

					if(cell.column.field == "parId") {
						self.displayFile(file);
					}

				});

			} else {

				this.coolFileRepo.getAvailableProperties(path).then(function(properties){

					array.forEach(properties, function(property){
						if(property.showInList)
							columns[property.name] = {
								sortable: true, // lets us apply own header click sort
								label: self.translator.trans("property_" + property.name),
								width:100,
								get: function(object){
										if(lang.exists("dec_"+property.name, object))
											return object["dec_"+property.name];
										return object[property.name];
									}
								}
					});

					self.grid.set('columns', columns);
					self.grid.set('view', self.grid.view);
				});
			}
		},

		initGrid: function() {
			var self = this;
			var div = domConstruct.create('div', null, this.panes.gridPane.domNode);

			this.grid = new Grid({
				rfe: this,
				columns: this._getGridColumns(),
				tabIndex: 31,
				view: this.defaultView,
				store: null, // store is set in FileExplorer.initState()
				dndConstructor: GridSource,	// dgrid/extension/dnd can't be overridden directly
				dndParams: {
					accept: ['treeNode'],
					rfe: this
				}
			}, div);

			this.grid.on("dgrid-sort", function (event) {
				// Stop the normal sort event/bubbling
				event.preventDefault();
				event.stopPropagation();

				self.grid.set('sort', [
					event.sort[0],
					{ attribute : 'dir', descending : false }
				]);
			});

            this.own(this.grid);
		},

		initDialogs: function() {

			// TODO: move to dialogs.js
			var self = this;

			this.settingsDialog = new Dialog({
				title: "Settings",
				content: '<div>Not implmented yet' +
				'<fieldset><legend>Navigation Pane (Folders)</legend></fieldset>' +
				'</div>'
			});

			// TODO: move dialog creation to constructor/init so we can use cookie also to set store on first display
			var label = domConstruct.create('label', {
				innerHTML: 'Remember folders state'
			}, query('fieldset', this.settingsDialog.domNode)[0], 'last');

			domConstruct.create('br', null, label);
			var input = domConstruct.create('input', null, label, 'first');
			new CheckBox({
				checked: cookie(this._cnDialogSettingsFolderState) || true,
				disabled: 'disabled',
				onChange: function() {
					self.tree.set('persist', this.checked);
					cookie(this._cnDialogSettingsFolderState, this.checked);
				}
			}, input);

			label = domConstruct.create('label', {
				innerHTML: 'Show folders only'
			}, query('fieldset', this.settingsDialog.domNode)[0], 'last');

			input = domConstruct.create('input', null, label, 'first');
			new CheckBox({
				checked: true,
				disabled: 'disabled',
				onClick: function() {
					self.store.skipWithNoChildren = this.checked;
					self.reload();
				}
			}, input);
		},

		showDialogSettings: function() {
            this.settingsDialog.show();
		},

		/**
		 * retrieves permissions and settings for the current folder/view state
		 * and updates visual elements accordingly
		 */
		_refreshVisuals: function() {
			var t = this;
			this.coolFileRepo.getPermissions(this.currentPath).then(function(permissions){
				t.toolbar.uploadButton.set("disabled", !permissions.canCreateFileIn);
			});
		}

	});
});