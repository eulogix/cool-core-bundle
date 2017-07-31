define("cool/form",
        [
            "cool/cool",
            "cool/_widget",
            "cool/formRenderer",
            "cool/functions/popup",
            "dojo/html",
            "dojo/_base/lang",
            "dojo/_base/array", // array.filter array.forEach array.map
            "dojo/_base/declare",
            "dojo/dom", 
            "dojo/dom-class",
            "dojo/dom-style",
            "dojo/dom-attr",
            "dojo/dom-construct",
            "dojo/request/xhr",
            "dojo/parser",
            "dojo/Deferred",

            //"dojox/lang/functional/object",

            "dijit/registry"
        ],
        function(cool, cwidget, formRenderer, coolPopup, html, lang, array, declare, dom, domClass, domStyle, domAttr, domConstruct, xhr, parser, Deferred,
                 registry
            ){
  
            return declare("cool.form", [cwidget], {

                /**
                 * if set, the form will auto scroll the page to itself upon loading/reloading
                 */
                autoScrollToMe: false,

                constructor: function() {
                    this._fields = {};
                },

                onBindSuccess: function( data ) {
                    return this.inherited(arguments); // builds definition
                },
                
                /**
                * submits the form, and parses the result as an object of commands/definitions
                * 
                */
                submit : function() {
                    this.callAction('submit');
                },
                
                /**
                * destroys the widgets
                * 
                */
                clear: function() {
                    var t = this;

                    this.destroyFields();

                    if(this.contentNode!==undefined) {
                        this.contentNode.innerHTML = '';
                    }

                    t.clearSlots();

                    for(var slotName in this.skippedSlotsTargets) {
                        t.clearSlot(slotName);
                    }

                    this.inherited(arguments);
                },
                
                rebuildNeeded: function(definition) {
                    //even if the definition of the fields is the same, we must rebuild the form as the values of all the fields may not match with the definition itself
                    //TODO: a more correct approach would be not to rebuild the form if the condition below is false BUT cycle on fields and set their value to the value in the form definition
                    return true;
                    /*return this.inherited(arguments) ||
                           definition.hasOwnProperty('layout') ||
                           definition.hasOwnProperty('fields') ||
                           definition.hasOwnProperty('constraints');*/
                },

                /**
                 * creates the required widgets and links them to the form field container
                 *
                 * @param fieldName
                 * @param layoutProps
                 */
                buildFormElement: function (fieldName, layoutProps) {
                    var t = this;
                    var fieldDefinition = this.definition.fields[ fieldName ] || {};
                    layoutProps = layoutProps || {};
                    
                    var props = lang.mixin({  
                        definition:fieldDefinition,
                        name: fieldName,
                        container: this
                    }, layoutProps);

                    var field = {};

                    var deferred = new Deferred();

                    require([fieldDefinition.coolDojoWidget, "dojo/domReady!"], function(controlWidget){

                        field = new controlWidget(props);
                        field.coolInit();

                        if(field.holdsValue) {
                            t.addField(fieldName, field);
                            field.on('change', function(){ t.emit('change', field); });
                        }

                        deferred.resolve(field);

                    });

                    return deferred;
                },
                
                addField: function(fieldName, field) {
                    this.own(field);
                    this._fields[fieldName] = field;    
                },

                /*
                returns a field, an element with a value
                 */
                getField: function(fieldName) {
                    return this._fields[fieldName];
                },

                hasField: function(fieldName) {
                    return lang.exists(fieldName, this._fields);
                },

                /*
                returns an associative array of fields (the elements that actually hold a value) of the form
                 */
                getFields: function() {
                    return this._fields;
                },


                destroyFields: function() {
                    var fields = this.getFields();
                    for(var fieldName in fields) {
                        fields[ fieldName ].destroyRecursive();
                    }
                    this._fields = {};
                },

                isChanged: function() {
                    var fields = this.getFields();
                    for(var fieldName in fields) {
                        if(fields[ fieldName ].isChanged()) {
                            return true;
                        }
                    }
                    return false;
                },

                revertToDefinedValues: function() {
                    var fields = this.getFields();
                    for(var fieldName in fields) {
                        if(fields[ fieldName ].isChanged()) {
                            fields[ fieldName].revertToDefinedValue();
                        }
                    }
                },

                skippedSlotsTargets: {},

                /**
                * recreates the widgets
                * 
                */
                rebuild: function() {

                    var d = new Deferred();
                    var widget = this;

                    this.inherited(arguments);

                    this.skippedSlotsTargets = {};

                    var div = dojo.doc.createElement('div');

                    var renderer = new formRenderer(this);

                    renderer.render(div).then(function(instances){

                        //mark slots that have a placeholder in the template as skippable
                        var q = "[slot_container]";
                        dojo.query(q, div).forEach(function(node) {
                            widget.skippedSlotsTargets[ domAttr.get(node,'slot_container') ] = node;
                        });

                        widget.registerViewWithButton('fields', function(){ return div; }, '/bower_components/fugue/icons/application-form.png');
                        widget.setActiveView('fields');

                        array.forEach(instances, function(instance){
                            widget.own(instance);
                            widget.destroyOnClearList.push(instance);
                        });

                        if(widget.autoScrollToMe) {
                            //scroll slowly the first time, as fast as possible from then on as the form is already visible and in place
                            //so we try to minimize the glitch for the user
                            widget.scrollToMe(widget.firstScroll == undefined ? 500 : 1);
                            widget.firstScroll = false;
                        }

                        widget.renderSkippedSlots();

                        d.resolve();
                        setTimeout(function(){ widget.emit('fields_loaded', {}); }, 100);
                    });

                    this.addAction('RESET', {
                        label: this.getCommonTranslator().trans("RESET_FORM"),
                        onClick: function() {
                            widget.revertToDefinedValues();
                        },
                        icon: '/bower_components/fugue/icons/arrow-transition-180.png',
                        group:'TITLE'
                    });

                    //wait a bit for controls to settle
                    setTimeout(function(){
                        widget.updateChangedVisualStatus();
                        widget.on('change', function(control){
                            widget.updateChangedVisualStatus();
                        });
                    },500);

                    return d;
                },

                updateChangedVisualStatus: function() {
                    var changed = this.isChanged();
                    if(this.containerWindow) {
                        if(changed)
                            this.containerWindow.colorOrange();
                        else this.containerWindow.resetColor();
                    }

                    var resetBtn = this.getActionButton('RESET');
                    if(resetBtn) {
                        resetBtn.set('disabled', !changed);
                        if(changed)
                             domStyle.set(resetBtn.domNode, 'display', 'inline');
                        else domStyle.set(resetBtn.domNode, 'display', 'none');
                    }
                },

                /**
                 * this function can be overridden in derived classes to provide a different rendering
                 * mechanism for some slots
                 */
                _skipNormalSlotRender: function(slotName) {
                    var ret = (this.skippedSlotsTargets[ slotName ] != undefined);
                    return ret;
                },


                renderSkippedSlots: function() {
                    if(this.definition.slots != undefined) {
                        for(var groupName in this.definition.slots) {
                            for(var slotName in this.definition.slots[groupName]) {
                                if (this._skipNormalSlotRender(slotName) ) {
                                    var isDelayed = domAttr.get(this.skippedSlotsTargets[slotName],'_delayed') == 'true';
                                    if(!isDelayed)
                                        this._putSlot(this.definition.slots[groupName][slotName], slotName, this.skippedSlotsTargets[slotName]);
                                }
                            }
                        }
                    }
                },

                _getSystemMenuItems: function() {
                    var p = this.inherited(arguments);
                    var widget = this;
                    if(lang.exists('definition.attributes._configurable', this) && this.definition.attributes._configurable) {
                        p.push({
                            label: this.getCommonTranslator().trans("EDIT FORM"),
                            onClick: function() {
                                var popup = coolPopup.open(Routing.generate('_coolFormEditor'), "editorWindow_"+widget.definition.attributes.id, "directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes", 1000, 650, "CENTER");
                                window.widgetInEdit = widget;
                            },
                            icon: '/bower_components/fugue/icons/table.png'
                        });
                    }
                    return p;
                },

                /**
                 * sets up a tabcontainer so that its selection state is maintained through submissions
                 * @param tabContainerId
                 * @param opts
                 */
                setupTabContainer: function( tabContainerId, opts ) {
                    var options = opts || {};

                    var tabs = registry.byId( tabContainerId );
                    var self = this;
                    var activeTab = this.getDefinitionParameter('_activeTab');

                    tabs.watch('selectedChildWidget', function(name, oval, nval){
                        var selectedName = nval._name;
                        self.setDefinitionParameter( '_activeTab', selectedName);

                        if(lang.exists('delayedSlots', options)) {
                            for(var tabName in options.delayedSlots)
                                if(tabName == selectedName) {
                                    array.forEach(options.delayedSlots[tabName], function(slotName){
                                        self._putSlot(self._getSlotDefinitionByName(slotName), slotName, self.skippedSlotsTargets[slotName]);
                                    });
                                }
                        }

                    });

                    var children = tabs.getChildren();
                    for(var i in children) {
                        if(children[i]._name == activeTab)
                            tabs.selectChild( children[i] );
                    }
                },

                getValues: function(prefix) {
                    var wkPrefix = prefix || '';
                    var values = {};
                    var fields = this.getFields();
                    var fv;
                    for (var fieldName in fields) {
                        fv = fields[ fieldName ].get('value');
                        values[ wkPrefix + fieldName ] = fv;
                    }
                    return values;
                },

                dump: function() {
                    console.log(this.getValues());
                },

                /**
                 * checks if there are some file fields that have unfinished uploads pending
                 */
                hasPendingUploads: function() {
                    var fields = this.getFields();
                    for(var fieldName in fields) {
                        var field = fields[ fieldName ];
                        if( (field.getType() == 'file') && field.isWaiting() ) {
                            return true;
                        }
                    }
                    return false;
                },

                /**
                 * hook that returns the parameters that the widget sends to the controller along with the action specs
                 * @return dojo.Deferred
                 */
                getActionValues: function() {
                    var deferred = new Deferred();

                    this._checkUploads(deferred);
                    return deferred;
                },

                _checkUploads: function(deferred) {
                    var t = this;
                    if(this.hasPendingUploads()) {
                        setTimeout(function(){
                            t._checkUploads(deferred);
                        },100);
                    } else {
                        deferred.resolve( this.getValues() );
                    }
                }
                        
        });
  
});
