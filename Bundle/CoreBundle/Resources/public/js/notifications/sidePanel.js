define("cool/notifications/sidePanel",
    [
        "dojo/_base/lang",
        "dojo/_base/array",
        "dojo/_base/declare",
        "dojo/Evented",
        "dojo/Deferred",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/dom-construct",
        "dojo/_base/fx",
        "dojo/date/locale",
        "dojo/fx/easing",
        "dojo/window",
        "dojo/store/Observable",
        "cool/store/xhrstore",
        "dijit/_WidgetBase",
        "dijit/_TemplatedMixin",
        'dijit/_WidgetsInTemplateMixin',
        "cool/cool",
        "cool/notifications/sidePanelEntry",
        "dojo/text!./templates/sidePanel.html"
    ], function(lang, array, declare, Evented, Deferred, dom, domStyle, domConstruct, baseFx, dateLocale, easing, win, Observable, xhrStore,
                _WidgetBase,  _TemplatedMixin,  _WidgetsInTemplateMixin, cool, sidePanelEntry, template) {

        return declare("cool/notifications/sidePanel", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {
            templateString: template,

            status : "closed",

            contexts: [],
            user_id: "",

            store: null,
            handler: {},
            data: [],
            entries: [],

            postCreate : function() {
                var vs = win.getBox();
                var t = this;

                domStyle.set(this.domNode, "width", "350px");
                domStyle.set(this.domNode, "top", 41 + "px");
                domStyle.set(this.domNode, "height", (vs.h - 41) + "px");
                domStyle.set(this.domNode, "right", "-355px");
                domStyle.set(this.domNode, "display", "none");

                this.store = new Observable(
                    new xhrStore({
                        target: Routing.generate('dataSourceDojoStore', {
                            dataSourceId: 'userNotifications',
                            _sort: "Dcreation_date"}),
                        postVars: {
                            _filter: JSON.stringify({
                                user_id  : this.user_id
                            })
                        }
                    })
                );

                this.reloadData();
            },

            slideOut: function() {
                var t = this;
                baseFx.animateProperty({
                        easing: easing.quintOut,
                        duration: 500,
                        node: this.domNode,
                        properties: {
                            right: { start: 0, end:-355 }
                        },
                        onEnd: function(){
                            domStyle.set(t.domNode, "display", "none");
                        }
                    }).play();
                this.status = "closed";
            },

            slideIn: function() {
                var t = this;
                baseFx.animateProperty({
                    easing: easing.quintOut,
                    duration: 500,
                    node: this.domNode,
                    properties: {
                        right: { start: -355, end:0 }
                    },
                    onBegin: function(){
                        domStyle.set(t.domNode, "display", "inline");
                    }
                }).play();
                this.status = "open";
            },

            toggle: function() {
                if(this.status == "closed")
                    this.slideIn();
                else this.slideOut();
            },

            refreshList: function() {
                var t = this;

                array.forEach(t.entries, function(entry){
                    entry.destroyRecursive();
                    t.domNode.innerHTML = '';
                });
                t.entries = [];

                array.forEach(t.data, function(item){
                    var initHash = lang.mixin({}, item, {
                        creation_date: t.formatEntryDate(item.creation_date),
                        notification_id: item.user_notification_id,
                        parentPanel: t,
                        notificationData: item
                    });
                    delete initHash.title; //or you get a stupid HTML tooltip
                    var entry = new sidePanelEntry(initHash);
                    entry.startup();
                    t.own(entry);
                    t.entries.push(entry);
                    domConstruct.place(entry.domNode, t.domNode, "last");

                });
            },

            reloadData: function() {
                var t = this;
                var deferred = new Deferred();

                if(this.contexts !== null) {
                    this.store.query({contexts: this.contexts.join(',')}).then(function (data) {
                        t.data = [];
                        array.forEach(data, function (item) {
                            t.data.push(lang.mixin({}, item, {
                                notification_data: JSON.parse(item.notification_data)
                            }));
                        });
                        t.refreshList();
                        deferred.resolve(t.data);
                    });
                } else {
                    t.data = [];
                    t.refreshList();
                    deferred.resolve(t.data);
                }

                deferred.then(function(data){
                    t.emit('dataReloaded', data);
                });

                return deferred;
            },

            formatEntryDate: function(iso_timestamp) {
                var today = new Date();
                var date = new Date(iso_timestamp);
                if(date.toDateString() === today.toDateString())
                     return dateLocale.format(date, { locale: dojoConfig.locale, selector: "time", datePattern: "HH/ii" });
                else return dateLocale.format(date, { locale: dojoConfig.locale, selector: "date", datePattern: "dd/MM" });
            },

            deleteNotification: function(notification_id) {
                var t = this;
                this.store.remove(notification_id).then(function(data){
                    var newData = [];
                    array.forEach(t.data, function(item){
                        if(item.user_notification_id != notification_id)
                            newData.push(item);
                    });
                    t.data = newData;
                    t.refreshList();
                    t.emit('dataReloaded', newData);
                });
            },

            setContexts: function(contexts) {
                if(JSON.stringify(contexts) != JSON.stringify(this.contexts)) {
                    this.contexts = contexts;
                    this.reloadData();
                }
            }

        });

    });