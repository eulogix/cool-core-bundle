define("cool/widget/_LayoutMixin",
        [
            "dojo/_base/declare",
            "dojo/dom-style",
            "dojo/dom-geometry",
            "dojo/Deferred",

            "dojo/_base/fx",
            "dojo/fx",
            "dojo/fx/Toggler",

            "dojox/fx",
            "dojox/fx/_core",
            "dojox/fx/scroll",

            "cool/dijit/Button"
        ], 
        function(declare, domStyle, domGeometry, Deferred,
                 BaseFx, Dfx, Toggler, Fx, FxCore, Scroll,
                 Button
            ) {
  
            return declare("cool.widget._LayoutMixin", [], {

                activeView : '',
                viewNodes : {},
                views : {},
                viewCount: 0,

                constructor: function() {
                    this.activeView = '';
                    this.viewNodes = {};
                    this.views = {};
                },

                clear: function() {
                    this.inherited(arguments);
                    this.activeView = '';
                    this.viewNodes = {};
                    this.views = {};
                    this.viewCount = 0;
                },

                registerViewWithButton: function(name, viewFunction, iconSrc){
                    var widget = this;

                    var switchButton = this.addAction('SWITCH_VIEW_'+name, {
                        label: this.getCommonTranslator().trans('widgetView-'+name),
                        onClick: function() {
                            widget.setActiveView(name);
                        },
                        icon: iconSrc,
                        group:'TITLE'
                    });

                    widget.safeOn('viewChanged', function(){
                        switchButton.set('disabled', name == widget.activeView);
                        domStyle.set(switchButton.domNode, 'display', widget.viewCount==1  ? 'none' : 'inline');
                    });

                    this.registerView(name, viewFunction);
                },

                registerView: function(name, viewFunction){
                    this.views[name] = {
                        viewFunction: viewFunction
                    };
                    this.viewCount++;
                },

                getRegisteredView: function(name){
                    return this.views[name];
                },

                getViewNode: function(name) {
                    return this.viewNodes[name];
                },

                rebuildViewNode: function (name) {
                    var viewNode = dojo.doc.createElement('div');

                    var innerDiv = this.views[name].viewFunction();
                    var contentBox = domGeometry.getContentBox(innerDiv);

                    viewNode.appendChild( innerDiv );
                    this.viewNodes[name] = viewNode;
                    return this.getViewNode(name);
                },

                setActiveView: function(name) {
                    var t = this;

                    var d = new Deferred();
                    var retd = new Deferred();

                    if(this.activeView == name) {
                        retd.resolve();
                        return retd;
                    }

                    if(this.activeView) {
                        d = this._hideView(this.activeView);
                    } else d.resolve();

                    d.then(function() {

                        var viewNode = t.getViewNode(name);
                        if(!viewNode) {
                            viewNode = t.rebuildViewNode(name);
                            t.contentNode.appendChild(viewNode);
                        }

                        if(t.activeView != '') {
                            retd = t._showView(name);
                        } else retd.resolve();

                        t.activeView = name;
                        retd.then(function(){ t.emit('viewChanged'); });
                    });

                    return retd;
                },

                _hideView: function(name) {
                    var d = new Deferred();
                    var viewNode = this.getViewNode(name);
                    if(viewNode) {
                        var anim = BaseFx.fadeOut({
                            node: viewNode
                        });
                        anim.on("End", function() {
                            domStyle.set(viewNode, 'display', 'none');
                            d.resolve(viewNode);
                        });
                        anim.play();
                    } else d.resolve(null);
                    return d;
                },

                _showView: function(name) {
                    var d = new Deferred();
                    var viewNode = this.getViewNode(name);
                    if(viewNode) {
                        var anim = BaseFx.fadeIn({
                            node: viewNode
                        });
                        anim.on("Begin", function() {
                            domStyle.set(viewNode, 'display', '');
                        });
                        anim.on("End", function() {
                            d.resolve(viewNode);
                        });
                        anim.play();
                    } else d.resolve();
                    return d;
                }

        });
  
});
