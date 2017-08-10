define("cool/workflow/taskEditor",
    [
        "dojo/_base/declare",
        "dojo/dom-style",
        "dojo/dom-class",
        "dojo/dom-geometry",
        "dojo/Deferred",
        "cool/cool",
        "cool/_widget",
        "dojo/_base/lang",
        "dojo/_base/array",
        "dojo/promise/all",

        "cool/form",
        "cool/lister"

    ],

function(declare, domStyle, domClass, domGeometry, Deferred, cool, _cwidget, lang, array, all, coolForm, coolLister) {

    return declare("cool.workflow.taskEditor", [_cwidget], {

        actualForm: null,

        //very basic implementation that ignores everything but the actual form, and renders it directly in the contentNode
        renderSlots: function() {

            var t = this;

            this.clearSlots();

            this.contentNode.innerHTML = '';

            try {
                var slot = this.definition.slots._base.actualForm;
                var d =  this._putSlot(slot, 'actualForm', this.contentNode);
                d.then(function(actualForm){
                    t.actualForm = actualForm;
                    //sets the slot container to fit the whole div
                    domStyle.set(actualForm.domNode.parentNode, {
                        "height": "100%",
                        "padding": 0
                    });
                });
                return d;
            } catch(e) {

                var d = new Deferred();
                d.resolve();
                return d;
            }

        },

        resize: function() {
            this.inherited(arguments);
            if(this.actualForm)
                this.actualForm.containerWindow.resize();
        }

    });

});