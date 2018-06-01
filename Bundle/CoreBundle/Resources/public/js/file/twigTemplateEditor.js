define("cool/file/twigTemplateEditor",
	[
    "dojo/_base/lang",
    "dojo/_base/declare",
    "dojo/_base/array",
    "dojo/dom",
    "dojo/dom-style",
    "cool/form",
    "dijit/_TemplatedMixin",
    'dijit/_WidgetsInTemplateMixin',
    'gridx/Grid',
    'gridx/modules/select/Cell',
    "gridx/modules/CellWidget",
    "gridx/modules/Tree",
    'dojo/store/Memory',
    'dojo/store/util/QueryResults',
    'dojo/store/util/SimpleQueryEngine',
    "dojo/text!./templates/twigTemplateEditor.html"

], function(lang, declare, array, dom, domStyle,
            form, _TemplatedMixin, _WidgetsInTemplateMixin,
            Grid, selectCell, CellWidget, Tree,
            Memory, QueryResults, SimpleQueryEngine
            , template) {
 
    return declare("cool/file/twigTemplateEditor", [form, _TemplatedMixin, _WidgetsInTemplateMixin], {

        templateString: template,

        sampleData: null,

        postCreate: function() {
            this.inherited(arguments);
            domStyle.set(this.sidePane.domNode, 'display', 'none');
        },

        _setSampleDataAttr: function(sampleData) {
            this.getField('sampleData').set('value', JSON.stringify(sampleData));
            this.sampleData = sampleData;
            this._buildDataPicker(sampleData);
        },

        _buildDataPicker: function(data) {
            var t = this;

            var layout = [
                {id: 'name', field: 'name', name: 'Name'},
                {id: 'value', field: 'value', name: 'Value',
                    widgetsInCell: true,
                    setCellValue: function(gridData, storeData, cellWidget) {
                        var rawData = cellWidget.cell.row.rawData();
                        cellWidget.domNode.innerHTML =
                            rawData.value ? (rawData.value.constructor === Array ? '' : rawData.value) : '';
                    }
                }
            ];

            var store = this._buildStore(this._linearizeData(data));

            var grid = new Grid({
                store: store,
                structure: layout,
                autoHeight: false,
                style: "height: 80%;",
                modules: [
                    selectCell,
                    CellWidget,
                    Tree
                ]
            });

            //ensures that only one cell is selected
            dojo.connect(grid.select.cell, "onSelected", function(cell){
                var selected = grid.select.cell.getSelected();
                for(var i=0;i<selected.length;i++) {
                    var rowId = selected[i][0];
                    var col = selected[i][1];
                    if((rowId != cell.row.id) || (col != cell.column.id))
                        grid.select.cell.deselectById(rowId, col);
                }
                t.getField('template').insertAtCursor(rowId);
            });

            this.own(grid);
            domStyle.set(this.sidePane.domNode, 'display', null);
            this.sidePane.domNode.appendChild(grid.domNode);
            grid.startup();
        },

        _buildStore: function(data) {

            var memoryStore =  new Memory({data:data, idProperty:'id'});

            memoryStore.hasChildren = function(id, item){
                return item && item.value && (item.value.constructor === Array) && item.value.length > 0;
            };

            memoryStore.getChildren = function(item, options){
                return QueryResults(SimpleQueryEngine(options.query, options)(item.value));
            };

            return memoryStore;
        },

        _linearizeData: function(data, root) {
            root = root || '';

            if(data === Object(data)) {
                var ret = [];
                for(var k in data) {
                    if(data.hasOwnProperty(k)) {
                        var value = this._linearizeData(data[k], root + k + '.');
                        ret.push({id: '{{ '+ root + k + ' }}', name: k, value:value})
                    }
                }
                return ret;
            }
            return data;
        },

        resize: function() {
            this.bc1.resize();
            this.inherited(arguments);
        },

        _onPreviewShow: function() {
            var t = this;
            if(this.sampleData)
                this.callAction('GetPreview', function(data) {
                   t.previewPane.set('content', data.preview);
                });
            else t.previewPane.set('content', this.getField('template').get('value'));
        }

    });
 
});