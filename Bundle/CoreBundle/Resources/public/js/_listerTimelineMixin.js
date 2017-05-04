define("cool/_listerTimelineMixin",
    [
        "dojo/_base/lang",
        "dojo/_base/array",
        "dojo/_base/declare",

        "dojo/date",
        "dojo/date/stamp",

        "dijit/form/DropDownButton",
        "dijit/Menu",
        "dijit/MenuItem",
        "dijit/CheckedMenuItem",
        "dijit/MenuSeparator",
        "dijit/PopupMenuItem",

        "cool/cool"
    ],
    function(lang, array, declare,
             date, stamp,
             DropDownButton, Menu, MenuItem, CheckedMenuItem, MenuSeparator, PopupMenuItem,
             cool
           ){

    /**
     * this mixin provides functionalities for the timeline view of the lister
     */
    return declare("cool._listerTimelineMixin", [], {

        tlColorBy : null,
        tlDateField : null,

        tlDateFieldSelectors : null,
        tlColorBySelectors : null,

        setUpTimelineView: function() {
            var t = this;

            var timelineColumns = t.getDefinitionAttribute("timeline_columns");
            var timelineGroups = t.getDefinitionAttribute("timeline_group_columns");

            var dateFieldSelectors = {};
            var colorBySelectors = {};

            if(timelineColumns) {

                var timelineView;

                var timelineMenu = new Menu({});

                for(var i=0; i<timelineColumns.length; i++) {

                    dateFieldSelectors[ timelineColumns[i] ] = new CheckedMenuItem({
                        label: t.getTranslator().trans( timelineColumns[i] ),

                        onChange: (function(fieldName) {
                            return function(checked) {
                                var previousValue = t.tlDateField;

                                if(checked) {
                                    t.tlDateField = fieldName;
                                } else if(t.tlDateField == fieldName)
                                    t.tlDateField = null;

                                t.refreshTimeLineMenu();

                                t.setActiveView('timeline').then(function(){
                                    if(t.tlDateField != previousValue) {
                                        t.timelineData.clear();
                                        t._reloadTimeLineData();
                                    }
                                });
                            }
                        })(timelineColumns[i])

                    });

                    timelineMenu.addChild(dateFieldSelectors[ timelineColumns[i] ]);

                }

                t.tlDateFieldSelectors = dateFieldSelectors;

                if(timelineGroups.length > 0) {

                    timelineMenu.addChild(new MenuSeparator());

                    var timelineGroupsMenu = new Menu({});

                    for(i=0; i<timelineGroups.length; i++) {
                        colorBySelectors[ timelineGroups[i] ] = new CheckedMenuItem({
                            label: t.getTranslator().trans( timelineGroups[i] ),

                            onChange: (function(fieldName) {
                                return function(checked) {

                                    if(checked) {
                                        t.timelineColorBy(fieldName);
                                    } else if(t.tlColorBy == fieldName)
                                        t.timelineColorBy(null);

                                    t.refreshTimeLineMenu();

                                }
                            })(timelineGroups[i])

                        });

                        timelineGroupsMenu.addChild(colorBySelectors[ timelineGroups[i] ]);
                    }

                    timelineMenu.addChild(new PopupMenuItem({
                        label: t.getCommonTranslator().trans( "TIMELINE_GROUP_BY" ),
                        iconClass:"arrowSubMenuIcon",
                        popup:timelineGroupsMenu
                    }));
                }

                t.tlColorBySelectors = colorBySelectors;

                var timelineButton = new DropDownButton({
                    label: '<img src="/bower_components/fugue/icons/film-timeline.png" class="icon">&nbsp;'+t.getCommonTranslator().trans("TIMELINE"),
                    dropDown: timelineMenu,
                    style: {"float":"right"}
                });

                t.addActionButton('TIMELINE', timelineButton, 'TITLE');
                t.timelineButton = timelineButton;

                t.registerView('timeline', function(){
                    timelineView = timelineView || t.rebuildTimeLineView();
                    return timelineView;
                });

            }
        },

        refreshTimeLineMenu: function() {
            for(var k in this.tlDateFieldSelectors)
                this.tlDateFieldSelectors[k].set('checked', k==this.tlDateField);
            for(k in this.tlColorBySelectors)
                this.tlColorBySelectors[k].set('checked', k==this.tlColorBy);
        },

        timelineColorBy: function(columnName) {
            var t = this;
            this.tlColorBy = columnName;
            var distinct = [];

            if(columnName) {
                this.timelineData.forEach(function(item){
                    if(item.rowData[columnName] && (distinct.indexOf(item.rowData[columnName]) == -1))
                        distinct.push(item.rowData[columnName]);
                });

                for(var i=0; i<distinct.length; i++) {
                    var color = this.rainbow(distinct.length,i);

                    var items = this.timelineData.get({
                        filter: function (item) {
                            return (item.rowData[columnName] == distinct[i]);
                        }
                    });

                    for(var k=0; k<items.length; k++) {
                        items[k].style="background-color: "+color+";";
                        items[k].title = items[k].rowData._decodifications[columnName];
                    }

                    this.timelineData.update(items);
                }

            } else {
                this.timelineData.forEach(function(item){
                    t.timelineData.update(lang.mixin(item, {style:"", title:""}));
                });
            }

            this.timeline.redraw();
        },


        rebuildTimeLineView: function() {
            var div = dojo.doc.createElement('div');
            var startDate = new Date();

            var lister = this;

            this.timelineData = new vis.DataSet({});

            var options = {
                locale: dojoConfig.locale,
                minHeight: 100,
                maxHeight: 400,
                tooltip: {
                    followMouse: true,
                    overflowMethod: 'cap'
                }
            };

            var timeline = new vis.Timeline(div, this.timelineData, options);

            timeline.setWindow({
                start:  stamp.toISOString(date.add(startDate, "week", -1)),
                end:   stamp.toISOString(startDate)
            });

            this.timeline = timeline;

            timeline.on('rangechanged', function (properties) {
                lister._reloadTimeLineData();
            });

            timeline.on('select', function (properties) {
                var selectedId = properties.items.pop();
                lister.openRowEditor(selectedId);
            });

            lister._reloadTimeLineData();

            this.safeOn('reloadRows', function(){
                lister.timelineData.clear();
                lister._reloadTimeLineData();
            });

            return div;
        },

        _reloadTimeLineData: function() {
            var t = this;

            var tlWindow = this.timeline.getWindow();

            var dateField = this.tlDateField;

            if(!t.requestFloodAvoider) {
                t.requestFloodAvoider = true;
                t.store.query({
                    _includeDescriptions: 1,
                    timelineStart: stamp.toISOString(tlWindow.start),
                    timelineEnd: stamp.toISOString(tlWindow.end),
                    timelineDateField: dateField
                }).then(function(data){

                        var transformedData = [];

                        var transformDataFunction = function(row) {
                            return {
                                id: row._recordid,
                                content: row._record_description || row._recordid,
                                start: row[dateField],
                                rowData: row
                            };
                        };

                        for(var k in data)
                            if(data[k]._recordid) {
                                transformedData.push(transformDataFunction(data[k]));
                            }

                        t.timelineData.update(transformedData);
                        if(t.tlColorBy)
                            t.timelineColorBy(t.tlColorBy);

                        t.requestFloodAvoider = false;
                    },
                    function(err){
                        t.requestFloodAvoider = false;
                    });
            }
        },

        rainbow: function(numOfSteps, step) {
            var r, g, b;
            var h = step / numOfSteps;
            var i = ~~(h * 6);
            var f = h * 6 - i;
            var q = 1 - f;
            switch(i % 6){
                case 0: r = 1; g = f; b = 0; break;
                case 1: r = q; g = 1; b = 0; break;
                case 2: r = 0; g = 1; b = f; break;
                case 3: r = 0; g = q; b = 1; break;
                case 4: r = f; g = 0; b = 1; break;
                case 5: r = 1; g = 0; b = q; break;
            }
            var c = "#" + ("00" + (~ ~(r * 255)).toString(16)).slice(-2) + ("00" + (~ ~(g * 255)).toString(16)).slice(-2) + ("00" + (~ ~(b * 255)).toString(16)).slice(-2);
            return (c);
        }

    });
});
