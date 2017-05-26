define("cool/_listerModelMixin",
        [
            "cool/cool",
            "cool/_widget",
            "cool/functions/popup",

            "dojo/_base/lang",
            "dojo/_base/array", // array.filter array.forEach array.map
            "dojo/_base/declare"
        ], 
        function(cool, cwidget, coolPopup,
                 lang, array, declare
               ){

            /**
             * this mixin provides an implementation independent model Api
             */
            return declare("cool._listerModelMixin", [], {


                setSelectedIds: function(ids) {
                    this.grid.select.row.clear();
                    for(var i = 0; i<ids.length; i++)
                        if(this.grid.row(ids[i], true) !== null)
                            this.grid.select.row.selectById(ids[i]);
                },

                getSelectedIds: function() {
                    return this.grid.select.row.getSelected();
                },

                getSelectedRows: function() {
                    var t = this;
                    var ids = this.getSelectedIds();
                    var ret = [];
                    array.forEach(ids, function(id){
                        ret.push(t.getRow(id));
                    });
                    return ret;
                },

                getRow: function(rowId) {
                    return this.grid.row(rowId).rawData();
                },

                deleteRow: function(rowID){
                    var parametersToPropagate = lang.mixin({_recordid:rowID}, this._getRequestParametersToPropagate(rowID));

                    if(confirm(this.getCommonTranslator().trans("CONFIRM DELETE ROW")))
                        this.callAction('deleteRow', null, parametersToPropagate );
                },

                scrollToRow: function(rowID) {
                    try{
                        var rowVisualIndex = this.grid.row(rowID).visualIndex();
                        //this.grid.vScroller.scrollToRow(rowVisualIndex);
                    } catch(e) {}
                },

                /**
                 * determines which fields and/or parameters have to be propagated (used to initialize the editor widget)
                 * for a given row
                 * @param rowID
                 * @private
                 */
                _getRequestParametersToPropagate: function(rowID) {

                    var record = this._getRowRecord(rowID);
                    var propagatedData = rowID ? {_recordid: rowID} : {};

                    var propFields = this.getDefinitionAttribute('propagated_fields') || {};
                    for(var colName in record) {
                        if(lang.exists(colName, propFields))
                            propagatedData[ propFields[colName].as ] = record[colName];
                    }

                    var editorWidgetParameters = lang.mixin({}, this.getCleanDefinitionParameters(), propagatedData);

                    return editorWidgetParameters;
                },

                _getRowRecord: function(rowID) {
                    var record = {};
                    try {
                        //sometimes rowID can be passed as parameter from other widgets,
                        //and the row may not be in cache
                        record = this.grid.row(rowID).rawData();
                    } catch(e) {
                        record = {};
                    }
                    return record;
                },

                _getColumnLayout: function(columnName, columnDefinition) {
                    var cLayout;
                    var lister = this;

                    //when the column has no widgets in it, the cell data is passed along
                    var withoutWidgetsDecorator = function(cellData, rowID, index){
                        var rowData = lister._getRowRecord(rowID);

                        var decodedValue = lang.exists('_decodifications.'+columnName, rowData) ? rowData['_decodifications'][columnName] : null;

                        if(columnDefinition.cellTemplateJs) {
                            var source   = columnDefinition.cellTemplateJs;
                            var template = Handlebars.compile(source);
                            var templateVars = lang.mixin({
                                        _listerId: lister.id,
                                        _containerId: lister.getContainer() ? lister.getContainer().id : null,
                                        _value: cellData,
                                        _decodedValue: decodedValue,
                                        _parameters: lister.getCleanDefinitionParameters()
                                    },
                                    rowData);
                            return template(templateVars);
                        }

                        return decodedValue || cellData;
                    };

                    /**
                     * otherwise things are more complex as the widgets have to be reused
                        @see  https://github.com/oria/gridx/wiki/How-to-show-widgets-in-gridx-cells%3F
                     */
                    var withWidgetsDecorator = function(){

                        if(columnDefinition.dijitWidgetTemplate) {
                            var source   = columnDefinition.dijitWidgetTemplate;
                            var template = Handlebars.compile(source);
                            var templateVars = {
                                _listerId: lister.id
                            };
                            if(lister.getContainer())
                                templateVars['_containerId'] = lister.getContainer().id;

                            return template(templateVars);
                        }

                        return "SET A WIDGET TEMPLATE!";
                    };

                    var withWidgets = columnDefinition.dijitWidgetTemplate != null;

                    cLayout = {
                        id:columnName,
                        style: columnDefinition.columnStyleCss || "",
                        name: columnDefinition.label,   //header text
                        field: columnName,  //link to the store
                        width: columnDefinition.width || "100px",
                        editable: columnDefinition.editable,
                        sortable: columnDefinition.sortable !== false,
                        editor: columnDefinition.control ? columnDefinition.control.coolDojoWidget : null,

                        editorArgs: {
                            toEditor: function (storeData, gridData, cell, editor) {

                                editor.parameters = {
                                    definition: columnDefinition.control
                                };
                                editor.definition = columnDefinition.control;

                                editor.coolInit();
                                setTimeout( function() { editor.select(); }, 100);

                                //the returned value is set as value to the editor
                                return storeData;
                            }
                        },

                        widgetsInCell: withWidgets,

                        decorator: withWidgets ? withWidgetsDecorator : withoutWidgetsDecorator
                    };

                    if(columnDefinition.setValueJs) {
                        cLayout.setCellValue = function (gridData, storeData, cellWidget) {
                            var rowIndex = cellWidget.cell.row.index();
                            var rowData = lister.grid.row(rowIndex).rawData();
                            var decodedValue = lang.exists('_decodifications.' + columnName, rowData) ? rowData['_decodifications'][columnName] : null;

                            var staticTemplateOutput = decodedValue || rowData[columnName];
                            if(columnDefinition.cellTemplateJs) {
                                //TODO: not sure if passing rowIndex is correct, maybe we have to pass rowData._recordid
                                staticTemplateOutput = withoutWidgetsDecorator(gridData, rowIndex);
                            }
                            var f = lister.createFunction(columnDefinition.setValueJs, false, {
                                lister:lister,
                                gridData:gridData,
                                storeData:storeData,
                                cellWidget:cellWidget,
                                rowIndex: rowIndex,
                                rowData: rowData,
                                decodedValue: decodedValue,
                                staticTemplateOutput: staticTemplateOutput
                            });

                            f();
                        };
                    }

                    return cLayout;
                },

                _showToolsColumn: function() {
                    return this.getDefinitionAttribute('show_tools_column') !== false;
                },

                getLayout: function() {

                    var layout = [];
                    
                    var lister = this;
                    
                    if(this._showToolsColumn()) {
                        layout.push({id: '_tools', name: '', width:"100px",
                            decorator: function(cellData, rowID, index){
                                var rowData = lister.grid.row(rowID).rawData();
                                var meta = rowData._meta || {};

                                var buttons = [];
                                if((meta.canDeleteRecord !== false) && !lister.isReadOnly())
                                    buttons.push('<A HREF="javascript:dijit.byId(\''+ lister.id +'\').deleteRow(\''+rowID+'\');"><img src=/bower_components/fugue/icons/minus-button.png></A>');
                                if(meta.canEditRecord !== false)
                                    buttons.push('<A HREF="javascript:dijit.byId(\''+ lister.id +'\').openRowEditor(\''+rowID+'\');"><img src=/bower_components/fugue/icons/layer--pencil.png></A>');

                                return buttons.join('&nbsp;')
                            },

                            filterable: false,
                            sortable: false

                        });
                    }

                    var dc = this.definition.columns;
                    for(var columnName in dc) {
                        layout.push(this._getColumnLayout( columnName, this.definition.columns[columnName] ));
                    }

                    return layout;

                },

                rebuildNeeded: function(definition) {
                    return this.inherited(arguments) ||
                           definition.hasOwnProperty('columns');
                },

                /**
                 * produces an array compatible with gridx, from the lister definition
                 */
                getInitialOrder: function () {
                    var ret = [];
                    var defSort = this.getDefinitionAttribute('initial_sort');
                    for(var columnId in defSort) {
                        ret.push({
                            colId: columnId,
                            descending: defSort[columnId] == 'desc'
                        });
                    }
                    return ret;
                },

                _getSystemMenuItems: function() {
                    var p = this.inherited(arguments);
                    var widget = this;
                    if(lang.exists('definition.attributes._configurable', this) && this.definition.attributes._configurable) {
                        //default standard button
                        p.push({
                            label: this.getCommonTranslator().trans("EDIT LISTER"),
                            onClick: function() {
                                var popup = coolPopup.open(Routing.generate('_coolListerEditor'), "editorWindow_"+widget.definition.attributes.id, "directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes", 1000, 650, "CENTER");
                                window.widgetInEdit = widget;
                            },
                            icon: '/bower_components/fugue/icons/table.png'
                        });
                    }
                    return p;
                },

                reloadRows: function() {
                    var t = this;
                    var oldSelection = this.getSelectedIds();
                    if(this.grid.model) {
                        this.grid.model.clearCache();
                        //keep the old postVars
                        var oldPostVars = lang.clone(this.grid.store.postVars);
                        delete this.grid.model.store;
                        delete this.grid.store;
                        var store = this._createStore(oldPostVars);
                        this.grid.model.setStore(store);
                        this.grid.store = store;
                        this.grid.body.refresh();

                        var c = t.grid.connect(t.grid.body, 'onRender', function(start, count) {
                            c.remove();
                            t.setSelectedIds(oldSelection);
                        });

                    }
                    this.emit('reloadRows');
                },

                reloadRow: function(rowId) {
                    var grid = this.grid;
                    var store = this.grid.store;
                    rowId+="";
                    //reload a row from the store and notify the cache (this does not trigger server side operations)
                    store.get(rowId).then(function( updatedRowObject ) {
                        store.notify(updatedRowObject, rowId);
                        grid.model.clearCache();
                        grid.body.refresh();
                    });
                },

                getColumnParameter: function(columnName, parameter) {
                    var colDef = this.getColumnDefinition(columnName);
                    if(colDef) {
                        if(lang.exists('parameters.'+parameter, colDef))
                            return colDef.parameters[parameter];
                    }
                    return null;
                },

                getColumnDefinition: function(columnName) {
                    if(lang.exists('definition.columns.'+columnName, this))
                        return this.definition.columns[columnName];
                    return null;
                },

                hideDndTargets: function() {
                    if(this.dndTargetsDiv)
                        dojo.destroy(this.dndTargetsDiv);
                },

                canDeleteMultiple: function() {
                    return this.getDefinitionAttribute('delete_multiple') && !this.isReadOnly();
                },

                canMoveElementsInTree: function() {
                    return this.getDefinitionAttribute('move_elements_in_tree') && !this.isReadOnly();
                },

                canReorderRows: function() {
                    return this.getDefinitionAttribute('reorder_rows') && !this.isReadOnly();
                },

                showToolbar: function() {
                    return this.getDefinitionAttribute('show_toolbar');
                }
                        
        });
  
});
