define([
	'dojo/_base/declare',
	'dojo/on',
    "dojo/dom-style",
	'dgrid/Grid',
	'put-selector/put',
	"cool/util/UrlUtils"
], function(declare, on, domStyle, Grid, put, UrlUtils) {

	return declare(null, {

		/** default view */
		view: 'list',

		/** width of icons */
		iconWidth: 80,

		/**
		 * Object providing the different render functions for the rows.
		 */
		rowRenderers: {
			list: Grid.prototype.renderRow,
			icons: function(obj) {
				var div, img, parent;

				img = this.get('iconType', obj);
				parent = put('div', img);
				div = put(parent, 'div', {columnId: 'name'});
                domStyle.set(parent, {'vertical-align':'top'});
				this.cellRenderers.icons.name(obj, obj.name, div);
				return parent;
			}
		},

		/**
		 * Object providing the different render functions for the cells.
		 */
		cellRenderers: {
			// note: a cell is only the part of a row with editable text and contains no elements
			list: {
				name: function(obj, data, containerEl) {
					containerEl.innerHTML = '<span class="' + (obj.dir ? 'dijitFolderClosed' : 'dijitLeaf') + '"></span><span>' + obj.name + '</span>';
				}
			},
			icons: {
				name: function(obj, data, containerEl) {
                    containerEl.innerHTML = '<div style="overflow:hidden; width: 80px; word-break: break-all;">' + obj.name + '</div>';
				}
			}
		},

		/**
		 * Object providing the different render functions for the headers.
		 */
		headerRenderers: {
			list: function() {
				this.renderHeaderList();
			},
			icons: function() {
				this.renderHeaderIcons();
			}
		},

		/**
		 * Re-render headings and rows
		 * @param {String} view
		 */
		_setView: function(view) {
			this.hiderToggleNode.style.display = view !== 'list' ? "none" : "";
			this._destroyColumns();
			this.set('renderer', view);
			this.view = view;
			this._updateColumns();
			this.refresh();
		},

		/**
		 * Return the icon according to provided mime type of object.
		 * @param {Object} obj
		 * @returns {HTMLImageElement}
		 */
		_getIconType: function(obj) {
			var mime, mimeExt, img = put('img'); /*, {
				width: 64,
				height: 64
			});*/
            if(obj.iconSrc) {
                img.className = 'iconImage';
                img.src = obj.iconSrc;
                img.removeAttribute('width');
                img.removeAttribute('height');
                return img;
            }

			mime = obj.mime ? obj.mime.split('/')[0] : null;
			mimeExt = obj.mime ? obj.mime.split('/')[1] : null;

			switch (mime) {
                case 'application':
                    if(mimeExt!='pdf')
                        img.src = require.toUrl('cool/rfe/resources/images/icons-64/file-text.png');
                    else
                        img.src = require.toUrl('cool/rfe/resources/images/icons-64/pdf.png');
                    break;
				case 'image':
					if(mimeExt != 'tiff' && mimeExt != 'tif'){
                        img.className = 'iconImage';
                        img.src = UrlUtils.addParams(self.servicePreview, {'filePath' : obj.id});
                        //img.width = this.iconWidth;
                        img.removeAttribute('width');
                        img.removeAttribute('height');
					} else {
                        img.src = require.toUrl('cool/rfe/resources/images/icons-64/file-image.png');
					}

					break;
                case 'video':
					img.src = require.toUrl('cool/rfe/resources/images/icons-64/file-video.png');
					break;
				case 'audio':
					img.src = require.toUrl('cool/rfe/resources/images/icons-64/file-audio.png');
					break;
				case 'text':
					img.src = require.toUrl('cool/rfe/resources/images/icons-64/file-text.png');
					break;
				default:
					img.src = require.toUrl('cool/rfe/resources/images/icons-64/'+ (obj.dir ? 'folder.png' : 'file.png'));
			}
			return img;
		},

		/**
		 * Set renderers for headers, rows and cells depending on view
		 * @param {String} view
		 */
		_setRenderer: function(view) {
			var prop, cssClassRemove = '',
				cssClass = 'gridView' + view.charAt(0).toUpperCase() + view.slice(1);

			// update class on grid domNode for correct row css
			for (prop in this.rowRenderers) {
				if (this.rowRenderers.hasOwnProperty(prop)) {
					cssClassRemove += '!gridView' + prop.charAt(0).toUpperCase() + prop.slice(1);
				}
			}
			this.renderHeader = this.headerRenderers[view];
			this.renderRow = this.rowRenderers[view];

			// set correct cell renderer for current view
			for (prop in this.cellRenderers[view]) {
				if (this.cellRenderers[view].hasOwnProperty(prop)) {
					this.columns[prop].renderCell = this.cellRenderers[view][prop];
				}
			}

			// Should keyboard navigation occur at row or cell level?
			// @see dgrid/Keyboard.js
			this.cellNavigation = view === 'list';

			put(this.domNode, "!gridViewList!gridViewIcons!gridViewDetails." + cssClass);
		},

		/**
		 * Render headers when in icon mode.
		 */
		renderHeaderIcons: function() {
			var headerNode = this.headerNode,
				i = headerNode.childNodes.length;

			headerNode.setAttribute("role", "row");

			// clear out existing header in case we're resetting (changing view)
			while(i--){
				put(headerNode.childNodes[i], "!");
			}
		},

		/**
		 * Render column headers when in list mode.
		 */
		renderHeaderList: function() {
			// Note: overriding to be able to manipulate sorting, when clicking on header
			var grid = this, headerNode;

			//target = grid._sortNode;	// access before sort is called, because Grid._setSort will delete the sort node
			this.inherited('renderHeader', arguments);

			headerNode = this.headerNode;

			// if it columns are sortable, add events to headers
			/*on(headerNode.firstChild, 'click, keydown', function(event) {

				// respond to click or space keypress
				if (event.type === "click" || event.keyCode === 32) {
					var target = event.target, field, arrSort;

					do {
						if (target.field) {	// true = found the right node
							// stash node subject to DOM manipulations to be referenced then removed by sort()
							grid._sortNode = target;
							field = target.field || target.columnId;
							arrSort = grid._sort;
							return grid.set("multisort", field, arrSort);
						}
					}
					while ((target = target.parentNode) && target !== headerNode);
				}
			});*/
		},

		/**
		 * Override to make cell work with other views than lists (table)
		 * @param {Event} target event, node, object id
		 * @param {Integer} [columnId] column id
		 * @returns {Object}
		 */
		cell: function(target, columnId) {
			var cell = this.inherited('cell', arguments);

			// note: cell is also called when clicking on the grid, but not on a row, e.g. cell is an empty object
			if (this.view === 'icons' && this.row(target)) {
				cell.element = cell.element || this.row(target).element.getElementsByClassName('field-name')[0];
			}
			return cell;
		},

		_getEditableElement: function(id, columnId) {
			var cell = this.cell(id, columnId);

/*
			if (this.view === 'icons') {
				cell.element.contents = cell.element.getElementsByTagName('span')[0];
			}
			else {
				cell.element.contents = cell.element.getElementsByTagName('span')[1];
			}
*/

			return cell;
		}

	})
});