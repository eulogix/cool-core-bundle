define("cool/controls/_control",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/Evented",
    "dojo/dom-construct",
    "dojo/dom-style",
    "dojo/dom-geometry",

    "dojo/_base/fx",
    "dojo/fx",
    "dojo/fx/Toggler",

    "dijit/_WidgetBase",
    'dijit/_TemplatedMixin',

    "cool/dijit/iconButton",
    "cool/dialog/manager",

    "dojo/text!./templates/_control.html"
], function(declare, lang, Evented, domConstruct, domStyle, domGeom,
            fx, coreFx, Toggler,
            _WidgetBase, _TemplatedMixin, iconButton, dialogManager,
            baseTemplate) {
 
    return declare("cool.controls._control", [_WidgetBase, Evented, _TemplatedMixin], {

        templateString: baseTemplate,

		type: '_control', //base type, override in descendants

        firstLoad : true,     //avoid firing onchange on control instantiation
        needsLabel : true,    //determines wether the rendered template should or not include a label
        holdsValue : true,     //needed for form to treat this element as a control, e.g. something that holds a value 

        initialValue : null,

        cssStyles : [],
        cssClasses : [],

        eventHandles : [],

        constructor: function(params) {
  			this.parameters = params;
  			this.definition = params.definition || {};
            this.eventHandles = [];
            this.cssStyles = [];
            this.cssClasses = [];
    	},

        /**
         * this build method builds the actual controls using the control definition.
         * We can't use the regular postCreate widget method, as when the control is instantiated
         * by gridx, the definition is not passed to the constructor.
         */
    	coolInit: function() {
	        var self = this;
            //compute css styles on parameters
            var cssStyles = this.getDefinitionElement("CSSStyles") || [];
            var cssClasses = this.getDefinitionElement("CSSClasses") || [];

            var width = this.getParameter('width');
            var align = this.getParameter('att_align');

            if(width) {
                cssStyles.push("width:"+this.getParameter('width'));
                if(width.match(/[0-9]+%/im)) {
                    domStyle.set(this.domNode, "width", width);
                    domStyle.set(this.containerTable, "width", "100%");
                } //else domStyle.set(this.fieldNode, "width", width);
            }

            if(this.getParameter('height')) {
                cssStyles.push("height:"+this.getParameter('height'));
            }

            if(align == "center") {
                domStyle.set(this.containerTable, "margin", "0 auto");
            }

            this.cssStyles = cssStyles;
            this.cssClasses = cssClasses;

	        if(this.getParameter('att_labelize')) {
              this.holdsValue = false;
              this.needsLabel = true;
              this.parameters.readonly = true;
            }

            if(this.getParameter('att_nolabel')) {
              this.needsLabel = false;
            }

            var revertToggler = this._addRevertButton();
            revertToggler.hide();

            this.on('valueInit', function(){
                self.initialValue = JSON.stringify(self.get('value')+"");
                //console.log(self.getDefinitionElement('name')+' initialValue set to'+self.initialValue);
                self.on('change', function(){
                    if(self.isChanged())
                        revertToggler.show();
                    else revertToggler.hide();
                });
            });

            this._attachDefinitionEvents();

            if(lang.exists('definition.actionButtons.length', this) && this.definition.actionButtons.length>0) {
                for(var i=0; i<this.definition.actionButtons.length; i++) {
                    var ab = this.definition.actionButtons[i];
                    this._addActionButton( ab.js, ab.icon, ab.label);
                }
            }

            if(this.getParameter('att_audit')) {
                var form = this.getContainerWidget();
                if(form && !form.getDefinitionAttribute('no_audit_trails'))
                    this.addAuditTrailButton();
            }

            if( this.getContainerWidget() ) // the control may have no container, when used to inline edit a grid
                this.getContainerWidget().on('fields_loaded', function() {
                    self.firstLoad = false;
                    self.emit("load", {});
                });

            if(this._hasTooltip()) {
                if(this.definition.tooltip.url) {
                    var f = function() {
                        var currentTooltipUrl = self.definition.tooltip.url.replace('prop_value', self.get('value'));
                        self.emit('tooltipUrlChanged', currentTooltipUrl);
                    };
                    this.on('change', f);
                    this.on('valueInit',f);
                }
            }

        },

        _hasTooltip : function() {
            return this.definition.tooltip.content || this.definition.tooltip.url;
        },

        _addActionButton: function( js, icon, label) {
            var Button = new iconButton({
                label: label,
                onClick: this._createFunction(js),
                iconSrc: icon,
                showLabel: false,
                tooltip: label
            });
            this._appendButtonNode(Button.domNode);
            return Button;
        },

        buttonCount: 0,

        _appendButtonNode: function(node){
            domStyle.set(node, "float", "left");
            domConstruct.place(node, this.buttonsNode, "first");
            this.buttonCount++;
            domStyle.set(this.buttonsNode, "display", "table-cell");
            domStyle.set(this.buttonsNode, "width", (this.buttonCount*23)+"px");
        },

        _createFunction: function (js) {
            if (typeof js === "function")
                return js;
            //necessary because if declared without eval, closure compiler modifies the name of the variable, which would later be unaccessible in the created function!
            eval(   'var control = this;'+
                    'var widget = this.getContainerWidget();'+
                    'var container = this.getContainerWidget();'    );

            return function() {
                if(js!=undefined) {
                    eval(js);
                }
            }
        },

        _addRevertButton: function() {
            var t = this;

            var revertButton = new iconButton({
                label: GlobalTranslator.trans('revertButtonLabel'),
                onClick: function() {
                    t.revertToDefinedValue();
                },
                iconSrc: "/bower_components/fugue/icons/arrow-transition-180.png",
                showLabel: false,
                tooltip: GlobalTranslator.trans('revertButtonTooltip')
            });

            this._appendButtonNode(revertButton.domNode);

            return new Toggler({
                node: revertButton.domNode,
                showFunc: fx.fadeIn,
                hideFunc: fx.fadeOut
            });
        },

        isChanged: function() {
            var ret = JSON.stringify(this.get('value') + "") != this.initialValue;
            //if(ret) console.log('current: '+JSON.stringify(this.get('value') + "") +" initial: " + this.initialValue);
            return ret;
        },

        revertToDefinedValue: function() {
            this.set('value', this.getDefinitionElement('value'));
            this.isChanged();
        },

        addAuditTrailButton: function() {
            var t = this;

            var Button = new iconButton({
                iconSrc: "/bower_components/fugue/icons/clock.png",
                showLabel: false
            });

            dialogManager.trackMouseOver(Button.domNode);

            Button.on('MouseOver', function () {
                if(!Button.trail) {
                    t.getContainerWidget().callAction('getFieldAuditTrail', function(data){
                        Button.trail = data.trail;
                        dialogManager.bindTooltip(Button.domNode, Button.trail);
                    },{ fieldName: t.getDefinitionElement('name') },{dontLock:true});
                }
            });

            Button.on('Click', function () {
                dialogManager.hideTooltip(Button.domNode);
                var d = dialogManager.openWidgetDialog('EulogixCoolCore/Audit/DSFieldAuditTrailLister', 'AUDIT TRAIL', {
                    widgetServerid: t.getContainerWidget().serverId,
                    widgetParameters: JSON.stringify( t.getContainerWidget().definition.parameters ),
                    fieldName: t.getDefinitionElement('name')
                }, null, null, null, {
                    w: 1000,
                    h: 600
                });
            });

            this._appendButtonNode(Button.domNode);
        },

        getTemplate: function(addLabel) {
            errorClass = this.definition.errors ? ' error' : '';
            var e = [];
            if(this.needsLabel) {
                e.push('<div class="controlLabelStacked'+errorClass+'">',this.definition.label,'</div>');
            }
            e.push('<div class="controlCellStacked'+errorClass+'" control_container="', this.getPlaceHolderName(), '"></div>');
            
            var definition = this.definition;
            if(definition.errors) {
                definition.errors.forEach(function(error){
                    e.push("<div>"+definition.errors.pop()+"</div>");
                });
            }
            
            var ret = e.join('');
            return ret;
        },

	    _attachDefinitionEvents: function() {
	    	if(this.definition.parameters != undefined) {
	    		var evt = this.definition.parameters.evt_onchange;
	    		if(evt!=undefined) {
	    			this.eventHandles.push( this.on("change", this._createFunction(evt)) );
	    		}
                evt = this.definition.parameters.evt_onload;
                if(evt!=undefined) {
                    this.eventHandles.push( this.on("load", this._createFunction(evt)) );
                }
	    	}
	    },

        getPlaceHolderName: function() {
            if(this.placeholder_name) {
                return this.placeholder_name;
            }
            return this.placeholder_name = 'pch'+Math.floor(Math.random() * 100000);
        },

        getParameter: function(paramName) {
            if(this.parameters && (this.parameters[ paramName ]!=undefined)) {
                return this.parameters[ paramName ];
            }
            return this.getDefinitionParameter(paramName);
        },

        getDefinitionParameter: function(parameter) {
            if(lang.exists('definition.parameters.'+parameter, this))
                return this.definition.parameters[parameter];
            return null;
        },

        getDefinitionElement: function(elementName) {
            if(lang.exists('definition.'+elementName, this))
                // pass a clone so that the original definition does not get affected if the object gets manipulated
                return lang.clone(this.definition[elementName]);
            return null;
        },

        getType: function() {
            return this.type;
        },

        getName: function() {
            return this.name;
        },

        getContainerWidget: function() {
            return this.parameters.container;
        },

        getTranslator: function() {
            return this.getContainerWidget().getTranslator();
        },

        isReadOnly: function() {
            return this.getParameter('readonly') || (this.getContainerWidget() && this.getContainerWidget().isReadOnly());
        },

        select: function() {

        },

        /**
         * basic disable/enable implementations that assumes the control has a "field" widget
         * these are usually called at runtime by Js events and commands, the enabled/disabled field
         * state may change at every form reload/action call
         */
        disable: function() {
            this.field.set('disabled', true);
        },

        enable: function() {
            this.field.set('disabled', false);
        },

        destroy: function() {
            this.inherited(arguments);
            for(var i = 0; i<this.eventHandles.length; i++) {
                this.eventHandles[i].remove();
            }
        }

    });
 
});