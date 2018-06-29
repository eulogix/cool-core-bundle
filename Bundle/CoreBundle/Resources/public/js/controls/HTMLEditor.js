define("cool/controls/HTMLEditor",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dijit/Editor", 
    "dijit/_editor/plugins/AlwaysShowToolbar", 
    "dijit/_editor/plugins/ViewSource",  
    "dojo/dom", "dojo/query"
], function(declare, lang, array, _control, Editor, AlwaysShowToolbar, ViewSource, dom, query) {
 
    return declare("cool.controls.HTMLEditor", [_control], {

        tempValue: null,

    	coolInit : function() {
            this.inherited(arguments);

            var control = this;

            var field = new Editor({
                html:'',
                height:this.getParameter('height'),
                extraPlugins: [AlwaysShowToolbar, ViewSource],
                styleSheets: '/bundles/eulogixcoolcore/js/css/htmleditor/editorarea.css'
            }, dojo.doc.createElement('div'));

            field.startup();

            this.fieldNode.appendChild(field.domNode);
            this.own(field);
            this.field = field;

            if(this.isReadOnly()) {
                field.set('readOnly', true);
                this.disable();
            }

            field.on("change", function(){
                control.tempValue = field.get('value');
                if(!control.firstLoad)
                    control.emit("change", {});
            });

            if(this.definition.hasOwnProperty('value')) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
  		},

        /**
         * dijit/Editor seems to set value asynchronously, but the method does not return a promise
         * for that reason we buffer the value, to achieve a consistent behavior
         */
  		_setValueAttr: function(value) {
  		    this.tempValue = value || '';
            this.field.set('value', this.tempValue);

            if(!this.firstLoad)
                this.emit("change", {}); //for some reason, dijit/Editor does not fire onchange if the new value is set programmatically
  		},

  		_getValueAttr: function() {
  			return this.tempValue;
  		}


    });
 
});