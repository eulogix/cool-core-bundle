define("cool/controls/listPicker",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "dojo/dom-construct",
    "dojo/dom-style",
    "dojo/aspect",
    "dojo/on",

    'gridx/Grid',
    "dijit/registry",

    'gridx/core/model/cache/Sync',
    'gridx/core/model/cache/Async',

    'dojo/store/Memory',
    'gridx/allModules',
    'gridx/modules/select/Row',
    "gridx/modules/SingleSort",

    "cool/cool",
    "cool/controls/_control",

    'dijit/_TemplatedMixin',
    'dijit/_WidgetsInTemplateMixin',

    "dojo/text!./templates/listPicker.html"

], function(declare, lang, array, domConstruct, domStyle, aspect, on,
            Grid, registry, SyncCache, AsyncCache,

            memoryStore,
            mods, SelectRow, SingleSort,

            cool, _control, _TemplatedMixin, _WidgetsInTemplateMixin, widgetTpl) {
 
    return declare("cool.controls.listPicker", [ _control, _TemplatedMixin, _WidgetsInTemplateMixin ], {

        templateString: widgetTpl,

        constructor: function() {
            this.inherited(arguments);
            this.grid1 = {};
            this.grid2 = {};
            this.data = [];
        },

        coolInit : function() {
            this.inherited(arguments);

            this.maindiv.style.cssText = this.cssStyles.join(';');

            var options = this.getParameter('options');

            for(var rowId in options) {
                this.data.push(lang.mixin({id:rowId}, options[rowId]));
            }

            this.grid1 = this._buildGrid();
            this.grid1.placeAt(this.grid1div, "first");
            this.grid1.startup();

            this.grid2 = this._buildGrid();
            this.grid2.placeAt(this.grid2div, "first");
            this.grid2.startup();

            if(this.definition.hasOwnProperty('value')) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }

            if(this.isReadOnly()) {
                domStyle.set(this.grid1div, "display", "none");
                domStyle.set(this.grid2div, "width", "100%");
                domStyle.set(this.buttonAdd, "display", "none");
            }
        },

		_setValueAttr: function(value) {
            this._setRawValueAttr(JSON.parse(value) || []);
		},

        _setRawValueAttr: function(value) {
            var selectedIds = value || [];

            this.grid1.store.setData([]);
            this.grid2.store.setData([]);

            for(var i=0;i<this.data.length;i++) {
                var row = this.data[i];
                if(selectedIds.indexOf(row.id) >= 0)
                    this.grid2.store.put(row);
                else this.grid1.store.put(row);
            }

            this._refreshGrids();
        },

		_getValueAttr: function() {
            var allItems = this.grid2.store.query();
            var ret = [];
            for(var i=0;i<allItems.length;i++) {
                ret.push(allItems[i].id);
            }
            return JSON.stringify( ret );
		},

        moveToFirstList: function() {
            this._moveItems(this.grid2, this.grid1);
        },

        moveToSecondList: function() {
            this._moveItems(this.grid1, this.grid2);
        },

        _moveItems: function(sourceGrid, targetGrid) {

            var rowID;
            var selectedIds = sourceGrid.select.row.getSelected();
            for(var i=0;i<selectedIds.length;i++) {
                rowID = selectedIds[i];
                var row = sourceGrid.row(rowID).rawData();
                targetGrid.store.add(row);
                sourceGrid.store.remove(rowID);
            }
            this._refreshGrids();
        },

        _refreshGrids: function() {
            this.grid1.model.clearCache();
            this.grid1.body.refresh();

            this.grid2.model.clearCache();
            this.grid2.body.refresh();
        },

        _buildGrid: function() {

            var store = new memoryStore({
                data: []
            });

            var layout = [];

            var columnWidth = Math.floor( 100 / Object.keys(this.data[0]).length)+'%';
            for(var field in this.data[0]) {

                var mix = {};
                if( lang.exists(field, this.definition.columnLayouts) )
                    eval( 'mix = {'+this.definition.columnLayouts[field]+'};' );

                layout.push(
                    lang.mixin({
                        id: field,
                        field: field,
                        name: field,
                        title: this.getContainerWidget().getTranslator().trans(field),
                        sortable: true,
                        width: columnWidth
                    }, mix));
            }

            var grid = Grid({
                cacheClass: SyncCache,
                store: store,
                structure: layout,
                style: 'width:100%; height: 100%;',
                modules: [
                    SelectRow,
                    SingleSort
                ],
                selectRowTriggerOnCell:true
            });

            setTimeout(function(){ grid.resize(); }, 200);

            this.own(grid);

            return grid;
        }

    });
 
});