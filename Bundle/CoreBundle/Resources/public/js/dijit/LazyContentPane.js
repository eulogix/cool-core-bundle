define("cool/dijit/LazyContentPane",
	[
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/Deferred",
        "dojox/layout/ContentPane"
], function(declare, lang, Deferred, ContentPane) {
 
    return declare("cool.dijit.LazyContentPane", [ContentPane], {

        watcher: null,

        _setHrefAttr: function(/*String|Uri*/ href){
            var t = this;
            var doSetHref = function() {
                ContentPane.prototype._setHrefAttr.apply(t, [href]);
            };

            setTimeout(function(){
                if(t._isVisible())
                    doSetHref();
                else {
                    if(t.watcher)
                        t.watcher.unwatch();

                    t.watcher = t.getParent().watch("selectedChildWidget", function(name, oval, nval){
                        if(nval == t) {
                            t.watcher.unwatch();
                            doSetHref();
                        }
                    });
                }
            }, 100);
        },

        _isVisible: function() {
            return this.getParent().selectedChildWidget == this;
        }

    });
 
});