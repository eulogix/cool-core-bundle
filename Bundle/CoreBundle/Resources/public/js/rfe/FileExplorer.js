/**
 * A module that creates an application that allows you to manage and browse files and directories on a remote web server.
 * It consists of tree and a grid. The tree loads file data over REST via php from remote server.
 * @module FileExplorer cool/rfe/FileExplorer
 */
define([
	'dojo/_base/lang',
	'dojo/_base/declare',
	"dojo/_base/array",
	'dojo/Deferred',
	'dojo/when',
	'dojo/dom',
	'dojo/dom-class',
	'dojo/on',
	'dojo/query',
	'dojo/Stateful',
	'dijit/registry',
	'cool/rfe/_Base',
	'cool/rfe/Layout',
	'cool/rfe/History',
	'cool/rfe/Edit',
	'cool/rfe/store/FileStore',
	'cool/rfe/dialogs/dialogs',
	'cool/rfe/Keyboard',
	'cool/rfe/dnd/Manager',	// needs to be loaded for dnd
	"cool/file/fileUtils",
	"cool/dialog/manager",
	"cool/util/UrlUtils",
	"dojox/widget/Standby"
], function(lang, declare, array, Deferred, when, dom, domClass, on, query, Stateful,
				registry, _Base, Layout, History, Edit, FileStore, dialogs, Keyboard, Manager, fileUtils, dialogManager, UrlUtils, Standby) {

	/*
	 *	@class cool/rfe/FileExporer
	 *	@extends {cool/rfe/Layout}
	 * @mixes {cool/rfe/Edit}
	 * @property {string} version
	 * @property {string} versionDate
	 * @property {dojo/Stateful} currentTreeObject keeps track of currently selected store object in tree. Equals always parent of grid items
	 * @property {dojo/Stateful} context keeps track of widget the context menu was created on (right clicked on)
	 * @config {boolean} isOnGridRow
	 * @config {boolean} isOnTreeRow
	 * @config {boolean} isOnGridContainer
	 * @config {boolean} isOnTreeContainer
	 * @property {object} history
	 * @config {array} steps saves the steps
	 * @config {int} curIdx index of current step we're on
	 * @config {int} numSteps number of steps you can go forward/back
	 * @property {cool/rfe/storstore/FileStore} store
	 *
	 */
	return declare([_Base, History, Layout, Edit, Keyboard], {
		version: '0.9',
		versionDate: '2014',
		currentTreeObject: null,
		context: null,
		store: null,

        storeUrl: '/',

        currentPath : '',

        storeParameters: {},

        translator: {},

		coolFileRepo: {},

		defaultView: 'icons',

		constructor: function() {
			// TODO: should tree connect also on right click as grid? If so, attache event to set currentTreeItem
			this.currentTreeObject = new Stateful();	// allows Toolbar and Edit to keep track of selected object in tree

			this.store = new FileStore({
                storeUrl: this.storeUrl,
				rfe: this
            });

			this.context = {
				isOnGridRow: false,
				isOnGridContainer: false,
				isOnGrid: false,
				isOnTreeRow: false,
				isOnTreeContainer: false,
				isOnTree: false
			};
		},

        postCreate: function() {
            this.inherited(arguments);
            this.init();
            this.initEvents();
        },

		initEvents: function() {
			var self = this,
				grid = this.grid,
				tree = this.tree,
				store = this.store;

			on(this.panes.domNode, '.rfeTreePane:mousedown, .rfeGridPane:mousedown', function(evt) {
				self.set('context', evt, this);
			});

			tree.on('click', function(object) {	// when calling tree.on(click, load) at once object is not passed
				when(self.displayChildrenInGrid(object), function() {	// use when since dfd might already have resolved from previous click
					self.currentTreeObject.set(object);
					self.currentPath = object.id;
					self.set('history', object.id);
                });
			});

			tree.on('load', function() {
				self.initState().then(function() {

					grid.on('.dgrid-row:click, .dgrid-row:dblclick', function(evt) {
						var object = grid.row(evt.target).data;

						switch(evt.type) {
							case 'dblclick':
								//builds an array of siblings to allow for gallery visualization of all the pictures in a folder
								var nodes = query('.dgrid-row', grid.bodyNode);
								var siblings = [];
								array.forEach(nodes, function(node){
									var rowObject = grid.row(node).data;
									if(rowObject.dir !== true && (rowObject.size > 0))
										siblings.push({
											'serveUrl' : UrlUtils.addParams(self.serviceServe, {'filePath' : rowObject.id}),
											'downloadUrl' : UrlUtils.addParams(self.serviceDownload, {'filePath' : rowObject.id}),
											'ext' :	rowObject.ext || '',
											'title' : rowObject.name
										});
								});

								if (object.dir) {
									self.display(object).then(function() {
										self.set('history', object.id);
									});
								}
								else {
									fileUtils.previewOrDownload(
										UrlUtils.addParams(self.serviceServe, {'filePath' : object.id}),
										UrlUtils.addParams(self.serviceDownload, {'filePath' : object.id}),
										object.ext || '',
										object.name,
										siblings
									);
								}
								break;
							case 'click':
								self.set('history', object.id);
								break;
						}

					});

					// catch using editor when renaming
					grid.on('dgrid-datachange', function(evt) {

						if(evt.cell.column.field == 'name') {
							var newName = evt.value;
							if(!newName.match(/^[^\r\n\.]+\.[^\r\n\.]+$/im) &&
							   !confirm(self.translator.trans("CONFIRM_RENAME_BAD_NAME %name%", {"name":newName}))) {
									event.preventDefault();
									return false;
								}
							/* since a name change may result in the id of the file to change, we must signal a deletion
							 * followed by an insertion
							 * */
						}

						var obj = evt.cell.row.data;
						var oldId = obj.id;
						obj[store.labelAttr] = evt.value;

						store.put(obj).then(function(newId) {

							self.reload();
							//grid.save();
						}, function(error){
							self.reload();
						});
					});
				});
			});
		},

		/**
		 * Displays folder content in grid.
		 * @param {Object} object dojo data object
		 * @return {dojo.Deferred}
		 */
		displayChildrenInGrid: function(object) {
			var self = this,
				grid = this.grid,
				store = this.store,
				dfd = new Deferred();

			if (object.dir) {
				self._lockForServerCall();
				when(store.getChildren(object), function() {
					on.once(grid, "dgrid-refresh-complete", function() {
						dfd.resolve(object);
						self._refreshGridColumns();
						self._refreshVisuals();
						self._unlockFromServerCall();
					});
					var sort = grid.get('sort');
					grid.set('query', {parId: object.id}, {sort: sort});
				});
			}
			else {
				dfd.resolve(object);
			}
			return dfd;
		},

		/**
		 * Displays the store object (folder) in the tree and it's children in the grid.
		 * The tree and the grid can either be in sync meaning that they show the same content (e.g. tree folder is expanded)
		 * or the grid is one level down (e.g. tree folder is selected but not expanded).
		 * @param {Object} object store object
		 * @return {dojo/Deferred}
		 */
		display: function(object) {
			var path, dfd = new Deferred();
			var dfd2 = new Deferred();
			var self = this;

			if (object.dir) {
				self.currentPath = object.id;
				//changed, this used the Memory store, but had problems with the search box
				this.store.storeMaster.getPath(object).then(function(path){
					dfd = self.tree.set('path', path);
				});
			}
			else {
				dfd.reject(false);
			}

			dfd.then(lang.hitch(this, function() {
				this.displayChildrenInGrid(object).then(function(){ dfd2.resolve(object); });
			}));

			this.currentTreeObject.set(object);

			//TODO check this way of chaining deferreds, may leave something hanging
			return dfd2;
		},

		/**
		 * opens the parent folder of a file and highlights the file in the reloaded grid
		 * @param file
		 */
		displayFile: function(file) {
			var rfe = this;
			when(rfe.store.get(file.parId), function(object) {
				rfe.display(object).then(function() {
					var grid = rfe.grid,
						row = grid.row(file.id),
						cell = grid.cell(file, 'name');
					grid.select(row);
					grid.focus(grid.cellNavigation ? cell : row);
				});
			});
		},

		/**
		 * Display parent directory.
		 * @param {Object} [object] dojo.data.object
		 */
		goDirUp: function(object) {
			var def;
			if (!object) {
				object = this.currentTreeObject;
			}
			if (object.parId) {
				def = when(this.store.get(object.parId), lang.hitch(this, function(object) {
					return this.display(object);
				}), function(err) {
					console.debug('Error occurred when going directory up', err);
				});
			}
			else {
				def = new Deferred();
				def.resolve(false);
			}
			return def;
		},

        reload: function() {
            //var dndController = this.tree.dndController.declaredClass;
            this.currentPath = this.currentTreeObject.id;
            this.store.childrenCache = {};
            this.store.storeMemory.setData([]);
			this.grid.refresh();
			this.initState();
        },

		search: function(query) {
			var self = this;
			var grid = this.grid;

			query = query || this.searchBox.get('value');

			self._lockForServerCall();

			this.store.query({_search:'1', searchDir:this.currentPath, extended_query:query}).then(function(data){
				self.store.childrenCache = {};
				self.store.storeMemory.setData(data);
				self._unlockFromServerCall();
				self._refreshVisuals();

				on.once(grid, "dgrid-refresh-complete", function() {
					self._refreshGridColumns(self.currentPath, true);
				});

				var sort = grid.get('sort');
				grid.set('query', {}, {sort: sort});
			});
		},

		_lockForServerCall: function() {
			if(!this._standby) {
				var standby = new Standby({
					target: this.domNode,
					duration: 50,
					color:'white'
				});
				document.body.appendChild(standby.domNode);
				standby.startup();
				this._standby = standby;
			}

			this._standby.show();
		},

		_unlockFromServerCall: function() {
			if(this._standby) {
				this._standby.hide();
			}
		},

        /**
		 * Reload file explorer.
		 */
		oldReload: function() {
			//window.location.reload();
			// TODO: only reload files and folders

			//var dndController = this.tree.dndController.declaredClass;
			this.store.storeMemory.setData([]);
			this.grid.refresh();

			// reset and rebuild tree
			//this.tree.dndController = null;
			this.tree._itemNodesMap = {};
			this.tree.rootNode.destroyRecursive();
			this.tree.rootNode.state = "UNCHECKED";
			//this.tree.dndController = dndController; //'rfe.dnd.TreeSource',
			this.tree.postMixInProperties();
		//	this.tree._load();
			this.tree.postCreate();
			this.initState();


		},

		/**
		 * Set object properties describing on which part of the file explorer we are on.
		 * @param {Event} evt
		 * @param {HTMLElement} node
		 */
		_setContext: function(evt, node) {
			var widget = registry.getEnclosingWidget(evt.target),
				isGridRow = this.grid.row(evt) !== undefined,
				isTreeRow = widget && widget.baseClass === 'dijitTreeNode';

			node = node || widget.domNode;

			this.context = {
				isOnGridRow: isGridRow,
				isOnGridContainer: domClass.contains(node, 'rfeGridPane') && !isGridRow,
				isOnTreeRow: isTreeRow,
				isOnTreeContainer: widget && widget.baseClass === 'dijitTree'
			};

			this.context.isOnGrid = this.context.isOnGridRow || this.context.isOnGridContainer;
			this.context.isOnTree = this.context.isOnTreeRow || this.context.isOnTreeContainer;
		},

		/**
		 * Initializes the default or last state of the tree and the grid.
		 * Expects the tree to be loaded and expanded otherwise it will be set to root, then displays the correct folder in the grid.
		 */
		initState: function() {

			var self = this, arr, id,
				tree = this.tree,
				grid = this.grid,
				store = this.store,
				file = false,
				path = this.currentPath,
				paths,
				dfd = new Deferred();

			// id form url, can either be a file or a folder
			if (path !== '') {
				// load all parent paths since path can also just be an id instead of a full hierarchical file path
				dfd = when(store.get(path), function(object) {
					return store.storeMaster.getPath(object).then(function(paths) {
						if (object.dir) {
							id = object.id;
						}
						else {
							file = object;
							id = object.parId; // display parent folder of file in tree
							paths.pop();
						}
						return [paths];
					});
				});
			}
			// id from cookie
			else {
				paths = this.tree.loadPaths();
				if (paths.length > 0) {
					// we only use last object in array to set the folders in the grid (normally there would be one selection only anyway)
					arr = paths[paths.length - 1];
					id = path = arr[arr.length - 1];
				}
				// use tree root
				else {
					id = tree.rootNode.item.id;
				}
				dfd.resolve(paths);
			}

			// expand all paths
			dfd = dfd.then(function(paths) {
				return tree.set('paths', paths);
			});
			// get object from id of last item in path
			dfd = dfd.then(function() {
				return when(store.get(id), function(object) {
					self.set('history', path);
					self.currentTreeObject.set(object);
					self.context.isOnTreeRow = true;
					return object;
				});
			});
			// get objects children and display them in grid
			dfd = dfd.then(function(object) {
					return when(store.getChildren(object), function() {	// load children puts them in the cache, then set grid's store
						var row, cell;
						// Setting caching store for grid would not use cache, because cache.query() always uses the
						// master store => use storeMemory.
						grid.set('store', store.storeMemory, { parId: id });
						self._refreshGridColumns();
						self._refreshVisuals();
						if (file) {
							row = grid.row(file.id);
							cell = grid.cell(file, 'name');
							grid.select(row);
							grid.focus(grid.cellNavigation ? cell : row);
						}

						grid.set('sort', [{'attribute':'cre', 'descending': true}]);
					});
			});

			return dfd;
		},

		showFileDetails: function() {
			// Note: A visible file/folder object is always loaded
			var dialog, id, store = this.store,
				i = 0, len,
				widget = this.context.isOnGrid ? this.grid : this.tree,
				selection = widget.selection;

			// TODO: if multiple selected file objects, only use one dialog with multiple values (and sum of all file sizes). Requires preloading folder contents first!
			// grid
			if (selection) {
				for (id in selection) {
					if (selection[id] === true) {
						dialog = dialogs.getByFileObj('fileProperties', store.get(id));
						dialog.show();
					}
				}
			}
			// TODO: extend dijit.tree._dndSelector to work same way as grid.selection ? so we don't need to differentiate here
			// tree
			else if (widget.selectedItems) {
				len = widget.selectedItems.length;
				for (i; i < len; i++) {
					dialog = dialogs.getByFileObj('fileProperties', widget.selectedItems[i]);
					dialog.show();
				}
			}
		},

		showFileProperties: function() {

			var rfe = this;
			// Note: A visible file/folder object is always loaded
			var dialog, id, store = this.store,
				i = 0, len,
				widget = this.context.isOnGrid ? this.grid : this.tree,
				selection = widget.selection;

			// TODO: if multiple selected file objects, only use one dialog with multiple values (and sum of all file sizes). Requires preloading folder contents first!
			// grid
			var selectedIds = [];
			if (selection) {
				for (id in selection) {
					if (selection[id] === true) {
						selectedIds.push(id);
					}
				}
				var d = dialogManager.openWidgetDialog('EulogixCoolCore/Core/Files/FilePropertiesForm', 'PROPERTIES', lang.mixin({
					filePaths: selectedIds.join(';')
				}, rfe.commonParameters), null, null, null, {
					w: 800,
					h: 500
				});
				d.rfe = rfe;

			}
			// TODO: extend dijit.tree._dndSelector to work same way as grid.selection ? so we don't need to differentiate here
			// tree
			else if (widget.selectedItems) {
				len = widget.selectedItems.length;
				for (i; i < len; i++) {

					var d = dialogManager.openWidgetDialog('EulogixCoolCore/Core/Files/FilePropertiesForm', 'PROPERTIES', lang.mixin({
						folder: rfe.currentTreeObject.id,
						filePath: widget.selectedItems[i]
					}, rfe.commonParameters), null, null, null, {
						w: 800,
						h: 500
					});
					d.rfe = rfe;
				}
			}
		},

        resize: function() {
            this.inherited(arguments);
            try { this.grid.resize(); } catch(e) {}
        }


	});

});