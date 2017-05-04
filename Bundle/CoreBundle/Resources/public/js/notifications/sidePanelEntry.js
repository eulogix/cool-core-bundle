define("cool/notifications/sidePanelEntry",
    [
        "dojo/_base/lang",
        "dojo/_base/declare",
        "dojo/_base/event",
        "dojo/Evented",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/_base/fx",
        "dojo/fx/easing",
        "dojo/window",
        "dojo/store/Observable",
        "cool/store/xhrstore",
        "dijit/_WidgetBase",
        "dijit/_TemplatedMixin",
        'dijit/_WidgetsInTemplateMixin',
        "cool/cool",
        "cool/fx/rollover",
        "dojo/text!./templates/sidePanelEntry.html"
    ], function(lang, declare, event, Evented, dom, domStyle, baseFx,  easing,  win, Observable, xhrStore,
                _WidgetBase,  _TemplatedMixin,  _WidgetsInTemplateMixin, cool, coolRollover, template) {

        return declare("cool/notifications/sidePanelEntry", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {
            templateString: template,

            parentPanel: {},

            //db properties
            icon: "/bundles/eulogixcoolcore/gfx/notifications/task.png",
            creation_date: "",
            notification: "",
            notification_id: "",

            notificationData: {},

            postCreate : function() {
                coolRollover.addImgRollover(this.closeImg, true, GlobalTranslator.trans('CLOSE_NOTIFICATION_TIP'));
            },

            _onHover: function() {
                baseFx.animateProperty({
                    easing: easing.quintOut,
                    duration: 300,
                    node: this.bgDiv,
                    properties: {
                        opacity: { start: 0, end: 0.2 }
                    }
                }).play();
            },

            _onUnhover: function() {
                baseFx.animateProperty({
                    easing: easing.quintOut,
                    duration: 300,
                    node: this.bgDiv,
                    properties: {
                        opacity: { start: 0.2, end: 0 }
                    }
                }).play();
            },

            _onClick: function() {
                this.parentPanel.handler.handleClick(this.notificationData);
            },

            _onClose: function(e) {
                this.parentPanel.deleteNotification(this.notification_id);
                event.stop(e);
            }

        });

    });