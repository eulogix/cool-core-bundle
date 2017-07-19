define("cool/util/GridUtils", [
    "dojo/dom-class",
    "dojo/dom-style",
    "dojo/dom-geometry",

    'gridx/Grid',
    "dijit/registry",

    "dojo/date/locale",

    'gridx/core/model/cache/Sync',
    'gridx/core/model/cache/Async',

    'dojo/store/Memory',
    'gridx/allModules',

    "gridx/modules/Tree"
], function(domClass, domStyle, domGeometry,
            Grid, registry, dateLocale,
            SyncCache, AsyncCache,

            memoryStore,
            mods, Tree) {
  
    var obj = {

        renderSimpleGrid: function(data, title, targetNode) {
            var store = new memoryStore({
                data: data
            });

            store.hasChildren = function(id, item){
                return item.children != undefined;
            };

            store.getChildren = function(item){
                return item.children;
            };

            var layout = [];
            for(var colName in data[0]) {
                layout.push({ id: colName, field: colName, name: colName});
            }

            console.log(data);

            var grid = Grid({
                cacheClass: SyncCache,
                store: store,
                structure: layout,
                style: 'width:100%;',
                autoHeight: true,
                modules: [
                    Tree
                ]
            });

            if(title) {
                var div = dojo.doc.createElement('div');
                domStyle.set(div, 'width', '100%');
                div.innerHTML = title;
                targetNode.appendChild(div);
            }

            grid.placeAt( targetNode );

            grid.startup();
        }
        
    };

    return obj;
  
});
    