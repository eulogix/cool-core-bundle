define("cool/lister",
        [
            "cool/cool",
            "cool/_widget",
            "cool/functions/popup",

            "dojo/_base/lang",
            "dojo/_base/array", // array.filter array.forEach array.map
            "dojo/_base/declare",
            "dojo/_base/event",
            "dojo/Deferred",
            "dojo/promise/all",
            "dojo/dom-construct",
            "dojo/dom-class",
            "dojo/dom-style",
            "dojo/dom-geometry",
            "dojo/date",
            "dojo/date/stamp",
            "dojo/window",
            "dojo/request/xhr",
            'dojo/dnd/Target',
            'dojo/dnd/Source',
            "dojo/on",

            "cool/store/xhrstore",
            "cool/store/lister",
            "dojo/store/Observable",
            "gridx/Grid",
            "gridx/core/model/cache/Sync",
            "gridx/core/model/cache/Async",
            
            "gridx/modules/Edit",
            "gridx/modules/Filter",
            "gridx/modules/filter/FilterBar",
            "gridx/support/QuickFilter",
            "gridx/modules/SingleSort",
            "gridx/modules/NestedSort",
            "gridx/modules/IndirectSelect",
            "gridx/modules/extendedSelect/Row",
            //"gridx/modules/select/Row",
            "gridx/modules/RowHeader",
            "gridx/modules/IndirectSelectColumn",
            //"gridx/modules/VirtualVScroller",
            "gridx/modules/Pagination",
            "gridx/modules/pagination/PaginationBar",

            "gridx/modules/ColumnResizer",
            "gridx/modules/HScroller",
            "gridx/modules/CellWidget",
            "gridx/modules/ToolBar",
            "gridx/modules/Tree",
            "gridx/modules/move/Row",
            "gridx/allModules",

            "gridx/support/Summary",

            'dijit/ProgressBar',
            "dijit/Tooltip",
            "dijit/form/Button",
            "dijit/form/DropDownButton",
            "dijit/Menu",
            "dijit/MenuItem",
            "dijit/PopupMenuItem",
            "dijit/layout/BorderContainer",
            "dijit/layout/ContentPane",

            "dojo/fx",
            "dojo/fx/Toggler",

            "dojox/fx",
            "dojox/fx/_core",
            "dojox/fx/scroll",
            "dojox/fx/Shadow",

            "cool/dijit/Button",
            "cool/controls/textbox",
            "cool/controls/JSONEditor",
            "cool/controls/HTMLEditor",
            "cool/controls/tab",
            "cool/controls/select",
            "cool/controls/textarea",
            "cool/controls/button",
            "cool/controls/hidden",
            "cool/controls/checkbox",
            "cool/controls/datetime",

            "cool/gridx/Summary",
            "cool/gridx/modules/DndRow",

            "cool/_listerModelMixin",
            "cool/_listerEditorMixin",
            "cool/_listerLayoutMixin",
            "cool/_listerTimelineMixin"

        ], 
        function(cool, cwidget, coolPopup,
                 lang, array, declare, Event, Deferred, all, domConstruct, domClass, domStyle, domGeometry, date, stamp, win, xhr, dndTarget, dndSource, on,
                 xhrStore, listerStore, Observable, Grid, Sync, ASync,
                 Edit, Filter, FilterBar, QuickFilter, SingleSort, NestedSort, IndirectSelect, selectRow, rowHeader, IndirectSelectColumn, /*VirtualVScroller,*/ Pagination, PaginationBar,
                 ColumnResizer, HScroller, CellWidget, Toolbar, Tree, moveRow, allModules,
                 Summary,
                 ProgressBar, Tooltip, Button, DropDownButton, Menu, MenuItem, PopupMenuItem, BorderContainer, ContentPane,
                 Dfx, Toggler,
                 Fx, FxCore, Scroll, Shadow,
                 iconButton, CoolTextbox, CoolJSONEditor, CoolHTMLEditor, CoolTab, CoolSelect, CoolTextarea, CoolButton, CoolHidden, CoolCheckbox, CoolDateTime,
                 SummaryModule, dndRow,
                 _listerModelMixin, _listerEditorMixin, _listerLayoutMixin, _listerTimelineMixin
               ){
  
            return declare("cool.lister", [cwidget, _listerModelMixin, _listerEditorMixin, _listerLayoutMixin, _listerTimelineMixin], {

                extFilterDivId : false, //if set, the filter will be rendered at this dom id

                editorFormDivId : false, //if set, the editor form will be rendered at this dom id

                inEditRowId : null,

                store : {},

                maxHeight: 400,

                exportButton: {},

                constructor: function() {
                    //this.inherited(arguments); // builds definition

                    this.grid = {}; //link to gridx instance, per-instance object
                    this.filterForm = {}; //link to filter form, per-instance object
                    this.editorForm = {}; //link to editor widget, per-instance object
                    this.store = {}; //link to gridx store
                    this.exportButton = {};
                },

                /**
                 * the lister always propagates the selected Ids, and the last dojo request
                 * @return dojo.Deferred
                 */
                getActionValues: function() {
                    var deferred = new Deferred();
                    deferred.resolve({
                        _recordIds:              JSON.stringify(this.getSelectedIds()),
                        _lastDojoStoreRequest:   JSON.stringify(this.store.getLastRequest())
                    });
                    return deferred;
                },

                postCreate: function(){
                    this.inherited(arguments);
                },

                onBindSuccess: function( data ) {
                    return this.inherited(arguments); // builds definition
                },

                /**
                 * this function can be overridden in derived classes to provide a different rendering
                 * mechanism for some slots
                 */
                _skipNormalSlotRender: function(slotName) {
                    return slotName == 'filterSlot' || this.inherited(arguments);
                    //return ret;
                },

                openFilterForm: function() {
                    var lister = this;
                    var d = new Deferred();

                    this._buildFilterDiv();

                    if(this.hasSlot('filterSlot')) {

                        this._putSlot(this._getSlotDefinitionByName('filterSlot'), 'filterSlot', lister.filterDiv, function(filterForm){

                            filterForm.onlyContent = true;

                            filterForm.on('filterLinkedLister', function() {
                                lister.applyFilter();
                                lister.reloadRows();
                            });

                            lister.filterForm = filterForm;
                            filterForm.lister = lister;

                            filterForm.on('fields_loaded', function(){ d.resolve(filterForm); });

                        });

                    } else d.resolve(false);

                    return d;
                },

                applyFilter: function() {
                    //appends the current value of the form to the lister store, so it will be propagated with every request
                    //this is useful for implementing custom logic on the server side accessing individual filter form fields
                    this.store.postVars['_filter_raw_values'] = JSON.stringify(this.filterForm.getValues());

                    //_query is a JSON representation of a filter object, identical to the ones generated by gridx filter* modules (bar, quickfilter)
                    //but generated on the server by the filterForm
                    var query = this.filterForm.getDefinitionAttribute('_query');

                    if(query) {
                        this.store.postVars['_query'] = query;
                    } else delete this.store.postVars['_query'];
                },

                _buildFilterDiv: function() {
                    if(this.extFilterDivId) {
                        this.filterDiv = dojo.byId(this.extFilterDivId);
                    }

                    if(!this.filterDiv) {
                        this.filterDiv = dojo.doc.createElement('div');
                        domClass.add(this.filterDiv, "filterNode");
                        domConstruct.place(this.filterDiv, this.actionsNode, "first"); //attached to the actions node means that the actions relative to the lister are BELOW the filter
                    }

                    //hack, refactor this
                    this.filterDiv.innerHTML ='';
                },

                /**
                * destroys the widgets
                * 
                */
                clear: function() {

                    if(this.grid.destroyRecursive!=undefined) {
                        this.grid.destroyRecursive();  
                    }

                    if(this.editorForm.clear!=undefined) {
                        this.editorForm.clear();  
                    }

                    if(this.filterForm.clear!=undefined) {
                        this.filterForm.clear();  
                    }
                    
                    //todo: clear this stuff    
                    this.filterForm = {}; //link to gridx instance, per-instance object
                    this.editorForm = {}; //link to gridx instance, per-instance object
                    this.grid = {}; //link to gridx instance, per-instance object

                    
                    this.inherited(arguments);
                },



                rebuild: function() {
                    var lister = this;
                    var widget = this;

                    var minHeight = 100;

                    var ret = new Deferred();
                    var parentPromise = this.inherited(arguments).promise;
                    var selfD = new Deferred();

                    //delete multiple?
                    if(this.canDeleteMultiple()) {
                        var deleteMultipleButton = new Button({
                            label: this.getCommonTranslator().trans("DELETE SELECTED"),
                            onClick: function() {
                                var ids = lister.getSelectedIds();
                                if( (ids.length > 0) && confirm(lister.getCommonTranslator().trans("CONFIRM DELETE MULTIPLE"))) {
                                    widget.callAction('deleteRows', null, {ids:ids.join(',')} );
                                }
                                //lister.reloadRows();
                            },
                            iconClass: "dijitIconDelete"
                        });
                        this.getToolbar().addChild(deleteMultipleButton);
                    }

                    //various export options
                    if(this.getDefinitionAttribute('export_xlsx')) {
                        var exportMenu = new Menu({});

                        if(this.getDefinitionAttribute('export_xlsx')) {

                            exportMenu.addChild(new MenuItem({
                                label: '<img src="/bower_components/fugue/icons/table-excel.png" class="icon">&nbsp;'+this.getCommonTranslator().trans("XLSX DECODED"),
                                //iconClass: "dijitEditorIcon dijitEditorIconSave",
                                onClick: function() {
                                    lister.exportButton.set("disabled", true);
                                    lister.getActionValues().then(function(av){
                                        widget.callAction('export',
                                            function(data){
                                                lister.exportButton.set("disabled", false);
                                                lister.parseData(data);
                                            },
                                            lang.mixin({ format:'xlsx' }, av),
                                            { dontLock : true }
                                        );
                                    });
                                }
                            }));
                        }

                        var exportButton = new DropDownButton({
                            label: this.getCommonTranslator().trans("EXPORT"),
                            optionsTitle: "Save Options",
                            iconClass: "dijitIconTable",
                            dropDown: exportMenu,
                            style: {"float":"right"}
                        });

                        this.addActionButton('EXPORT', exportButton, 'TITLE');
                        this.exportButton = exportButton;
                    }

                    //button to invalidate grid cache
                    if(true) {
                        this.addAction('CLEAR_CACHE', {
                            label: this.getCommonTranslator().trans("CLEAR CACHE"),
                            onClick: function() {
                                lister.reloadRows();
                            },
                            icon: '/bower_components/fugue/icons/arrow-circle.png',
                            group:'TITLE'
                        });
                    }


                    this.openFilterForm().then(function(filterForm) {

                        var gridStore = lister._createStore();

                        if(filterForm)
                            lister.applyFilter();

                        var layout = lister.getLayout();
                        var initialOrder = lister.getInitialOrder();

                        var gridModules = [
                            Filter,
                            NestedSort,
                            rowHeader,
                            IndirectSelect,
                            selectRow,
                            Pagination,
                            PaginationBar,
                            ColumnResizer,
                            HScroller,
                            CellWidget,
                            Edit,
                            Tree,
                            SummaryModule
                        ];

                        if(lister.showToolbar())
                            gridModules.push( Toolbar );

                        if(lister.canReorderRows() || lister.canMoveElementsInTree() || lister.canDeleteMultiple())
                            gridModules.push( dndRow );

                        var grid = new Grid({
                            cacheClass: ASync,
                            store: gridStore,
                            structure: layout,

                            selectRowTreeMode: false, //does not select parent rows
                            selectRowTriggerOnCell: false, /* was false prior to dnd */

                            filterBarMaxRuleCount: Infinity,

                            style: "height:"+minHeight+"px; width:100%;",

                            pageSize: 30,

                            autoWidth: false,

                            filterServerMode: true,

                            //filters the object that comes from listerBar, quickfilter...
                            filterSetupQuery: function(obj){
                                return JSON.stringify(obj); //let the server process the JSON tree of filter conditions
                            },

                            sortInitialOrder: initialOrder,

                            indirectSelectAll:true,

                            modules: gridModules

                        });

                        grid.edit.connect(grid.edit, "onApply", function(cell, applySuccess) {
                            if(lister.store.lastStatus) {
                                domClass.toggle( cell.node(), "listerCellModified", true);
                            } else {
                                //there is an error, but maybe not on lister cell
                                domClass.toggle( cell.node(), "listerCellPending", true);
                                lister.displayRowErrors(cell.row);
                            }
                        });


                        grid.connect(grid.body, 'onRender', function(start, count) {
                            lister._refreshGridVisuals();
                        });

                        grid.connect(grid.tree, 'onExpand', function(id) {
                        });

                        grid.connect(grid.tree, 'onCollapse', function(id) {
                        });

                        grid.connect(grid.body, 'onEmpty', function() {
                        });

                        //applies custom styles to rows
                        grid.connect(grid.body, 'onAfterRow', function(row){
                            //var r = row.data();
                            var rowData = row.rawData();
                            var meta = rowData._meta || {};
                            var node = row.node();

                            if(meta.rowClass) {
                                domClass.remove(node, "gridxRowOdd");
                                domClass.toggle(node, meta.rowClass, true);
                            }

                            if(meta.cellClass) {
                                var cells = dojo.query('.gridxCell', node);
                                var c = cells.length;

                                for(var i = 0; i < c; i++){
                                    domClass.toggle( cells[i], meta.cellClass, true);
                                }
                            }
                        });

                        grid.connect(grid.model, 'onSizeChange', function(newSize) {
                            lister._refreshSize();
                        });

                        lister.grid = grid;
                        grid.lister = lister;

                        lister.setEventManager('dataChanged', function() {
                            lister.reloadRows();
                        });

                        grid.connect(grid.select.row,"onSelectionChange", function(){
                            lister.emit('selectionChange');
                        });

                        var div = dojo.doc.createElement('div');
                        grid.placeAt( div );
                        grid.startup();

                        lister.registerViewWithButton('grid', function(){
                            return div;
                        }, '/bower_components/fugue/icons/application-form.png');

                        lister.setUpTimelineView();

                        lister.setActiveView('grid');

                        var initialSelection = lister.getDefinitionAttribute("initial_selection");
                        if(initialSelection) {
                            var c = grid.connect(grid.body, 'onRender', function(start, count) {
                                c.remove();
                                //the small delay is to ensure that the lister is able to initialize and then fire a selectionChange event
                                setTimeout(function(){ lister.setSelectedIds(initialSelection); },100);
                            });
                        }

                        lister.emit('loadComplete');

                        grid.connect(grid, "onModulesLoaded", function() {
                            selfD.resolve();
                        });
                    });


                    all({
                        parent: parentPromise,
                        self: selfD.promise
                    }).then(function(results){
                        ret.resolve();
                    });

                    return ret;
                }, //rebuild

                _createStore: function(postVars) {
                    var lister = this;

                    var url = Routing.generate('_lister_store', this.getActionParameters({serverId:this.serverId}));

                    lister.store = new Observable(
                        new listerStore({
                            target: url,
                            postVars: postVars || {},

                            widget: lister
                        })
                    );

                    return lister.store;
                }
                        
        });
  
});
