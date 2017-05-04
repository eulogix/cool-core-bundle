define("cool/store/lister", 
	[ "dojo/_base/declare","cool/store/xhrstore"],

function(declare, xhrStore){

return declare("cool.store.lister", xhrStore, {

	constructor: function(options){
		this.widget = {};
		declare.safeMixin(this, options);
	},

    idProperty: '_recordid',

    widget: {},

    // the PUT hook looks in the returned data if a new widget definition has been provided
    _onPutSuccess: function(data) {
        if(data._widgetDefinition != undefined) {
            this.widget.onBindSuccess( data._widgetDefinition );
            store.lastErrors = data._errors || [];
            store.lastStatus = data._success;

            delete data._widgetDefinition;
            delete data._errors;
            delete data._success;
        }
    },

    // updates the lister summary module
    _onQuerySuccess: function(data) {
        if(this.widget.grid) {
            if(this.widget.grid.summaryModule && data._summary) {
                this.widget.grid.summaryModule.setSummaryData(data._summary);
            }
        }
    }

});

});