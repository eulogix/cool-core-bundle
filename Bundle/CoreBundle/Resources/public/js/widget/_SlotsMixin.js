define("cool/widget/_SlotsMixin",
        [
            "dojo/_base/lang",
            "dojo/_base/declare",
            "dojo/_base/array",
            "dojo/dom-style",
            "dojo/dom-geometry",
            "dojo/Deferred",
            "dijit/layout/ContentPane",
            "dijit/layout/TabContainer",
            "cool/cool"
        ],
        function(lang, declare, array, domStyle, domGeometry, Deferred,
                 ContentPane, TabContainer,
                 cool
            ) {
  
            return declare("cool.widget._SlotsMixin", [], {

                slotsTab : null,
                widgetSlots: {}, //keeps a reference of slotted widgets

                slotPanes: {}, //tracks the contentPanes of the tabcontainer, key is the slot group

                constructor: function() {
                    this.widgetSlots = {};
                    this.slotPanes = {};
                },

                renderSlots: function() {

                    var self = this;
                    var definition = this.definition;

                    var d = new Deferred();

                    this.clearSlots();

                    var regularSlots = this.getRegularSlotsFlat();
                    if(regularSlots.length > 0) {

                        this.slotsNode.className = "slotsNode";

                        //render first the base tab
                        if(lang.exists("slots._base", definition)) {
                            this._putSlots(this.slotsNode, definition.slots._base);
                        }

                        var addTabContainer = false;
                        for(var groupName in definition.slots) {
                            if(groupName != '_base') {
                                for(var slotName in definition.slots[groupName])
                                    if(!this._skipNormalSlotRender(slotName))
                                        addTabContainer = true;
                            }
                        }

                        if(addTabContainer) {

                            var slotsTab = new TabContainer({
                                nested: true,
                                doLayout: false  //flexible height
                            });

                            for(groupName in definition.slots) {

                                var addTabElement = false;
                                for(slotName in definition.slots[groupName])
                                    if(!this._skipNormalSlotRender(slotName))
                                        addTabElement = true;

                                if(addTabElement) {
                                    var tabElem = new ContentPane({
                                        title: this.getTranslator().trans( groupName ),

                                        doLayout: false,

                                        onShow: (function(gpName){
                                                    return function() {
                                                        domStyle.set(this.domNode, 'width', 'inherit');
                                                        domStyle.set(this.domNode, 'overflow', 'hidden');

                                                        //needed only when this property gets screwed by scrollToMe
                                                        this.domNode.scrollTop = 0;

                                                        self._putSlots(this.domNode, definition.slots[gpName]);
                                                    }
                                                }(groupName)),

                                        onHide: function() {
                                            /**
                                             * necessary because when there is a gridx in there with hscroller, and the total
                                             * columns width exceeds the viewport width, the parent width of the container gets
                                             * screwed. With this statement (and its counterpart in onShow()) we avoid this from
                                             * happening.
                                             */
                                            domStyle.set(this.domNode, 'width', '0px');
                                        }
                                    });

                                    //small delay to ensure that the first tab renders correctly (otherwise the onShow event may not fire)
                                    (function(cpar){
                                        self.slotPanes[groupName] = cpar;
                                        setTimeout(function(){ slotsTab.addChild(cpar); }, 10);
                                    }(tabElem));
                                }

                            }

                            slotsTab.placeAt( this.slotsNode );
                            slotsTab.startup();

                            this.own(slotsTab);

                            this.slotsTab = slotsTab;
                        }
                    }
                    d.resolve();
                    return d;
                },

                focusSlot: function(slotName) {
                    var t = this;
                    if(!this._skipNormalSlotRender(slotName)) {
                        array.forEach(this.getAllSlotsFlat(), function(slot){
                            if(slot.name == slotName && t.slotsTab) {
                                console.log(t.slotPanes[slot.group]);
                                t.slotsTab.selectChild(t.slotPanes[slot.group]);
                            }
                        });
                    }
                },

                clearSlots: function() {
                    for(var slotName in this.widgetSlots)
                        if(!this._skipNormalSlotRender(slotName))
                            this.clearSlot(slotName);

                    if(this.slotsTab)
                        this.slotsTab.destroyRecursive();

                    this.slotsNode.className = "hiddenNode";
                    this.slotsNode.innerHTML = '';
                },

                clearSlot: function(slotName) {
                    if(this.widgetSlots[slotName]) {
                        this.widgetSlots[slotName].destroyRecursive();
                    }
                    delete this.widgetSlots[slotName];
                },

                /**
                 * this function can be overridden in derived classes to provide a different rendering
                 * mechanism for some slots
                 */
                _skipNormalSlotRender: function(slotName) {
                    return false;
                },

                /**
                 * instances some slots and attaches their domnodes to a node
                 */
                _putSlots: function(target, slots) {
                    for(var slotName in slots) {
                        if(!this._skipNormalSlotRender(slotName)) {
                            this._putSlot(slots[slotName], slotName, target).then(function(newWidget) {
                            });
                        }
                    }
                },

                _putSlot: function(slot, slotName, target, onSuccess) {
                    var d = new Deferred();

                    if(lang.exists(slotName, this.widgetSlots)) {
                        d.resolve(this.widgetSlots[slotName]);
                        return d;
                    }

                    /**
                     * sometimes this method gets called twice in very quick sequence, because contentPane fires the onChange event
                     * oddly. This ensures that only the first call effectively instantiates the widget, preventing errors in dijit registry
                     */
                    this.widgetSlots[slotName] = null;

                    var widget = this;

                    if(!lang.isFunction(onSuccess))
                        onSuccess = function(){};

                    switch(slot.type) {
                        case 'widget' : {
                            var content = dojo.doc.createElement('div');
                            content.className = "slotContainer";
                            target.appendChild(content);
                            cool.widgetFactory(slot.serverId, slot.parameters, function(widgetInstance){
                                    widgetInstance.placeAt(content);
                                    widgetInstance.parentWidget = widget;
                                    widget.widgetSlots[slotName] = widgetInstance;
                                    widget.own(widgetInstance);
                                    onSuccess(widgetInstance);
                                    d.resolve(widgetInstance);
                                },
                                null,
                                slot.hasOwnProperty('widgetDefinition') ? slot.widgetDefinition : undefined,
                                slot.hasOwnProperty('dojoParameters') ? slot.dojoParameters : undefined);
                            break;
                        }
                    }

                    return d;
                },

                _getSlotDefinitionByName: function(slotName) {
                    var ret = false;
                    array.forEach(this.getAllSlotsFlat(), function(slot){
                        if(slot.name == slotName)
                            ret = slot.slot;
                    });
                    return ret;
                },

                hasSlots: function() {
                    //slots is passed from the server as an empty array if empty, or as a populated object if filled.
                    //because of this, we check if an empty array is passed by comparing length to 0.
                    //when it is an object, length is undefined and the condition is satisfied
                    return this.definition.slots != undefined && this.definition.slots.length != 0;
                },

                hasSlot: function(slotName) {
                    return this._getSlotDefinitionByName(slotName) !== false;
                },

                getAllSlotsFlat: function() {
                    var ret = [];
                    var definition = this.definition;

                    if(definition.slots != undefined)
                        if(definition.slots.length != 0)
                            for(var groupName in definition.slots)
                                for(var _slotName in definition.slots[groupName])
                                        ret.push({ 'name': _slotName, 'group': groupName, 'slot': definition.slots[groupName][_slotName]});

                    return ret;
                },

                getRegularSlotsFlat: function() {
                    var t = this;
                    return array.filter(this.getAllSlotsFlat(), function(slot){
                        return !t._skipNormalSlotRender(slot.name);
                    });
                }

        });
  
});
