define("cool/workflow/taskEditor",
    [
        "dojo/_base/declare",
        "dojo/Deferred",
        "cool/cool",
        "cool/_widget",
        "dojo/_base/lang",
        "dojo/_base/array",
        "dojo/promise/all",

        "cool/form",
        "cool/lister"

    ],

function(declare, Deferred, cool, _cwidget, lang, array, all, coolForm, coolLister) {

    return declare("cool.workflow.taskEditor", [_cwidget], {

        //very basic implementation that ignores everything but the actual form, and renders it directly in the contentNode
        renderSlots: function() {
            this.contentNode.innerHTML = '';

            try {
                var slot = this.definition.slots._base.actualForm;
                this._putSlot(slot, 'actualForm', this.contentNode);
            } catch(e) {}

        }

    });

});