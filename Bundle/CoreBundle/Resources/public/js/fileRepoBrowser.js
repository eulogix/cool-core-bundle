define("cool/fileRepoBrowser",
        [
            "dojo/_base/declare",
            "dojo/_base/lang",
            "dojo/dom-class",
            "dojo/dom-style",
            "dojo/Deferred",
            "dojo/promise/all",

            "cool/cool",
            "cool/_widget",
            'cool/rfe/FileExplorer',
            "cool/translator",
            "cool/file/repository"
        ],
        function(declare, lang, domClass, domStyle, Deferred, all,
                 cool, cwidget, FileExplorer, ctr, coolFileRepo){
  
            return declare("cool.fileRepoBrowser", [cwidget], {

                onlyContent: true,
                fillContent: true,

                rfe: null,

                cssHeight:"300px",

                treePaneVisible: false,

                constructor: function() {
                    this.inherited(arguments); // builds definition
                },

                onBindSuccess: function( data ) {
                    return this.inherited(arguments); // builds definition
                },
                

                /**
                * destroys the widgets
                * 
                */
                clear: function() {
                    if(this.contentNode!==undefined) {
                        this.contentNode.innerHTML = '';
                    }
                    this.inherited(arguments); 
                },
                
                /**
                * recreates the widgets
                * 
                */
                rebuild: function() {
                    var self = this;

                    this.clear();

                    var ret = new Deferred();
                    var parentPromise = this.inherited(arguments).promise;
                    var selfD = new Deferred();

                    this.contentNode.style.height = this.cssHeight;

                    domClass.toggle(this.contentNode, "rfe", true);

                    var commonParameters = this.definition.parameters;

                    var frepo = new coolFileRepo(commonParameters);

                    var rfe = new FileExplorer({

                        storeUrl: Routing.generate('dataSourceDojoStore', lang.mixin({dataSourceId: 'fileRepository'}, commonParameters)),

                        servicePreview: Routing.generate('frepoGetPreviewImage', lang.mixin({width: 80}, commonParameters)),

                        serviceDownload: Routing.generate('frepoDownload', commonParameters),

                        serviceServe: Routing.generate('frepoServe', commonParameters),

                        serviceQueryStringSuggestion:  Routing.generate('frepoQueryStringSuggestion', lang.mixin({}, commonParameters)),

                        coolFileRepo: frepo,

                        commonParameters: commonParameters, //TODO: remove that, it is used by the upload form

                        defaultView: self.defaultView || 'icons',

                        translator: new ctr({
                            domain: "WIDGET_RFE"
                        })
                    });

                    rfe.placeAt(this.contentNode);
                    this.own(rfe);
                    rfe.startup();
                    rfe.resize();


                    this.rfe = rfe;


                    setTimeout(function() {
                        rfe.panes.set('treePaneVisible', self.treePaneVisible);
                        rfe.panes.set('view', 'horizontal');
                        selfD.resolve();
                    }, 100);

                    all({
                        parent: parentPromise,
                        self: selfD.promise
                    }).then(function(results){
                        ret.resolve();
                    });

                    return ret;
                },

                resize: function(){
                    this.inherited(arguments);
                    this.rfe.resize();
                },

                destroyRecursive: function() {
                    //this is horrendous, but somehow this method gets called twice triggering errors in dgrid destructors...
                    try { this.inherited(arguments); } catch(e) {}
                }

        });
  
});
