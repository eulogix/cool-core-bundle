define("cool/controls/number",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dijit/form/NumberTextBox",

    "dojo/currency",
    "dojo/i18n!dojo/cldr/nls/it/currency",
    "dojo/i18n!dojo/cldr/nls/it/number"

], function(declare, lang, array, _control, NumberTextBox) {
 
    return declare("cool.controls.number", [_control], {
		
        coolInit : function() {
            this.inherited(arguments);

            var control=this;

            var field = new NumberTextBox(lang.mixin(this.parameters,{
                "class": this.cssClasses.join(' '),
                "style": this.cssStyles.join(';')
            }), dojo.doc.createElement('div'));

            this.fieldNode.appendChild(field.domNode);
            this.own(field);
            this.field = field;

            field.on("change", function(){
                if(!control.firstLoad)
                    control.emit("change", {});
            });

            if(this.isReadOnly()) {
                field.set('readOnly', true);
                this.disable();
            }

            var constraints  = {};

            if(this.getParameter('from')) {
                constraints.min = this.getParameter('from');
            }
            if(this.getParameter('to')) {
                constraints.max = this.getParameter('to');
            }
            if(this.getParameter('places')) {
                var p = parseInt( this.getParameter('places') );
                constraints.places = '0,'+p;

                var pattern = (p>0?'#############################.':'');
                for(var i=0;i<p;i++)
                    pattern+='#';
                if(pattern)
                    constraints.pattern = pattern;
            }

            field.set('constraints', constraints);

            if(this.definition.hasOwnProperty('value')) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
        },

		_setValueAttr: function(value) {
            if(isNaN(value) || value===null || value=='') {
                this.field.set('value', null );
            }
            else this.field.set('value', Number(value) );
		},

		_getValueAttr: function() {
            var rv = this.field.get('value');
            var ret = isNaN( rv ) ? null : rv;
            return ret;
		}


    });
 
});