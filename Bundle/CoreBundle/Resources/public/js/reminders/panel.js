define("cool/reminders/panel",
    [
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/_base/array",
        "dojo/_base/event",
        "dojo/Deferred",
        "dojo/Evented",
        "dijit/_WidgetBase",
        "dijit/_TemplatedMixin",
        "dijit/_WidgetsInTemplateMixin",
        "dijit/layout/BorderContainer", "dijit/layout/ContentPane",

        'gridx/Grid',

        "dojo/dom",
        "dojo/dom-class",
        "dojo/dom-style",
        "dojo/dom-construct",
        "dojo/dom-geometry",

        "dojo/on",
        "dojo/date",
        "dojo/date/locale",
        "dojo/date/stamp",
        "dojo/store/Memory",
        "dojo/request/xhr",

        "dojo/_base/fx",
        "dojo/fx",

        'gridx/allModules',
        'gridx/modules/select/Cell',
        "gridx/modules/CellWidget",
        'cool/cool',
        'cool/translator',
        'cool/dialog/manager',
        "cool/dijit/iconButton",

        "dojo/text!./templates/panel.html"
    ], function(declare, lang, array, event, deferred, Evented,
                _WidgetBase,
                _TemplatedMixin,
                _WidgetsInTemplateMixin,

                BorderContainer, ContentPane,
                Grid,

                dom,
                domClass,
                domStyle,
                domConstruct,
                domGeometry,

                on, date, locale, stamp, Memory, xhr,
                coreFx, fx,
                mods,
                selectCell, CellWidget,
                cool, ctr, dialogManager, iconButton,

                template){

        return declare("cool/reminders/panel", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin, Evented], {

            initialStartDate: null,

            startDate: null,

            showDays: 7,

            showDebug: false,

            debugMode: false,

            templateString: template,

            widgetsInTemplate: true,

            grids : [],

            simpleGrids : [],

            detailsWidget: {},

            fadeDurationMs: 200,

            currentView : null,

            translator : null,

            categories: {},

            label_1week: '',
            label_2weeks: '',
            label_1month: '',
            label_recentre: '',
            label_today_mode: '',
            label_tm_start: '',
            label_tm_30PC: '',
            label_tm_centered: '',

            constructor: function() {
                if(!(this.startDate instanceof Date)) {
                    this.startDate = this.startDate ? new Date(this.startDate) : new Date();
                }
                this.initialStartDate = this.startDate;

                this.grid = {};

                this.label_1week = this.getTranslator().trans("button_1week");
                this.label_2weeks = this.getTranslator().trans("button_2weeks");
                this.label_1month = this.getTranslator().trans("button_1month");
                this.label_recentre = this.getTranslator().trans("button_recentre");
                this.label_today_mode = this.getTranslator().trans("label_today_mode");
                this.label_tm_start = this.getTranslator().trans("TM_START");
                this.label_tm_30PC = this.getTranslator().trans("TM_30PC");
                this.label_tm_centered = this.getTranslator().trans("TM_CENTER");
            },

            getOffsetStartDate: function() {
                var daysToSubtract = 0;
                switch(this.tmSelector.get('value')) {
                    case '30PC'     : daysToSubtract = Math.floor(this.showDays/3)-1; break;
                    case 'CENTER'   : daysToSubtract = Math.floor(this.showDays/2)-1; break;
                    default:
                    case 'START'    : daysToSubtract = 0; break;
                }
                return date.add(this.startDate, "day", -1*daysToSubtract);
            },

            postCreate: function() {
                this.inherited(arguments);
                this.configureButtons();
                var t = this;

                t.initialize().then(function(){
                    t.reloadData();
                });

            },

            initialize: function() {
                var d = new deferred();

                var t = this;

                if(this.showDebug) {
                    var debugButton = new iconButton({
                        label: this.getTranslator().trans('debug'),
                        onClick: function() {
                            t.toggleDebugMode();
                        },
                        style: "float:right;",
                        iconSrc: "/bower_components/fugue/icons/bug.png",
                        showLabel: false,
                        tooltip: this.getTranslator().trans('debug_tip')
                    });

                    this.toolbar.addChild(debugButton);
                }

                cool.callCommand('getReminderCategories', function(data) {
                    t.categories = data;
                    d.resolve();
                });

                return d;
            },


            reloadData: function() {
                this.refreshDatedGrid();
                this.refreshSimpleGrid();
            },

            configureButtons: function(){

                var t = this;

                if(this.previousButton){
                    this.own(
                        on(this.previousButton, "click", lang.hitch(this, this.previousRange))
                    );
                }

                if(this.todayButton){
                    this.own(
                        on(this.todayButton, "click", lang.hitch(this, this.todayRange))
                    );
                }

                if(this.nextButton){
                    this.own(
                        on(this.nextButton, "click", lang.hitch(this, this.nextRange))
                    );
                }

                if(this.tmSelector){
                    this.own(
                        on(this.tmSelector, "change", function(){
                            t.refreshDatedGrid();
                        })
                    );
                }

                if(this.oneMonthButton){
                    on(this.oneMonthButton, "click", function(){ t.changeDetail(30) });
                }

                if(this.oneWeekButton){
                    on(this.oneWeekButton, "click", function(){ t.changeDetail(7) });
                }

                if(this.twoWeeksButton){
                    on(this.twoWeeksButton, "click", function(){ t.changeDetail(14) });
                }
            },

            changeDetail: function(days) {
                this.showDays = days;
                var t = this;
                t.refreshDatedGrid();
            },

            nextRange: function(){
                var t = this;

                this.startDate = date.add(this.startDate, "day", this.showDays);

                this._animateRange(this.grid.domNode, true, false, 0, -100,
                     function(){
                        t.refreshDatedGrid().then(function(grid){

                            domStyle.set(grid.domNode, "opacity", "0");
                            t._animateRange(grid.domNode, true, true, 100, 0,function(){});

                        });
                    });
            },

            todayRange: function(){
                var t = this;

                this.startDate = this.initialStartDate;

                this._animateRange(this.grid.domNode, true, false, 0, -100,
                    function(){
                        t.refreshDatedGrid().then(function(grid){

                            domStyle.set(grid.domNode, "opacity", "0");
                            t._animateRange(grid.domNode, true, true, 100, 0,function(){});

                        });
                    });
            },

            previousRange: function(){
                var t = this;

                this.startDate = date.add(this.startDate, "day", -1*this.showDays);

                this._animateRange(this.grid.domNode, false, false, 0, 100,
                     function(){
                        t.refreshDatedGrid().then(function(grid){

                            domStyle.set(grid.domNode, "opacity", "0");
                            t._animateRange(grid.domNode, true, true, -100, 0,function(){});

                        });
                    });
            },

            refreshDatedGrid: function() {
                var d = new deferred();

                var t = this;
                var refDate = this.getOffsetStartDate();

                var url = Routing.generate('getDatedMatrix',{dateStart:refDate.toISOString(), days: this.showDays});

                xhr(url, {
                    handleAs: "json",
                    method: "POST",
                    data: this.getQueryParameters()
                }).then(function(matrix){

                    t.destroyDatedGrids();

                    var categories = t.getCategoriesFor(Object.keys(matrix.counts));

                    var layout = t.buildLayout(matrix);

                    array.forEach(categories, function(category){

                        var store = t.buildStore(matrix, category);

                        var grid = new Grid({
                            store: store,
                            structure: layout,
                            autoHeight: true,
                            modules: [
                                // mods.VirtualVScroller,
                                selectCell,
                                CellWidget
                            ]
                        });

                        t.own(grid);

                        t.grids.push(grid);

                        var titleDiv = dojo.doc.createElement('div');
                        domClass.add(titleDiv, 'sectionTitle');
                        titleDiv.innerHTML = t.categories.all[category].label;
                        t.viewContainer.domNode.appendChild(titleDiv);

                        t.viewContainer.domNode.appendChild(grid.domNode);

                        dojo.connect(grid, "onCellClick", function(evt){
                        });

                        //ensures that only one cell is selected
                        dojo.connect(grid.select.cell, "onSelected", function(cell){
                            var selected = grid.select.cell.getSelected();
                            for(var i=0;i<selected.length;i++) {
                                var row = selected[i][0];
                                var col = selected[i][1];
                                if((row!=cell.row.id) || (col!=cell.column.id))
                                    grid.select.cell.deselectById(row, col);
                            }
                            //open details for cell
                            if(col != 'key')
                                t.openDetail(grid, row, col);
                        });

                        //if there are errors, the line is red
                        dojo.connect(grid.body, 'onAfterRow', function(row){

                            var rowData = row.rawData();
                            var node = row.node();


                            if( t.debugMode && rowData.errors.length > 0) {
                                domClass.remove(node, "gridxRowOdd");
                                domClass.toggle(node, "gridxRowError", true);
                            }

                        });

                        grid.startup();

                    });

                    d.resolve();

                }, function(err){
                    dialogManager.showXhrError(err);
                }, function(evt){
                });

                return d;
            },

            refreshSimpleGrid: function() {
                var d = new deferred();

                var self = this;
                var url = Routing.generate('getSimpleMatrix',{});

                xhr(url, {
                    handleAs: "json",
                    method: "POST",
                    data: this.getQueryParameters()
                }).then(function(matrix){

                    self.destroySimpleGrids();

                    var categories = self.getCategoriesFor(Object.keys(matrix.counts));

                    var layout = [
                        {id: 'key', field: 'key', name: self.getTranslator().trans("Key"), width: '80%',

                            widgetsInCell: true,

                            setCellValue: function(gridData, storeData, cellWidget){

                                var rawData = cellWidget.cell.row.rawData();

                                dialogManager.trackMouseOver(cellWidget.domNode);
                                dialogManager.unbindTooltip(cellWidget.domNode);

                                var label = '';
                                var tooltip = '';

                                if(self.debugMode) {
                                    if (rawData.processingDuration > 50)
                                        label += "<img src='/bower_components/fugue/icons/hourglass--exclamation.png' style='vertical-align:middle'/>&nbsp;";

                                    tooltip += '<br><b>Execution time (ms):</b>' + rawData.processingDuration + '<br><br>';

                                    if (rawData.errors.length > 0) {
                                        label += "<img src='/bower_components/fugue/icons/exclamation-red.png' style='vertical-align:middle'/>&nbsp;";
                                        tooltip += '<br><b>Errors:</b><br><br>' + rawData.errors.join('<br>');
                                    }
                                }

                                label += self.getTranslator().trans(storeData);

                                cellWidget.domNode.innerHTML = label;
                                if(tooltip != '')
                                    dialogManager.bindTooltip(cellWidget.domNode, tooltip, '700');
                            }

                        },
                        {id: 'count', field: 'count', name: self.getTranslator().trans("Count"), width: '20%', decorator:self._cellDecorator}
                    ];

                    array.forEach(categories, function(category){
                        var store = self.buildSimpleStore(matrix, category);

                        var grid = new Grid({
                            store: store,
                            structure: layout,
                            autoHeight: true,
                            modules: [
                                // mods.VirtualVScroller,
                                selectCell,
                                CellWidget
                            ]
                        });

                        self.own(grid);

                        self.simpleGrids.push(grid);

                        var titleDiv = dojo.doc.createElement('div');
                        domClass.add(titleDiv, 'sectionTitle');
                        titleDiv.innerHTML = self.categories.all[category].label;
                        self.simpleGridContainer.domNode.appendChild(titleDiv);

                        self.simpleGridContainer.domNode.appendChild(grid.domNode);

                        //if there are errors, the line is red
                        dojo.connect(grid.body, 'onAfterRow', function(row){

                            var rowData = row.rawData();
                            var node = row.node();

                            if(self.debugMode && rowData.errors.length > 0) {
                                domClass.remove(node, "gridxRowOdd");
                                domClass.toggle(node, "gridxRowError", true);
                            }

                        });

                        dojo.connect(grid, "onCellClick", function(evt){
                        });

                        //ensures that only one cell is selected
                        dojo.connect(grid.select.cell, "onSelected", function(cell){
                            var selected = grid.select.cell.getSelected();
                            for(var i=0;i<selected.length;i++) {
                                var row = selected[i][0];
                                var col = selected[i][1];
                                if((row!=cell.row.id) || (col!=cell.column.id))
                                    grid.select.cell.deselectById(row, col);
                            }
                            self.openSimpleDetail(grid, row);
                        });

                        grid.startup();

                    });

                    d.resolve();

                }, function(err){
                    dialogManager.showXhrError(err);
                }, function(evt){
                });

                return d;
            },

            getCategoriesFor: function(keys) {
                var self = this;
                var usedCategories = {};
                array.forEach(keys, function(key){
                    usedCategories[ self.categories.bykey[key] ] = 1;
                });
                return Object.keys(usedCategories);
            },

            destroySimpleGrids: function() {
                array.forEach(this.simpleGrids, function(gridObj){
                    if(lang.isFunction(gridObj.destroyRecursive)) {
                        gridObj.destroyRecursive();
                    }
                });
                this.simpleGridContainer.domNode.innerHTML = '';
            },

            destroyDatedGrids: function() {
                array.forEach(this.datedGrids, function(gridObj){
                    if(lang.isFunction(gridObj.destroyRecursive)) {
                        gridObj.destroyRecursive();
                    }
                });
                this.viewContainer.domNode.innerHTML = '';
            },

            refreshSimpleGrids: function() {
                array.forEach(this.simpleGrids, function(gridObj){
                    gridObj.body.refresh();
                });
            },

            refreshDatedGrids: function() {
                array.forEach(this.grids, function(gridObj){
                    gridObj.body.refresh();
                });
            },

            openDetail: function(gridObj, key, day) {
                var t = this;
                var record = gridObj.row(key).rawData();
                var column = gridObj.column(day);

                cool.widgetFactory(record.detailsLister, lang.mixin({
                    translationDomain: record.detailsTranslationDomain,
                    provider: key,
                    isoDate:column.isoDate,
                    comparison:column.comparison
                }, this.getQueryParameters() ), function(newLister) {


                    newLister.remindersPanel = t;
                    newLister.onlyContent = true;
                    newLister.fillContent = true;
                    newLister.maxHeight = 0;

                    if(lang.isFunction(t.detailsWidget.destroyRecursive)) {
                        t.detailsWidget.destroyRecursive();
                    }
                    t.detailsContainer.set('content', '');
                    t.detailsContainer.addChild(newLister);
                });
            },

            openSimpleDetail: function(gridObj, key) {
                var t = this;
                var record = gridObj.row(key).rawData();
                cool.widgetFactory(record.detailsLister, lang.mixin({
                    translationDomain: record.detailsTranslationDomain,
                    provider: key
                }, this.getQueryParameters() ), function(newLister) {

                    newLister.remindersPanel = t;
                    newLister.onlyContent = true;
                    newLister.fillContent = true;
                    newLister.maxHeight = 0;

                    if(lang.isFunction(t.detailsWidget.destroyRecursive)) {
                        t.detailsWidget.destroyRecursive();
                    }
                    t.detailsContainer.set('content', '');
                    t.detailsContainer.addChild(newLister);
                });
            },

            /**
             * returns an object that gets propagated to the listers
             */
            getQueryParameters: function() {
                return {};
            },

            _cellDecorator: function(value) {
                return value > 0 ? '<b>'+value+'</b>' : '-';
            },

            /**
             * builds a gridx layout based on the matrix retrieved by the server
             * @param matrix
             */
            buildLayout: function(matrix) {

                var self = this;

                var layout = [
                    {id: 'key', field: 'key', name: this.getTranslator().trans("Key"), width: '200px',

                        widgetsInCell: true,

                        setCellValue: function(gridData, storeData, cellWidget){

                            var rawData = cellWidget.cell.row.rawData();

                            dialogManager.trackMouseOver(cellWidget.domNode);
                            dialogManager.unbindTooltip(cellWidget.domNode);

                            var label = '';
                            var tooltip = '';

                            if(self.debugMode) {
                                if(rawData.processingDuration > 100)
                                    label += "<img src='/bower_components/fugue/icons/hourglass--exclamation.png' style='vertical-align:middle'/>&nbsp;";

                                tooltip += '<br><b>Execution time (ms):</b>'+rawData.processingDuration+'<br><br>';

                                if(rawData.errors.length > 0) {
                                    label += "<img src='/bower_components/fugue/icons/exclamation-red.png' style='vertical-align:middle'/>&nbsp;";
                                    tooltip += '<br><b>Errors:</b><br><br>'+rawData.errors.join('<br>');
                                }
                            }

                            label += self.getTranslator().trans(storeData);

                            cellWidget.domNode.innerHTML = label;
                            if(tooltip != '')
                                dialogManager.bindTooltip(cellWidget.domNode, tooltip, '700');
                        }
                    },

                    {id: 'before', field: 'before', name: this.getTranslator().trans("Past"), width: '80px', isoDate: this.startDate.toISOString(), comparison: 'smaller', style : "background-color:rgba(255,255,0,0.1);", decorator:self._cellDecorator}
                ];

                var formatter = cool.getFormatters().unixTimestampFormatter;
                if(this.showDays > 10)
                    formatter = cool.getFormatters().unixTimestampFormatterDayAndMonth;

                for(var i=0; i<matrix.days.length; i++) {
                    var date = new Date(matrix.days[i].timestamp*1000);
                    var cl = {id: 'd'+i, field: 'd'+i, name: formatter( matrix.days[i].timestamp ), isoDate: date.toISOString(), comparison: 'equal', decorator:self._cellDecorator};
                    if(matrix.days[i].holiday) {
                        cl.style = "background-color:rgba(255,0,0,0.1);";
                    }
                    if(matrix.days[i].weekend) {
                        cl.style = "background-color:rgba(0,255,0,0.1);";
                    }
                    if(matrix.days[i].today) {
                        cl.style = "background-color:rgba(0,0,255,0.1);";
                    }
                    layout.push(cl);
                }

                layout.push({id: 'after', field: 'after', name: this.getTranslator().trans("Future"), width: '80px', isoDate: date.toISOString(), comparison: 'greater', style : "background-color:rgba(255,255,0,0.1);", decorator:self._cellDecorator});

                return layout;
            },

            buildStore: function(matrix, category) {

                var rows = [];
                var cs = null;

                for(var countSet in matrix.counts)
                    if(matrix.counts.hasOwnProperty(countSet) &&
                        this.categories.bykey[countSet] == category) {

                        cs = matrix.counts[countSet];

                        var row = {
                            id: countSet,
                            key: countSet,
                            before: cs.before,
                            after: cs.after,

                            detailsLister : cs.detailsLister,
                            detailsTranslationDomain : cs.detailsTranslationDomain,

                            errors: cs.errors,
                            processingDuration: cs.processingDuration,
                            processingMemoryUsage: cs.processingMemoryUsage
                        };

                        for(var i=0; i<cs.days.length; i++) {
                            row['d'+i] =  cs.days[i];
                        }

                        rows.push(row);
                    }

                return new Memory({data:rows});
            },

            buildSimpleStore: function(matrix, category) {

                var rows = [];
                var cs = null;

                for(var countSet in matrix.counts) {
                    if(matrix.counts.hasOwnProperty(countSet) &&
                       this.categories.bykey[countSet] == category) {
                        cs = matrix.counts[countSet];
                        var row = {
                            id: countSet,
                            key: countSet,
                            count: cs.count,
                            detailsLister: cs.detailsLister,
                            detailsTranslationDomain: cs.detailsTranslationDomain,

                            errors: cs.errors,
                            processingDuration: cs.processingDuration,
                            processingMemoryUsage: cs.processingMemoryUsage
                        };

                        rows.push(row);
                    }
                }

                return new Memory({data:rows});
            },

            resize: function() {
                this.inherited(arguments);
                if(lang.isFunction( this.grid.resize )) {
                    this.grid.resize();
                }
            },

            _animateRange : function(node, toLeft, fadeIn, xFrom, xTo, onEnd){
                // summary:
                //		Animates the current view using a synchronous fade and horizontal translation.
                // toLeft: Boolean
                //		Whether the view is moved to the left or to the right.
                // fadeIn: Boolean
                //		Whether the view is faded in or out.
                // xFrom: Integer
                //		Position before the animation
                // xTo: Integer
                //		Position after the animation
                // onEnd: Function
                //		Function called when the animation is finished.
                // tags:
                //		protected


                if(this.animateRangeTimer){ // cleanup previous call not finished
                    clearTimeout(this.animateRangeTimer);
                    delete this.animateRangeTimer;
                }

                var fadeFunc = fadeIn ? coreFx.fadeIn : coreFx.fadeOut;
                domStyle.set(node, {left: xFrom + "px", right: (-xFrom) + "px"});

                fx.combine([
                    coreFx.animateProperty({
                        node: node,
                        properties: {left: xTo, right: -xTo},
                        duration: this.fadeDurationMs / 2,
                        onEnd: onEnd
                    }),
                    fadeFunc({node: node, duration: this.fadeDurationMs / 2})
                ]).play();
            },

            getTranslator: function() {
                if(this.translator)
                    return this.translator;

                this.translator = new ctr({
                    domain: "REMINDERS_PANEL"
                });

                return this.translator;
            },

            toggleDebugMode: function() {
                var t = this;
                t.debugMode = !t.debugMode;
                t.refreshSimpleGrids();
                t.refreshDatedGrids();
            }

        });

    });