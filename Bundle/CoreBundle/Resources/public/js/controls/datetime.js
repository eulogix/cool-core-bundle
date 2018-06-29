define("cool/controls/datetime",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dijit/form/DateTextBox",
    "dijit/form/TimeTextBox"
], function(declare, lang, array, _control, DateTextBox, TimeTextBox) {
 
    return declare("cool.controls.datetime", [_control], {
		
    	coolInit : function() {
            this.inherited(arguments);
            var control=this;

            if(this._showDate()) {
                var dateField = new DateTextBox(lang.mixin(this.parameters,{
                    "class": this.cssClasses.join(' '),
                    "style": this.cssStyles.join(';')
                }), dojo.doc.createElement('div'));
                this.fieldNode.appendChild(dateField.domNode);
                this.own(dateField);
                this.dateField = dateField;

                if(this.isReadOnly()) {
                    dateField.set('readOnly', true);
                    dateField.set('disabled', true);
                }
            }

            if(this._showTime()) {
                var timeField = new TimeTextBox(lang.mixin(this.parameters,{
                    style: this.cssStyles.join(';')
                }), dojo.doc.createElement('div'));
                this.fieldNode.appendChild(timeField.domNode);
                this.own(timeField);
                this.timeField = timeField;

                if(this.isReadOnly()) {
                    timeField.set('readOnly', true);
                    timeField.set('disabled', true);
                }
            }

            if(timeField)
                timeField.on("change", function(){
                    if(!control.firstLoad)
                        control.emit("change", {});
                });
            if(dateField)
                dateField.on("change", function(){
                    if(!control.firstLoad)
                        control.emit("change", {});
                });

            if(this.definition.hasOwnProperty('value')) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
		},

        disable: function() {
            if(this._showDate()) this.dateField.set('disabled', true);
            if(this._showTime()) this.timeField.set('disabled', true);
        },

        enable: function() {
            if(this._showDate()) this.dateField.set('disabled', false);
            if(this._showTime()) this.timeField.set('disabled', false);
        },

		_setValueAttr: function(value) {
            if(value=='') value = null;

            //we remove seconds as the timebox is unable to represent them.
            //leaving them in place would cause the timebox to render empty
            if(value) {
                /*
                    convert PG timestamp into iso, not needed in chrome but in all the other browsers
                     from
                     2015-03-11 20:13:11.378701
                     2014-11-25T00:00:00+0100
                     2014-11-25T00:00:00+0100
                     2000-12-01T00:00:00.000Z
                     2014-11-25

                     to 2015/03/11 20:13:11
                 */
                properDate = value.replace(/-/g,'/');
                var rx = /^(.+?)(|[ T](.*?)(\.[0-9Z]+|\+[0-9:]+))$/im;
                properDatePortion = properDate.replace(rx, "$1");
                properTimePortion = properDate.replace(rx, "$3");
                properDate = properDatePortion + ( properTimePortion ? " "+properTimePortion : "");

                var date = new Date(properDate);
                date.setUTCSeconds( 0 );
                date.setUTCMilliseconds( 0 );
                value = date.toISOString();
            }

            if(this._showDate())
                this.dateField.set('value', value);
            if(this._showTime()) {
			    this.timeField.set('value', value);
            }
		},

		_getValueAttr: function() {

            if(this._showDate()) {
                var dateDate = this.dateField.get('value');
                //this is a hack to avoid dojo localizing the dates stored in the server. This way date controls are always wysiwyg
                if(dateDate)
                    dateDate.setTime( dateDate.getTime() - dateDate.getTimezoneOffset() * 60 * 1000 );
            }

            if(this._showTime()) {

                var defaultTime = new Date(); defaultTime.setHours(9,0,0);
                var timeDate = this.timeField.get('value') || defaultTime;

                //this is a hack to avoid dojo localizing the dates stored in the server. This way date controls are always wysiwyg
                timeDate.setTime( timeDate.getTime() - timeDate.getTimezoneOffset() * 60 * 1000 );

                if(this._showDate() && dateDate) {
                    dateDate.setUTCHours( timeDate.getUTCHours() );
                    dateDate.setUTCMinutes( timeDate.getUTCMinutes() );
                    dateDate.setUTCSeconds( timeDate.getUTCSeconds() );
                }
            }

            if(this._showDate())
                return dateDate ? dateDate.toISOString() : null;
            return timeDate ? timeDate.toISOString() : null;
		},

        _showDate: function() {
            return this.definition.type == 'date' || this.definition.type == 'datetime';
        },

        _showTime: function() {
            return this.definition.type == 'time' || this.definition.type == 'datetime';
        }


    });
 
});