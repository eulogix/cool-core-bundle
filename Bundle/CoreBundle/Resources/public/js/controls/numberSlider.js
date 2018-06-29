define("cool/controls/numberSlider",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dijit/form/HorizontalSlider",

    'dijit/_TemplatedMixin',
    'dijit/_WidgetsInTemplateMixin',

    "dojo/text!./templates/numberSlider.html"
], function(declare, lang, array, _control, Slider, _TemplatedMixin, _WidgetsInTemplateMixin, widgetTpl) {
 
    return declare("cool.controls.numberSlider", [_control, _TemplatedMixin, _WidgetsInTemplateMixin], {

        templateString: widgetTpl,

        coolInit : function() {
            this.inherited(arguments);

            var self=this;

            var min = this.getParameter('from') || 0;
            var max = this.getParameter('to');
            var field = new Slider({
                minimum: min,
                maximum: max,
                intermediateChanges: true,
                discreteValues: 1+max-min,
                //style: "width:300px;",
                onChange: function(value){
                    self.labeldiv.innerHTML = value;
                    self.emit("change", {});
                }
            }, this.sliderdiv);

            this.own(field);
            this.field = field;

            if(this.isReadOnly()) {
                field.set('readOnly', true);
                this.disable();
            }

            if(this.definition.hasOwnProperty('value')) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
        },

		_setValueAttr: function(value) {
            if(isNaN(value)) {
                this.field.set('value', null );
            }
            else this.field.set('value', value );
		},

		_getValueAttr: function() {
            var ret = this.field.get('value');
            return isNaN( ret ) ? null : ret;
		}


    });
 
});