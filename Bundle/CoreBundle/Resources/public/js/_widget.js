define("cool/_widget",
        [
            "cool/cool",
            "cool/window",
            "cool/widget/_LayoutMixin",
            "cool/widget/_ActionsMixin",
            "cool/widget/_SlotsMixin",
            "cool/widget/message",
            "cool/translator",
            "cool/dialog/manager",

            "dojo/_base/lang",
            "dojo/_base/array", // array.filter array.forEach array.map
            "dojo/_base/declare",
            "dojo/request/xhr",
            "dojo/request/util",
            "dojo/dom",
            "dojo/dom-construct",
            "dojo/dom-style",
            "dojo/dom-geometry",
            "dojo/fx",
            "dojo/fx/easing", 
            "dojo/_base/fx",
            "dojo/Deferred",
            "dojo/Evented",

            "dojox/fx",
            "dojox/fx/_core",
            "dojox/fx/scroll",
            "dojox/fx/Shadow",

            "dijit/_WidgetBase",
            "dijit/layout/ContentPane",
            "dijit/layout/TabContainer",
            "dijit/TitlePane",
            "dijit/form/Button",
            "dijit/form/ToggleButton", 
            "dijit/form/ComboButton", 
            "dijit/Menu",
            "dijit/MenuItem",
            "dijit/PopupMenuItem",
            "dijit/form/DropDownButton",
            "dijit/TooltipDialog",
            "dijit/Toolbar",
            "dijit/Dialog",

            "dojox/widget/Standby"
        ], 
        function(cool, coolWindow, _LayoutMixin, _ActionsMixin, _SlotsMixin, widgetMessage, ctr, dialogManager,
                 lang, array, declare, xhr, util, dom, domConstruct, domStyle, domGeometry, coreFx, easing, baseFx, Deferred, Evented,
                 Fx, FxCore, Scroll, Shadow,
                 _WidgetBase, ContentPane, TabContainer, TitlePane, Button, ToggleButton, ComboButton, Menu, MenuItem, PopupMenuItem, DropDownButton, TooltipDialog, Toolbar, Dialog, Standby){
  
            return declare("cool._widget", [_WidgetBase, _LayoutMixin, _ActionsMixin, _SlotsMixin, Evented], {

                serverId : "",      //the token used to uniquely identify the server resource linked to the widget (eg. EulogixCoolCore/Test)
                hashes   : "",      //the token used to tell the server which version of the definition components we have already loaded

                onlyContent : false, //if set, the widget will not be decorated with a window
                fillContent : false, //if set, the content node of the widget will stretch to 100% height of the container node

                translator : false,
                commonTranslator : false,
                closeable: false,
                transparent: false,

                messages: {},

                destroyOnClearList: null,

                unregisterOnClearHandlers: [],

                constructor: function() {
                      this.definition = {}; //definition of the widget as it comes from the server
                      this.eventManagers = {}; //event manager functions
                      this.toolbar = null;
                      this.destroyOnClearList = []; //when a dijit is somehow attached to the widget, we keep track of it in this array to be able to safely destroy it on clear()
                      this.messages = {};
                },
                
                postCreate : function() {
                    this.inherited(arguments);
                },

                getActionParameters: function(obj, includeHashes) {
                    obj = obj || {};
                    var ret = lang.mixin({}, obj, this.definition.parameters, {_hashes:this.hashes, _client_id:this.id});
                    if(includeHashes === false)
                        delete ret._hashes;
                    return ret;
                },

                getContainer: function() {
                    return this.domNode.parentNode;
                },

                /**
                 * binds this instance to a serverId, retrieving the definition (if not supplied) and building the widget presentation
                 *
                 * @param serverId
                 * @param parameters
                 * @param onSuccess
                 * @param data
                 */
                bindToUrl : function(serverId,/* Object */ parameters, onSuccess, data) {
                    this.serverId = serverId;
                    this.definition.parameters = parameters != undefined ? parameters : this.definition.parameters;
                    var cwidget = this;
                    if(data==undefined) {
                        //we don't propagate the hashes here as otherwise the server response could be incomplete
                        var url = Routing.generate('_widget_get_definition', this.getActionParameters({serverId:serverId}, false));
                        xhr(url, {
                            handleAs: "json"
                        }).then(function(data){
                            cwidget.onBindSuccess( data).then(function(){
                                if(onSuccess!=undefined && onSuccess instanceof Function) {
                                    onSuccess();
                                }
                            });
                        }, function(err){
                            dialogManager.showXhrError("XHR error in _cwidget.bindToUrl", url, err.response.text);
                        }, function(evt){
                        });
                    } else {
                        cwidget.onBindSuccess( data ).then(function(){
                            if(onSuccess!=undefined && onSuccess instanceof Function) {
                                onSuccess();
                            }
                        });
                    }
                },
                

                reBind : function() {
                    this.clear();
                    this.bindToUrl(this.serverId, this.definition.parameters);
                },

                /**
                 * base implementation 
                 * @param  {[type]} data
                 * @return dojo.Deferred
                 */
                onBindSuccess: function( data ) {
                    return this.parseData( data );
                },

                rebuildNeeded: function(definition) {
                    return definition.hasOwnProperty('actions') ||
                           definition.hasOwnProperty('slots');
                },

                /**
                 * this function reads a returned json object from the server and determines the action to take.
                 * As a default, if a _definition is found, the widget is supposed to have to be redrawn upon the newly provided definition, hence a rebuild() is issued
                 * @param  {[type]} data [description]
                 * @return dojo.Deferred      [description]
                 */
                parseData : function( data ) {
                    var self = this;
                    var d = new Deferred();

                    if( data.hasOwnProperty('_hashes') ) {
                        this.hashes = data._hashes;
                    }

                    if( data.hasOwnProperty('_definition') ) {
                        this.definition = lang.mixin(this.definition, data._definition);

                        if(this.definition.clientParameters.serverId) {
                            this.serverId = this.definition.clientParameters.serverId; 
                        }

                        var d2 = new Deferred();

                        if(this.rebuildNeeded(data._definition)) {
                            d2 = this.rebuild();
                        } else d2.resolve();

                        d2.then(function(){
                            if(data._definition.hasOwnProperty('messages')) {
                                self.renderMessages();
                            }

                            var f = function() {
                                if(data._definition.hasOwnProperty('commands')) {
                                    self.executeCommands();
                                }

                                if(data._definition.hasOwnProperty('events')) {
                                    self.processEvents();
                                }
                            };

                            if(data._definition.hasOwnProperty('slots')) {
                                self.renderSlots().then( function() {
                                    f();
                                });
                            } else f();

                            d.resolve();
                        });

                    } else d.resolve();

                    return d;
                },

                renderMessage: function(messageText, type, messageId, parameters) {
                    var t = this;
                    var message = null;
                    parameters = parameters || {};
                    if(!messageId || !lang.exists(messageId, this.messages)) {

                        message = new widgetMessage(lang.mixin({
                            'type' : type,
                            'text' : messageText
                        }, parameters));

                        this.own(message);

                        if(messageId) {
                            this.messages[messageId] = message;
                            message.on('close', function(){
                                delete t.messages[messageId];
                            });
                        }

                        message.on('changeRender', function(){
                            t.resize();
                        });

                        message.placeAt(this.notificationsNode, "last");
                    } else {
                        message = this.messages[messageId];
                        message.setText(messageText);
                    }
                },

                getMessage: function(messageId) {
                    if(lang.exists(messageId, this.messages))
                        return this.messages[messageId];
                    return false;
                },

                closeMessage: function(messageId) {
                    var message = this.getMessage(messageId);
                    if(message)
                        message.close();
                },

                /**
                * renders the widget messages (if any)
                * 
                */
                renderMessages: function() {
                   var definition = this.definition;
                    for (var type in definition.messages) {
                        var msgs = definition.messages[type];
                        for(var i=0; i<msgs.length; i++) {
                            this.renderMessage( msgs[i].text, type );
                        }
                    }        
                },

                /**
                * returns the widget toolbar, if it is not instantiated yet, it gets intantiated here
                * 
                */
                getToolbar: function() {
                    if(this.toolbar == null) {
                        this.toolbar = new Toolbar({
                            style: { "min-height":"20px" }
                        });
                        this.toolbar.placeAt( this.actionsNode, "first");
                        this.toolbar.startup();
                        if(this.getDefinitionAttribute('hideToolbar')) {
                            domStyle.set(this.toolbar.domNode, {
                                display: "none"
                            });
                        }
                    }
                    return this.toolbar;
                },

                /**
                * processes the events generated by the server
                */
                processEvents: function() {
                   var definition = this.definition;
                   if(definition.events != undefined) {
                       for(var i=0; i<definition.events.length; i++) {
                           var event = definition.events[i];

                           this.emit(event.event, event.payload);

                           //TODO: replace this event managers stuff with proper event handling, allowed by this one line
                           if(this.eventManagers[event.event] instanceof Function) {
                                this.eventManagers[event.event](event.payload);
                           }
                       }     
                    }   
                },

                /**
                * executes the commands (js snippets) generated by the server
                */
                executeCommands: function() {
                   var definition = this.definition;
                   if(definition.commands != undefined) {
                       for(var i=0; i<definition.commands.length; i++) {
                            var cmd = definition.commands[i];
                            if(cmd.type=='js') {
                                this.createFunction(cmd.body)();
                            }
                       }     
                    }   
                },

                setEventManager: function(eventName, eventFunction) {
                    this.eventManagers[eventName] = eventFunction;
                },

                jhl: function (json) {
                    if (typeof json != 'string') {
                         json = JSON.stringify(json, undefined, 2);
                    }
                    json = json
                        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                        .replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                        var cls = 'number';
                        if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'key';
                            } else {
                                cls = 'string';
                            }
                        } else if (/true|false/.test(match)) {
                            cls = 'boolean';
                        } else if (/null/.test(match)) {
                            cls = 'null';
                        }
                        return '<span class="' + cls + '">' + match + '</span>';
                    });
                    return "<pre>"+json+"</pre>";
                },
                
                isDebug: function() {
                    return this.getDefinitionAttribute('_debug') == true;
                },

                isReadOnly: function() {
                    return this.getDefinitionAttribute('readOnly') == true;
                },

                getWidgetId: function() {
                    return this.getDefinitionAttribute('widgetId');
                },

                getCurrentVariation: function() {
                    return this.getDefinitionAttribute('currentVariation');
                },

                moveContent: function(target, newRoot) {
                    if ( this.domNode.hasChildNodes() ) {
                          var children = this.domNode.childNodes;
                          var len = children.length;
                          //needed because the length of children decreases at every iteration
                          for (var i = len-1; i >= 0 ; i--) {
                            domConstruct.place(children[i], target, "first");

                          }
                        }

                        if(newRoot) {
                            this.domNode.appendChild( newRoot );
                        }
                    this.resize();
                },

                decorateDebug: function() {
                    var widget = this;
                    setTimeout(function(){
                        var ftc = new TabContainer({
                            region: "center",
                            nested: false,
                            doLayout: false  //flexible height
                        });       
                        
                        var fcp = new ContentPane({
                             title: "Widget"
                        });
                      
                        
                        var cpDbg = new ContentPane({
                             title: "Definition"
                        });
                        
                        widget.debugNode = cpDbg.domNode;
                        widget.updateDebug();

                        ftc.addChild(fcp);
                        ftc.addChild(cpDbg);           
                                                 
                        widget.moveContent(fcp.domNode, ftc.domNode);

                        ftc.startup();   
                        ftc.resize();  //weird things happen if this is done synchronously
                    }, 500);
                           
                },

                containerWindow: null,

                decorateWindow: function() {
                    if(!this.onlyContent && !this.getDefinitionAttribute('onlyContent')) {
                        var widget = this;

                        var w = new coolWindow({
                            title:this.getDefinitionAttribute('title'),
                            closeable: this.closeable,
                            transparent: this.transparent,
                            fillContent: this.fillContent
                        });

                        w.onClose = function() {
                            widget.emit('close');
                            w.destroyRecursive();
                        };

                        w.placeAt(this.domNode.parentNode);
                        w.on('resize', function() { widget.resize() });

                        this.moveContent(w.containerNode, w.domNode);

                        w.startup();
                        w.resize();

                        this.containerWindow = w;

                        //tells the widget to render "TITLE" actions in the window's title bar
                        this.actionNodes['TITLE'] = w.actionsNode;
                    }
                },


                clear: function() {
                    if(this.toolbar!=null) {
                        this.toolbar.destroyRecursive();
                        this.toolbar = null;
                    }

                    this.clearActions();

                    var i;

                    for(i=0;i<this.destroyOnClearList.length;i++) {
                        try {
                            this.destroyOnClearList[i].destroyRecursive();
                        } catch (e) {
                            console.log(e);
                        }
                    }

                    for(i=0;i<this.unregisterOnClearHandlers.length;i++) {
                        try {
                            this.unregisterOnClearHandlers[i].remove();
                        } catch (e) {
                            console.log(e);
                        }
                    }

                    for(var m in this.messages) {
                        this.messages[m].destroyRecursive();
                    }

                    this.destroyOnClearList = [];
                    this.unregisterOnClearHandlers = [];

                    this.inherited(arguments);
                },

                destroyOnClear: function(widgetInstance) {
                    this.destroyOnClearList.push(widgetInstance);
                },

                unregisterOnClear: function(eventHandler) {
                    this.unregisterOnClearHandlers.push(eventHandler);
                },

                safeOn: function(eventName, handlerFunction) {
                    this.unregisterOnClear( this.on(eventName, handlerFunction) );
                },

                updateDebug: function() {
                    if(this.debugNode) {
                        this.debugNode.innerHTML = this.jhl( this.definition );
                    }
                },

                /**
                 * redraws the widget based on the loaded definition
                 * This function should return a promise, resolved when all the elements of the widget have been drawn
                 */
                rebuild: function() {

                    var d = new Deferred();

                    if(!this.alreadyBuilt) {
                        //in the constructor we set up the various container nodes in the correct order
                        this.actionsNode = dojo.doc.createElement('div');      
                        this.notificationsNode = dojo.doc.createElement('div');
                        this.contentNode = dojo.doc.createElement('div');
                        this.contentNode.id = Math.random().toString(10).replace('.','');
                        this.slotsNode = dojo.doc.createElement('div');
                        this.slotsNode.className = "hiddenNode";      
                        
                        this.domNode.appendChild(this.actionsNode);
                        this.domNode.appendChild(this.notificationsNode);
                        this.domNode.appendChild(this.contentNode);
                        this.domNode.appendChild(this.slotsNode);
                        this.alreadyBuilt = true;
                    
                        if(this.isDebug()) {
                            this.decorateDebug();    
                        } 
                    
                        this.decorateWindow();

                    } else {
                        this.updateDebug();
                    }
                    //refresh the debug pane content

                    this.clear();

                    this.renderActions();

                    var t = this;

                    //this ensures that widgets with fillContent to true can resize themselves to fit parent
                    setTimeout(function(){t.resize();}, 200);

                    d.resolve();
                    return d;
                },

                /**
                 * hook that returns the parameters that the widget sends to the controller along with the action specs
                 * @return dojo.Deferred
                 */
                getActionValues: function() {
                    var deferred = new Deferred();
                    deferred.resolve({});
                    return deferred;
                },

                mixAction: function(actionName, mixData, onSuccess) {
                    var t = this;

                    var d = new Deferred();

                    this.getActionValues().then(function(actionValues){
                        var mixedPost = lang.mixin(actionValues, mixData);
                        t.callAction(actionName, onSuccess, mixedPost).then(function(data){
                            d.resolve(data);
                        });
                    });

                    return d;
                },

                callAction: function(actionName, onSuccess, postData, parameters) {

                    parameters = parameters || {};
                    var dontLock = parameters["dontLock"] || false;
                    var downloadResult = parameters["downloadResult"] || false;

                    var url = Routing.generate('_widget_call_action', this.getActionParameters({serverId:this.serverId, actionName:actionName}));
                    var widget = this;

                    var d = new Deferred();

                    if(postData != null) {
                        var realPostData = new Deferred();
                        realPostData.resolve(postData);
                    } else {
                        realPostData = this.getActionValues();
                    }

                    realPostData.then(function(actionValues){

                        //since for some reason any key in actionValues which is set to null will not get posted at all
                        //we convert them here to empty strings
                        for(var k in actionValues) {
                            if(actionValues[k]===null)
                                actionValues[k]='';
                        }

                        if(downloadResult) {
                            widget._postNoXhr(url, actionValues);
                            d.resolve();
                        } else {
                            if(!dontLock) widget._lockForServerCall();
                            xhr(url, {
                                handleAs: "json",
                                method: "POST",
                                data: actionValues
                            }).then(
                                        function(data){
                                            if(onSuccess!=undefined && onSuccess instanceof Function) {
                                                onSuccess(data);
                                            } else {
                                                widget.parseData(data);
                                            }
                                            d.resolve(data);
                                            if(!dontLock) widget._unlockFromServerCall();
                                        },

                                        function(err){
                                            if(!dontLock) widget._unlockFromServerCall();
                                            dialogManager.showXhrError("XHR error in _cwidget.callAction", url, err.response.text);
                                            d.resolve();
                                        },

                                        function(evt){  //progress?
                                        }
                                );
                        }

                    });

                    return d;
                },

                _lockForServerCall: function() {
                    if(!this._standby) {
                        var standby = new Standby({
                            target: this.domNode,
                            duration: 50,
                            color:'white'
                        });
                        document.body.appendChild(standby.domNode);
                        standby.startup();
                        this._standby = standby;
                    }

                    this._standby.show();
                },

                _unlockFromServerCall: function() {
                    if(this._standby) {
                        this._standby.hide();
                    }
                },

                _postNoXhr: function(path, params, method) {
                    method = method || "post"; // Set method to post by default if not specified.

                    // The rest of this code assumes you are not using a library.
                    // It can be made less wordy if you use one.
                    var form = document.createElement("form");
                    form.setAttribute("method", method);
                    form.setAttribute("action", path);

                    for(var key in params) {
                        if(params.hasOwnProperty(key)) {
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", key);
                            hiddenField.setAttribute("value", params[key]);

                            form.appendChild(hiddenField);
                        }
                    }

                    document.body.appendChild(form);
                    form.submit();
                },
                
                createFunction: function (js, raw, preserveObjects) {

                    if(js instanceof Function)
                        return js;

                    //necessary because if declared without eval, closure compiler modifies the name of the variables, which would later be unaccessible in the created function!
                    preserveObjects = preserveObjects || {};
                    preserveObjects.widget = this;

                    //use this temporary variable to safely store the content of the preserveObjects object
                    this['_preserveObjects'] = preserveObjects;
                    eval('var preserveObjects = this[\'_preserveObjects\'];');

                    for(var objectName in preserveObjects) {
                        eval('var '+objectName+' = preserveObjects.'+objectName+';');
                    }

                    if(js!=undefined) {
                        if(raw) {
                            eval('var f = '+js);
                            return eval('f');
                        } else return function() {
                             return eval('(function() {'+js+'}());');
                        }
                    }
                    return function() {};
                },

                createMenu: function(items) {

                    if(items == undefined || items.length == 0) {
                        return;
                    }
                    
                    var menu = new Menu({
                      //  id: "saveMenu"
                    });
                    
                    for(var i = 0; i<items.length; i++) {
                        var menuItemClass = MenuItem;

                        var prefix = items[i]['icon'] ? "<img src='"+items[i]['icon']+"' class='icon'>&nbsp;" : '';
                        var o = {
                            label: prefix + items[i].label,
                            //iconClass: "dijitEditorIcon dijitEditorIconSave",
                            onClick: this.createFunction( items[i].onClick )
                        };

                        if(items[i].children && items[i].children.length > 0) {
                            o.popup = this.createMenu(items[i].children);
                            menuItemClass = PopupMenuItem;
                        }

                        var menuItem = new menuItemClass(o); 

                        menu.addChild(menuItem);   
                    }
                    return menu;
                },

                resize: function() {
                    this.inherited(arguments);

                    if(this.fillContent) {
                        var actionsBox = domGeometry.getContentBox(this.actionsNode);
                        var notificationsBox = domGeometry.getContentBox(this.notificationsNode);
                        var nodeBox = domGeometry.getContentBox(
                            this.containerWindow ? this.containerWindow.containerNode : this.domNode.parentNode
                        );

                        domStyle.set(this.contentNode, {
                            "height": (nodeBox.h - actionsBox.h - notificationsBox.h)+"px"
                        });
                    }

                    if(this.slotsTab)
                        this.slotsTab.resize();
                },

                getTranslator: function() {
                    if(this.translator)
                        return this.translator;

                    this.translator = new ctr({
                        domain: this.getTranslationDomain()
                    });

                    return this.translator;
                },

                getCommonTranslator: function() {
                    if(this.commonTranslator)
                        return this.commonTranslator;

                    this.commonTranslator = new ctr({
                        domain: "WIDGET_COMMON"
                    });

                    return this.commonTranslator;
                },

                getTranslationDomain: function() {
                    if(lang.exists('definition.attributes._translation_domains', this))
                        return this.definition.attributes._translation_domains[0];
                    return this.serverId;
                },

                getDefinitionAttribute: function(attribute) {
                    if(lang.exists('definition.attributes.'+attribute, this))
                        return this.definition.attributes[attribute];
                    return null;
                },

                getDefinitionParameter: function(parameter) {
                    if(lang.exists('definition.parameters.'+parameter, this))
                        return this.definition.parameters[parameter];
                    return null;
                },

                setDefinitionParameter: function(parameter, value) {
                    this.definition.parameters[parameter] = value;
                },

                /**
                 * return the definition parameters that can be propagated to other widgets without interfering with their rendering mechanism
                 * used for instance to initialize row editor from listers, without causing the editor form to try to bind itself to an existing
                 * dijit id (because of _client_id parameter)
                 * @returns {*}
                 */
                getCleanDefinitionParameters: function() {
                    var ret = this.definition.parameters;
                    delete ret._client_id;
                    delete ret._hashes;
                    return ret;
                },

                scrollToMe: function(durationMs, node) {

                    durationMs = durationMs || 500;
                    //hack that initiates an animations on all the parent nodes of the widget.
                    //works, but a better solution should be found TODO: fix that
                    var self = this;
                    var parent = this.domNode;
                    if(!node)
                        node = this.domNode;
                    var done = false;
                    do {
                        parent = parent.parentNode;
                        if(!parent) {
                            done = true;
                        } else {
                            if( parent.tagName=='HTML') {
                                parent = window;
                                done = true;
                            }

                            (function(winw) {

                                //avoid scrolling elements that have no scrollbars!
                                if(!done &&
                                    domStyle.get(winw, 'overflow') != 'hidden' &&
                                    domStyle.get(winw, 'overflow-x') != 'hidden' &&
                                    domStyle.get(winw, 'overflow-y') != 'hidden')

                                        new Fx.smoothScroll({
                                            node: node,
                                            win: winw,
                                            duration: durationMs
                                        }).play();

                            })(parent);
                        }
                    } while(!done);
                },

                openSimpleAuditTrail: function() {

                }

        });
  
});
