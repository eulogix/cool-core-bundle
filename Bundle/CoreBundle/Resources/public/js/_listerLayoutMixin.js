define("cool/_listerLayoutMixin",
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
            "cool/fx/rollover",
            "cool/functions/waiter",
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
            "dijit/Tooltip"
        ],
        function(cool, cwidget, coolPopup,
                 lang, array, declare, Event, Deferred, all, domConstruct, domClass, domStyle, domGeometry, date, stamp, win, xhr, dndTarget, dndSource, on,
                 xhrStore, listerStore, coolRollover, coolWaiter, Observable, Grid, Sync, ASync,
                 Edit, Filter, FilterBar, QuickFilter, SingleSort, NestedSort, selectRow, rowHeader, IndirectSelectColumn, /*VirtualVScroller,*/ Pagination, PaginationBar,
                 ColumnResizer, HScroller, CellWidget, Toolbar, Tree, moveRow, allModules,
                 Summary,
                 ProgressBar, Tooltip
               ){
  
            return declare("cool._listerLayoutMixin", [], {

                refresh: function() {
                    this.grid.body.refresh();
                },

                getRowHeight: function() {
                    if(firstRow = this.grid.body.getRowNode({visualIndex:0})) {
                        var contentBox = domGeometry.getContentBox(firstRow);
                        return contentBox.h;
                    }
                    return 1;
                },

                _getCalculatedHeightExceptBody: function() {
                    var t = this.grid.vLayout,
                        freeHeight = 0,
                        hookPoint, n;

                    for(hookPoint in t._mods){
                        n = t.grid[hookPoint];
                        if(n){
                            freeHeight += n.offsetHeight;
                        }
                    }
                    return freeHeight + 2;
                },

                /**
                 * Adjusts the size of the lister to match the number of rows to be shown
                 * @private
                 */
                _refreshSize: function() {

                    var lister = this;

                    var finalSize = {};
                    var rowHeight, rowsNodeGeo, rowsPerPage, rowCount, rowsAvailableHeight;

                    coolWaiter.waiter(
                        function() {},

                        //wait until we have a reliable number of rows and the lister is actually visible
                        function() {
                            if(lister.grid.rowCount instanceof Function) {
                                rowCount = lister.grid.rowCount();
                                rowsNodeGeo = domGeometry.getContentBox(lister.grid.body.domNode);
                                rowHeight = lister.getRowHeight();
                                //if rowsPerPage is NaN, the lister must be in the background so we want to fire this handler as soon as it gets rendered
                                //TODO: check a reliable method to determine if the lister is visible, now disabled
                                return rowsNodeGeo.w > 0 && rowCount != -1 && (rowHeight > 1 || rowCount == 0);
                            }
                            return false;
                        },

                        function() {
                            lister._recalculateWidths();

                            var freeHeight = lister._getCalculatedHeightExceptBody();

                            if(lister.maxHeight > 0) {

                                var totalCalcRowHeight = Math.ceil(rowHeight * rowCount);

                                finalSize = { h:Math.min(
                                    lister.maxHeight,
                                    lister.minHeight > 0
                                        ? Math.max(lister.minHeight, freeHeight + totalCalcRowHeight)
                                        : freeHeight + totalCalcRowHeight
                                )};

                                rowsAvailableHeight = finalSize.h - freeHeight;

                            } else {
                                //if fillContent is true, the filter is rendered in the actions div
                                var filterBox = lister.extFilterDivId || lister.fillContent ? {h:0} : domGeometry.getContentBox(lister.filterDiv);
                                var contentBox = domGeometry.getContentBox(lister.contentNode);
                                finalSize = {h: contentBox.h - filterBox.h};

                                rowsAvailableHeight = finalSize.h - freeHeight - filterBox.h;
                            }

                            if(JSON.stringify(finalSize) !== JSON.stringify(lister._lastFinalSize||{})) {
                                lister._lastFinalSize = finalSize;
                                lister.grid.resize(finalSize);
                            }

                            rowsPerPage = rowHeight > 1 ? Math.max( 5, Math.round(rowsAvailableHeight / rowHeight)) : 5;

                            if(rowsPerPage != lister._lastRowsPerPage) {
                                lister._lastRowsPerPage = rowsPerPage;
                                lister.grid.pageSize = rowsPerPage+1; //to have the store querying at most this amount of records

                                lister.grid.pagination.setPageSize(rowsPerPage);
                                lister.grid.paginationBar.sizes = [rowsPerPage, rowsPerPage*2, rowsPerPage*4];
                                lister.grid.paginationBar.refresh();
                            }
                        }
                    );
                },

                /**
                 * refreshes misc visual cues based on the grid status
                 *
                 * @private
                 */
                _refreshGridVisuals: function() {
                    var lister = this;
                    var grid = this.grid;

                    //refresh the row that is currently in edit (if any)
                    if(lister.inEditRowId) {
                        var row = grid.row(lister.inEditRowId+'');
                        if(row && row.node()) {
                            domClass.toggle(row.node(), "rowInEdit", true);
                            domClass.toggle(row.node(), "gridxRowOdd", false);
                        }
                    }
                },

                _recalculateWidths: function() {

                    //bit of a hack TODO: find a way to get the real width of headers including the padding
                    var padding = 6;

                    var t = this;
                    var header = this.grid.header;
                    var dc = this.definition.columns;
                    var contentBox = domGeometry.getContentBox(this.grid.body.domNode);

                    //subtract space for a vertical scrollbar
                    var totWidth = contentBox.w - 20;
                    var totalDefinedWidth = 0;
                    var cols = this.grid.columns();

                    var colsToResize = [], colsWithUndefinedWidth = [];

                    array.forEach(cols, function(col) {
                        if(dc[ col.id ] && !dc[ col.id ].fixedWidth) {
                            var definedWidth = dc[col.id].width+'';
                            if(definedWidth.match(/^[0-9]+(px|)$/im)) {
                                colsToResize.push({
                                    id: col.id,
                                    definedWidth: parseInt(definedWidth)
                                });
                                totalDefinedWidth += parseInt(definedWidth);
                            } else colsWithUndefinedWidth.push(col);
                        } else {
                            //the column is not one defined by the server, so it is assumed to be
                            //of fixed width (tools, select box...)
                            realColBox = domGeometry.getContentBox(header.getHeaderNode(col.id));
                            totWidth -= (padding*2) + realColBox.w;
                        }
                    });

                    /*console.log('total actual width:' + totWidth);
                    console.log('total defined width:' + totalDefinedWidth);
                    console.log(colsToResize);*/

                    array.forEach(colsToResize, function(colToResize) {
                        var colWidth = Math.floor(totWidth * colToResize.definedWidth / totalDefinedWidth) - (padding*2);
                        if(colWidth >= colToResize.definedWidth)
                            t.grid.columnResizer.setWidth(colToResize.id, colWidth+'px');
                    });

                    //no columns have defined widths, so we split the available space between them all
                    if(totalDefinedWidth == 0) {
                        array.forEach(colsWithUndefinedWidth, function(colToResize)  {
                            var colWidth = Math.floor(totWidth / colsWithUndefinedWidth.length) - (padding*2);
                            t.grid.columnResizer.setWidth(colToResize.id, colWidth+'px');
                        });
                    }

                },

                refreshAfterDelete: function () {

                    var deletedRows = this.getDefinitionAttribute("deletedRows");

                    if(deletedRows) {
                        for(var i in deletedRows) {
                            var recordId = deletedRows[i]+"";
                            console.log(recordId);
                            //delete the row from the store
                            this.grid.store.notify(undefined, recordId);
                            //if the editor is opened on a deleted record, close it
                            try {
                                if(this.editorForm != {} && this.editorForm.definition.parameters._recordid == recordId) {
                                    this.editorForm.clear();
                                    this.editorForm.destroyRecursive();
                                }
                            } catch(e) { }
                        }
                        this.grid.body.refresh();
                    }
                },

                displayRowErrors: function(row) {
                    var lister = this;
                    var cells = row.cells();
                    cells.forEach(function(cell){
                        var fieldName = cell.column.field();
                        var fieldError = lister.store.lastErrors[fieldName]
                        if(fieldError!=undefined) {
                            domClass.toggle( cell.node(), "listerCellError", true);
                            new Tooltip({
                                connectId: cell.node(),
                                label: fieldError
                            });
                        }
                    });

                },

                resize: function(){
                    this.inherited(arguments);
                    this._refreshSize();
                    this._refreshGridVisuals();
                },

                showDndTargets: function() {
                    var lister = this;

                    var actions = [];

                    if(this.canDeleteMultiple()) {
                        actions.push({
                            icon: '/bundles/eulogixcoolcore/gfx/lister/icons/dnd_row_delete.png',
                            tooltip: lister.getCommonTranslator().trans("DELETE_MULTIPLE_TIP"),
                            callFunction: function(rowIds) {
                                if( (rowIds.length > 0) && confirm(lister.getCommonTranslator().trans("CONFIRM DELETE MULTIPLE"))) {
                                    lister.callAction('deleteRows', null, {ids: rowIds.join(',')} );
                                }
                            }
                        });
                    }

                    if(this.canMoveElementsInTree()) {
                        actions.push({
                            icon: '/bundles/eulogixcoolcore/gfx/lister/icons/dnd_row_to_root.png',
                            tooltip: lister.getCommonTranslator().trans("MOVE_TO_ROOT_TIP"),
                            callFunction: function(rowIds) {
                                lister.callAction('moveRowsToRoot', null, {ids: rowIds.join(',')} );
                            }
                        });
                    }

                    if(actions.length > 0) {

                        this.dndTargetsDiv = dojo.doc.createElement('div');

                        this.dndTargetsDiv.style.cssText =
                            "position: absolute; top:10px; right:10px; border:1px solid red; background-color:rgba(255, 255, 255, 0.9); border: 3px solid #232629; border-radius:5px; padding:10px; z-index:9999;";

                        actions.forEach(function(action) {
                            var iconNode = dojo.doc.createElement('img');
                            iconNode.style.cssText = "width:50px; float:left; margin:10px;";
                            iconNode.src = action.icon;
                            coolRollover.addImgRollover(iconNode, false, action.tooltip);

                            lister.dndTargetsDiv.appendChild( iconNode );

                            var iconTarget = new dndTarget(iconNode, {
                                accept: ['grid/rows'],
                                onDropExternal: function(source, nodes, copy){
                                    lister.hideDndTargets();
                                    action.callFunction(lister.getSelectedIds());
                                }
                            });

                        });



                        this.grid.mainNode.appendChild( this.dndTargetsDiv );
                    }


                }
                        
        });
  
});
