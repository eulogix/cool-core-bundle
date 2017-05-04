define([
    "dojo/_base/declare",
    "dojo/Evented"
], function(declare, Evented) {

    return declare("cool/push/_basePushHandler", [Evented], {

        constructor: function() {

            this.on('jsCommand', function(eventData) {
                eval('(function() {' + eventData._eventData.js + '}());')
            });

            //this.on('callClosed', function(eventData) {});

            //this.on('userNotification', function(eventData) {});
        },

        handle: function(topic, data) {
            if(data._eventType)
                this.emit(data._eventType, data);
        }

    });

});