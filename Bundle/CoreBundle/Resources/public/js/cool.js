define([
    "dojo/_base/lang",
    "dojo/_base/array",
    "dojo/request",
    "dojo/request/xhr",

    "dijit/registry",

    "cool/file/repository",
    "cool/dialog/manager",
    "cool/util/formatters",

    "cool/store/errors/DsError"

], function(lang, array, request, xhr,
            registry,
            coolFileRepository, dialogManager, formatters,
            DsError) {
  
    var cool = {

        pendingWidgets : 0,

        safeState: function() {
            return this.pendingWidgets == 0;
        },

        signalWidgetLoading: function(serverId) {
            this.pendingWidgets++;
        },

        signalWidgetLoaded: function(serverId) {
            this.pendingWidgets--;
        },

        widgetFactory: function(serverId, parameters, preBind, afterBind, data, widgetParameters) {
            // summary:
            //		Instantiates a widget
            // serverId: String
            //		the server id of the widget
            // parameters: Object
            //		This bag will form the widget's parameters, they will be returned in the definition
            //      and will be propagated with every request.
            //      This bag should be as small as possible to avoid too long URIs
            // preBind: Function?
            //		function that will be executed with the newly instantiated widget as argument, just before
            //		the widget receives the server data with the bindToUrl method
            // afterBind: Function?
            //      this one will be called just after bindToUrl
            // data: Object
            //      this object contains the widget definition. If it is null or undefined, a server side call will
            //      be made to retrieve it
            // widgetParameters: Object
            //      hash that is fed to the js constructor of the widget
            // returns:
            //		null
            var self = this;
            self.signalWidgetLoading(serverId);

            if(!lang.isFunction(preBind))
                preBind = function(){};

            if(!lang.isFunction(afterBind))
                afterBind = function(){};

            widgetParameters = widgetParameters || {};

            /*normalize all slashes to single slash*/
            serverId = serverId.replace(/[\\\/]/g, '/');

            if(data==undefined) {

                var url = Routing.generate('_widget_get_definition', lang.mixin({serverId:serverId}, parameters));

                xhr(url, {
                        handleAs: "json",
                        sync: false,
                        method: 'GET'
                      }).then(function(data){
                        if(data._definition.clientParameters.widget != undefined && !data._definition.attributes.disabled) {

                            if(data._definition.attributes._client_id && !widgetParameters.id)
                                widgetParameters.id = data._definition.attributes._client_id;

                            self._destroyWidget(widgetParameters.id);

                            require([data._definition.clientParameters.widget, "dojo/domReady!"], function(widget){
                                var w = new widget(widgetParameters);
                                preBind(w);
                                w.bindToUrl(serverId, parameters, function(){
                                    afterBind(w);
                                    self.signalWidgetLoaded(serverId);
                                }, data);
                            });
                        }
                      }, function(err){

                        dialogManager.showXhrError(err);
                        self.signalWidgetLoaded(serverId);

                      }, function(evt){
                  });
            } else {
                if(data._definition.clientParameters.widget != undefined && !data._definition.attributes.disabled) {

                    if(data._definition.attributes._client_id && !widgetParameters.id)
                        widgetParameters.id = data._definition.attributes._client_id;

                    self._destroyWidget(widgetParameters.id);

                    require([data._definition.clientParameters.widget, "dojo/domReady!"], function(widget){
                        var w = new widget(widgetParameters);
                        preBind(w);
                        w.bindToUrl(serverId, parameters, function(){
                            afterBind(w);
                            self.signalWidgetLoaded(serverId);
                        }, data);
                    });
                }
            }
        },

        /**
         * sometimes previous widgets remain registered even if destroyed, this takes care of them
         * @param id
         * @private
         */
        _destroyWidget: function(id) {
            if(registry.byId(id)) {
                console.log("Dijit "+id+" still registered");
                registry.byId(id).destroyRecursive();
                registry.remove(id);
            }
        },

        callCommand: function(route, onSuccessCallback, parameters, postData) {
            parameters = parameters || {};

            var url = Routing.generate(route, parameters);
            onSuccessCallback = onSuccessCallback || function(data) {
                if(data.js) {
                    eval(data.js);
                }
            };

            request(url, {
                handleAs: 'json',
                method: postData ? 'POST' : 'GET',
                headers: {
                    "Content-Type": 'application/json',
                    Accept: 'application/javascript, application/json'
                },
                data: postData ? JSON.stringify(postData) : undefined
            }).then(
                function(data){
                    onSuccessCallback(data);
                },
                function(error){
                    console.log("An error occurred: " + error);
                }
            );
        },

        repoFromId: function(repositoryId, parameters) {
            return new coolFileRepository(lang.mixin({repositoryId:repositoryId}, parameters || {}));
        },

        getFormatters: function() { return formatters; },

        getDialogManager: function() { return dialogManager; },

        handleXhrError: function(error) {
            if(error instanceof DsError)
                alert(error.getErrors().pop());
            else this.getDialogManager().showXhrError(error);
        }

    };

    return cool;
  
});