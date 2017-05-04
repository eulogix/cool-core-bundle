define("cool/phone/phone",
    [
        "dojo/_base/lang",
        "dojo/_base/declare",
        "dojo/Evented",
        "dojo/dom",
        "dojo/dom-style",
        "dijit/_WidgetBase",
        "dijit/_TemplatedMixin",
        'dijit/_WidgetsInTemplateMixin',
        "cool/cool",
        "dojo/text!./templates/phone.html"
    ], function(lang, declare, Evented, dom, domStyle,  _WidgetBase,  _TemplatedMixin,  _WidgetsInTemplateMixin, cool, template) {

        return declare("cool.phone.phone", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {
            templateString: template,

            // Attributes
            status: "-",
            _setStatusAttr: { node: "statusLabel", type: "innerHTML" },
            callingName: "",
            _setCallingNameAttr: { node: "nameLabel", type: "innerHTML" },
            callingNumber: "",
            _setCallingNumberAttr: { node: "nrLabel", type: "innerHTML" },
            timerValue: "",
            _setTimerValueAttr: { node: "timerLabel", type: "innerHTML" },

            //other attributes
            capabilityToken: null,
            inited: false,
            calling: false,

            pendingCallId: null,
            callDuration: 0,

            setup: function() {
                if(!this.inited) {
                    cool.callCommand('getCapabilityToken', function(data) {
                        Twilio.Device.setup(data.token, {debug:true});
                    }, {} );
                    this.inited = true;
                }
            },

            postCreate : function() {
                var t = this;
                if(this.capabilityToken)
                    this.setUp(this.capabilityToken);
                this.refreshVisuals();

                Twilio.Device.ready(function (device) {
                    t.refreshVisuals();
                });

                Twilio.Device.offline(function (device) {
                    t.refreshVisuals();
                });

                Twilio.Device.error(function (error) {
                    t.refreshVisuals();
                    alert(error.message);
                });

                Twilio.Device.connect(function (conn) {
                    t.calling = true;
                    t.callDuration = 0;

                    cool.callCommand('logCallStart', function(data) {
                    }, lang.mixin({ CallSid: conn.parameters.CallSid }, {pendingCallId: t.pendingCallId} ));

                    conn.mute( function(isMuted, connection) {
                        t.refreshVisuals();
                    } );

                    t.refreshVisuals();
                });

                Twilio.Device.disconnect(function (conn) {
                    t.calling = false;
                    t.refreshVisuals();
                });


                setInterval(function(){
                    t.set('timerValue', t.calling ? t._renderTimer(t.callDuration++) : '');
                }, 1000);
            },

            call: function(target, pendingCallId, displayName) {
                var t = this;
                this.pendingCallId = pendingCallId;
                this.setup();

                //TODO: queue calls
                var callFunction = function() {
                    Twilio.Device.disconnectAll();
                    Twilio.Device.connect({target: target});
                    if(displayName) {
                        t.set('callingNumber', target);
                        t.set('callingName', displayName);
                    } else {
                        t.set('callingName', target);
                    }
                };

                if(this.isReady())
                    callFunction();
                else Twilio.Device.ready(function (device) {
                    callFunction();
                });
            },

            hangup: function() {
                Twilio.Device.disconnectAll();
            },

            toggleMic: function() {
                if(Twilio.Device.activeConnection())
                    Twilio.Device.activeConnection().mute( !Twilio.Device.activeConnection().isMuted() );
                return this.isMuted();
            },

            onClose: function() {
                this.destroyRecursive();
            },

            refreshVisuals: function() {
                var status = null;
                if(this.inited) {
                    status = Twilio.Device.status();
                    this.set('status', GlobalTranslator.trans('PHONE_STATUS')+' : '+status);
                }
                this.hangupImage.style.display = (this.calling ? 'block' : 'none');
                this.micImage.style.display = (this.calling ? 'block' : 'none');
                this.nameLabel.style.display = (this.calling ? 'block' : 'none');
                this.nrLabel.style.display = (this.calling ? 'block' : 'none');
                this.statusLabel.style.display = (this.calling ? 'block' : 'none');

                this.micImage.src = this.isMuted() ? "/bundles/eulogixcoolcore/gfx/phone/mic30_disabled.png" : "/bundles/eulogixcoolcore/gfx/phone/mic30.png";
            },

            isMuted: function() {
                if(!Twilio.Device.activeConnection())
                    return false;
                return Twilio.Device.activeConnection().isMuted();
            },

            isReady: function() {
                var ready = false;
                try {
                    ready = Twilio.Device.status() == 'ready';
                } catch(e) { }
                return ready;
            },

            _renderTimer: function(totalSec) {
                var hours = parseInt( totalSec / 3600 ) % 24;
                var minutes = parseInt( totalSec / 60 ) % 60;
                var seconds = totalSec % 60;

                var components = [];
                if(hours > 0) {
                    components.push( hours < 10 ? "0" + hours : hours );
                }
                components.push( minutes < 10 ? "0" + minutes : minutes );
                components.push( seconds  < 10 ? "0" + seconds : seconds );
                return components.join(':');
            }

        });

    });