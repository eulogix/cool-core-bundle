define("cool/_listerEditorMixin",
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
            "dojo/dom-geometry"
        ], 
        function(cool, cwidget, coolPopup,
                 lang, array, declare, Event, Deferred, all, domConstruct, domClass, domStyle, domGeometry
               ){

            /**
             * this mixin provides facilities for editing records
             */
            return declare("cool._listerEditorMixin", [], {

                openNewRecordEditor: function(parameters) {
                    this.openRowEditor(null, parameters);
                },

                openRowEditor: function(rowID, parameters) {
                    if(rowID && (rowID == this.inEditRowId)) {
                        //opening the same editor twice gets rejected
                        return;
                    }

                    this.emit("openRowEditor", {rowId: rowID, parameters:parameters});

                    this.setInEditRowID(rowID);

                    var editorWidgetParameters = lang.mixin({}, this._getRequestParametersToPropagate(rowID), parameters || {});

                    var record = this._getRowRecord(rowID);
                    if(this.getDefinitionAttribute('row_edit_function')) {
                        var f = this.createFunction(this.getDefinitionAttribute('row_edit_function'), true);
                        f(editorWidgetParameters, record);
                    } else this._openWidgetAsRowEditor(this.definition.clientParameters.editorServerId, editorWidgetParameters);
                },

                getEditorWidget: function() {
                    return this.editorForm;
                },

                setInEditRowID: function(rowID) {
                    this.inEditRowId = rowID;

                    var isVisible = this.grid.domNode.offsetHeight != 0;
                    if(isVisible) {
                        this.grid.body.refresh();
                    }

                    if(rowID !== null) {
                        this.scrollToRow(rowID);
                    }
                },

                _openWidgetAsRowEditor: function(serverId, editorWidgetParameters) {

                    var lister = this;
                    var originalRowID = this.inEditRowId;

                    cool.widgetFactory(
                        serverId,
                        editorWidgetParameters,

                        function(newEditorForm) {

                            newEditorForm.openerLister = lister;
                            newEditorForm.closeable = true;

                            var oldEditorForm = lister.editorForm;
                            lister.own(newEditorForm); // Wires widget into destroyRecursive()

                            //make the grid refresh when a record has been successfully saved
                            newEditorForm.on('recordSaved', function() {

                                lister.inEditRowId = newEditorForm.definition.parameters._recordid;

                                //this is needed because sometimes the recordId changes upon saving, so in this case we destroy the old one as a safety measure (the new one gets loaded by the call to reloadRows)
                                if(originalRowID && (lister.inEditRowId != originalRowID)) {
                                    try {
                                        lister.store.notify(undefined, lister.grid.row(originalRowID).id);
                                        //lister.store.remove(lister.grid.row(originalRowID).id);
                                    } catch(e) { }
                                }

                                lister.reloadRows();
                            });

                            newEditorForm.on('close', function () {
                                lister.setInEditRowID(null);
                            });

                            if(oldEditorForm && oldEditorForm.clear!=undefined) {
                                oldEditorForm.destroyRecursive();
                                lister.editorDiv.innerHTML='';
                            }

                            if(lister.getDefinitionAttribute('show_editor_in_place')) {

                                if(!lister.getRegisteredView('editor')) {

                                    var div = dojo.doc.createElement('div');
                                    var contentBox = domGeometry.getContentBox(lister.contentNode);

                                    domStyle.set(div, 'width', '100%');
                                    domStyle.set(div, 'height', contentBox.h+'px');
                                    domStyle.set(div, 'overflow', 'scroll');

                                    lister.editorDiv = div;

                                    lister.registerViewWithButton('editor', function(){
                                        return div;
                                    }, '/bower_components/fugue/icons/application-form.png');

                                }

                                lister.setActiveView('editor');

                                newEditorForm.on('close', function () {
                                    lister.setActiveView('grid').then(function(){
                                        setTimeout(function() {
                                            lister.grid.body.refresh();
                                        }, 1000);
                                    });
                                });

                            } else {

                                if(lister.editorFormDivId) {
                                    lister.editorDiv = dojo.byId(lister.editorFormDivId);
                                    newEditorForm.fillContent = true;
                                } else {
                                    lister.editorDiv = dojo.doc.createElement('div');
                                    lister.editorDiv.style.cssText = "margin-left:32px; margin-top:15px;";
                                    lister.domNode.appendChild( lister.editorDiv );
                                }

                            }

                            lister.editorForm = newEditorForm;
                            newEditorForm.placeAt(lister.editorDiv);
                            newEditorForm.startup();
                        },
                        function(newEditorForm){
                            lister.emit('editorOpened', {editor: newEditorForm});
                        },
                        null,
                        {
                            autoScrollToMe: true
                        }
                    );
                }
                        
        });
  
});
