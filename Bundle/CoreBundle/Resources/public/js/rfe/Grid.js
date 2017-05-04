define([
	'dojo/_base/lang',
	'dojo/_base/declare',
	'dojo/on',
	'dojo/aspect',
	'dojo/topic',
	'dojo/query',
	'dgrid/OnDemandGrid',
	'dgrid/Selection',
	'dgrid/editor',
	'dgrid/Keyboard',
	'dgrid/extensions/DnD',
	'dgrid/extensions/ColumnResizer',
	'dgrid/extensions/ColumnHider',
	'cool/rfe/grid/View'
], function(lang, declare, on, aspect, topic, query, Grid, Selection, editor, Keyboard, Dnd, ColumnResizer, ColumnHider, View) {

	/**
	 * @class cool.rfe.Grid
	 * @extends {OnDemandGrid} Grid
	 * @extends {dgrid/Selection} Selection
	 * @extends {dgrid.editor} editor
	 * @extends {dgrid/Keyboard} Keyboard
	 * @extends {dgrid.extensions.DnD} DnD
	 * @extends {dgrid.extensions.ColumnResizer} ColumnResizer
	 * @property {string} selectionMode
	 * @property {string} allowSelectAll
	 * @property {object} columns
	 */
	return declare([Grid, Selection, editor, Keyboard, Dnd, ColumnResizer, ColumnHider, View], /** @lends rfe.Grid.prototype */ {

		selectionMode: 'extended',
		allowSelectAll: true,
		maintainOddEven: false,
		showHeader: false,
		cleanEmptyObservers: false,	// otherwise adding to empty grid will not show added in grid

		columns: {},

		postCreate: function() {
			this.inherited('postCreate', arguments);
			// prevent bubbling of double click on editor content to allow selecting of words,
			// otherwise on a folder object its contents would be loaded and displayed instead.
			aspect.after(this, 'edit', function(promise) {
				promise.then(function(widget) {
					if (!widget.signal) {
						widget.signal = on(widget, 'dblclick', function(evt) {
							evt.stopPropagation();
						});
					}
				});
			});
			this.bodyNode.tabIndex = this.tabIndex;

			topic.subscribe('grid/views/state', lang.hitch(this, function(view) {
				this.set('view', view);
                this.resize();
			}));
			this.set('showHeader', true);	// if headers are re-rendered this taken care of in setView
			this.set('view', this.view);
		},

		/**
		 * Returns the first row object or false if none.
		 * A row object has the properties:
		 *      id: the data object's id
		 *      data: the data object represented by the row
		 *      element: the row's DOM element
		 * @return {object}
		 */
		_getFirstRow: function() {
			var nodes = query('.dgrid-row', this.bodyNode);
			return nodes.length > 0 ? this.row(nodes[0]) : false;
		}
	});
});