define("cool/workflow/explorer",
    [
        "dojo/_base/declare",
        "dojo/Deferred",
        "cool/cool",
        "cool/_widget",
        "dojo/_base/lang",
        "dojo/_base/array",
        "dojo/promise/all",

        "dojo/store/Memory",
        "dijit/registry",
        "dijit/tree/ObjectStoreModel",
        "dijit/Tree",
        "dijit/layout/ContentPane",
        "cool/form",
        "cool/lister"

    ],
    function(declare, Deferred, cool, _cwidget, lang, array, all, Memory, registry, ObjectStoreModel, Tree, ContentPane, coolForm, coolLister) {

        return declare("cool.workflow.explorer", [_cwidget], {

            onlyContent:true,

            treeDiv: {},

            lister: {},

            tree: {},

            treesel: {},

            rebuild: function() {
                var self = this;

                var ret = new Deferred();
                var parentPromise = this.inherited(arguments).promise;
                var selfD = new Deferred();

                this.treeDiv = dojo.byId(this.getDefinitionParameter('baseId')+'tree');

                cool.widgetFactory( 'EulogixCoolCore/Workflows/TaskLister', {
                        baseProcessNamespace: self.getDefinitionParameter('baseProcessNamespace')
                    }, function(newLister) {
                        newLister.placeAt(self.getDefinitionParameter('baseId')+'lister');

                        newLister.domNode.style.height = "100%";
                        newLister.onlyContent = true;
                        newLister.fillContent = true;
                        newLister.maxHeight = 0;

                        newLister.on('loadComplete', function() {
                            self.buildTree();
                        });

                        newLister.on('reloadRows', function() {
                            self.buildTree();
                        });

                        self.lister = newLister;

                    },
                    null,
                    null,
                    {
                        editorFormDivId: self.getDefinitionParameter('baseId')+'editorPane',
                        extFilterDivId: self.getDefinitionParameter('baseId')+'filter'
                    });


                selfD.resolve();

                all({
                    parent: parentPromise,
                    self: selfD.promise
                }).then(function(results){
                    ret.resolve();
                });

                return ret;
            },

            rebuildNeeded: function(definition) {
                return this.inherited(arguments);
            },

            buildTree: function() {

                var self=this;

                var tdata = this._getTreeData();
                tdata.then(function(data) {

                    // Create test store, adding the getChildren() method required by ObjectStoreModel
                    var myStore = new Memory({
                        data: data,

                        getChildren: function(object){
                            return this.query({parent: object.id});
                        }
                    });

                    // Create the model
                    var myModel = new ObjectStoreModel({
                        store: myStore,
                        query: {id: '_root'},
                        mayHaveChildren: function(item) {
                            return !item._leaf;
                        }
                    });

                    // Create the Tree.
                    var tree = new Tree({
                        model: myModel,
                        showRoot: false,
                        getIconClass: function(/*dojo.store.Item*/ item, /*Boolean*/ opened){
                            return !item._leaf ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf";
                        },

                        onClick: function(item) {

                            switch(item._type) {
                                case 'inbox' : {
                                    self.lister.filterForm.getField('processDefinitionKeyLike').set('value', item.processDefinitionKey);
                                    self.lister.applyFilter();
                                    self.lister.reloadRows();
                                    break;
                                }
                                /*case 'involved' : {
                                    self.lister.filterForm.getField('involvedUser').setValue(item.user);
                                    self.lister.applyFilter();
                                    break;
                                }*/
                            }

                        }

                    });

                    tree.startup();
                    tree.expandAll().then( function() {

                        try{
                            self.treesel = self.tree.selectedItems;
                            self.tree.destroyRecursive();
                        } catch(e) {}

                        tree.placeAt(self.treeDiv);
                        tree.set('selectedItems', self.treesel);
                        self.tree = tree;
                    });

                });

            },

            _getTreeData: function() {

                var self = this;
                var ret = new Deferred();

                cool.callCommand('WorkflowsExplorerTree', function(data) {

                    var retData = [
                        {id:'_root', name:'root'},
                        {id:'inbox', name: self.getTranslator().trans('INBOX'),  parent:'_root'}
                        //{id:'involved', name: self.getTranslator().trans('INVOLVED (%c%)', {c:data.involved.count}),  parent:'_root', _leaf:true, _type:'involved', _user:data.involved.user},
                        //{id:'queued', name: self.getTranslator().trans('QUEUED'),  parent:'_root'}
                    ];

                    array.forEach(data.inbox, function(task, i){
                        retData.push({id:'defkey'+i, processDefinitionKey : task.processDefinitionKey, name:task.processDefinitionKey+' ('+task.task_count+')', parent:'inbox', _leaf:true, _type:'inbox'});
                    });

                    ret.resolve(retData);
                }, self.lister.filterForm.getValues() );

                return ret;
            }

        });

    });