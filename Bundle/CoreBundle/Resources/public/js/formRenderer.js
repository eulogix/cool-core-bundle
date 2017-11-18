define([
            "dojo/_base/lang",
            "dojo/_base/array", // array.filter array.forEach array.map
            "dojo/_base/declare",
            "dojo/parser",
            "dojo/Deferred",
            "dojo/when",
            "dojox/html/_base",
            "dojox/lang/functional/object",
            "dojox/layout/ContentPane"
        ], function(lang, array, declare, parser, Deferred, when, htmlUtil, df, ContentPane){
  
    return declare("cool.formRenderer", [], {

        fieldNames: null, //keep track of duplicates

        constructor: function( formInstance ) {
            this.formInstance = formInstance;
            this.fieldNames = [];
        },

        render: function(div) {
            var def = new Deferred();

            var self = this;
            var form = this.formInstance;
            var layout = form.definition.layout;

            //fields container
            div.className = "formContainer";

            var fieldSets = this._parseLayout(layout);

            //wait until all deferreds have been resolved (all control widgets have been instantiated
            this.buildFields(fieldSets).then(function(){

                var template = self.getTemplate(layout, fieldSets);
                htmlUtil.set(div, template, {
                    executeScripts: true,
                    scriptHasHooks: false,
                    renderStyles: true
                });

                self._attachElements(fieldSets, div);

                parser.parse(div).then(function(instances){
                    def.resolve(instances);
                });

            });

            return def;
        },

        /**
         * [getTemplate description]
         */
        getTemplate: function(layout, fieldSets) {

            var renderer = this;

            fieldSets.forEach(function(fieldSet, index, array){
                layout = layout.replace(fieldSet.replaceToken, renderer.getFieldSetTemplate(fieldSet));
            });

            return layout;
        },

        getFieldSetTemplate: function(fieldSet) {
            var parsedFieldSet = fieldSet.parsedFieldSet;
            var props = fieldSet.properties;

            var rows   = parsedFieldSet.rows;
            var maxCol = parsedFieldSet.maxCol;

            var t = '';

            if(props.title) {
                t+='<div class="sectionTitle">'+props.title+'</div>';
            }

            //build container (table)
            t+= '<table class="fieldsContainer">';

            rows.forEach(function(row, index, array){
                var spans = 0;
                var colCounter = 1;

                t+="<tr>";
                row.forEach(function(cell){

                    var cellContent = '<table class="innerFieldsContainer"><tr>';
                    var colspan = '';
                    var alignment = '';

                    var cellStyle = [];

                    //colspan
                    switch(cell.properties.colspan) {
                        case undefined:
                        case '':
                        case null: spans++; break;
                        case '!' : colspan = ' colspan='+(maxCol-spans); spans=maxCol; break;
                        default  : spans+=parseInt(cell.properties.colspan); colspan = ' colspan='+parseInt(cell.properties.colspan); break;
                    }

                    //cell alignment
                    if(cell.properties.att_align) {
                        cellStyle.push('text-align: '+cell.properties.att_align);
                    }

                    var cssCellStyle = cellStyle.join(',');

                    cell.controls.forEach(function(element){
                        if(element.rawContent)
                            cellContent += '<td valign="bottom" class="innerFieldCell" style="'+cssCellStyle+'">'+element.rawContent+'</td>';
                        //check if element is a field or a label, fixed element, title etc... TODO
                        else if(element._control)
                             cellContent += '<td valign="bottom" class="innerFieldCell" style="'+cssCellStyle+'">'+element._control.getTemplate()+'</td>';
                        else if(element.name)
                            cellContent += '<td valign="bottom" class="innerFieldCell" style="color: red">[DUPLICATE: ' + element.name + ']</td>';
                    });

                    cellContent += '</tr></table>';

                    var colStyle = '';

                    if(props['c'+colCounter+'w'])
                        colStyle += "width:"+props['c'+colCounter+'w']+';';

                    t+="<td class=\"fieldCell\""+colspan+alignment+" valign=\"bottom\" style=\""+colStyle+"\">"+cellContent+"</td>";

                    colCounter++;
                });
                for(var i=spans; i<maxCol; i++) {
                    t+="<td></td>";
                }
                t+="</tr>";
            });
            t+="</table>";

            return t;
        },

        /**
         * builds the controls and the other static elements
         */
        buildFields: function(fieldSets) {
            var form = this.formInstance;
            var self = this;

            //some dijits may have to be retrieved over the wire, so we keep track of the pending deferreds here
            var pendingDeferreds = 0;

            //hidden fields have to be built regardless of their presence in the layout
            for(var fieldName in form.definition.fields) {
                if(form.definition.fields[ fieldName ].type=="hidden") {
                    pendingDeferreds++;
                    form.buildFormElement(fieldName).then(function(control){
                        pendingDeferreds--;
                    });
                }
            }

            //we build only the remaining fields specified in the parsed layout, which usually are a subset of the definition fields
            fieldSets.forEach(function(fl){   //cycle thru the fieldsets
                fl.parsedFieldSet.rows.forEach(function(row, index, array){
                    row.forEach(function(element){
                        element.controls.forEach(function(controlElement) {
                            if(controlElement.name && self.fieldNames.indexOf(controlElement.name) == -1) {
                                self.fieldNames.push(controlElement.name);

                                //we store the reference to the newly created widget in the fieldsets
                                pendingDeferreds++;
                                form.buildFormElement(controlElement.name, controlElement.properties).then(function(control){
                                    controlElement._control = control;
                                    pendingDeferreds--;
                                });
                            }
                        });
                    });
                });
            });

            var interval = 0;
            var def = new Deferred();
            var checkFunction = function() {
                if(pendingDeferreds == 0) {
                    clearInterval(interval);
                    def.resolve();
                }
            };

            interval = setInterval(function() {
                checkFunction();
            }, 50);

            checkFunction();

            return def;
        },

        canAddField: function(fieldName) {
            return this.formInstance.definition.fields[fieldName] != undefined;
        },

        /**
         * attaches fields and elements to the div
         */
        _attachElements: function(fieldSets, div) {

            var widget = this.formInstance;
            fieldSets.forEach(function(fl){   //cycle thru the fieldsets
                fl.parsedFieldSet.rows.forEach(function(row, index, array){
                    row.forEach(function(element){
                        element.controls.forEach(function(controlElement) {
                            if(controlElement._control) {

                                var q = "[control_container=\""+controlElement._control.getPlaceHolderName()+"\"]";
                                dojo.query(q, div).forEach(function(node) {
                                    node.appendChild( controlElement._control.domNode );
                                });

                                var ql = "[label_container=\""+controlElement._control.getPlaceHolderName()+"\"]";
                                dojo.query(ql, div).forEach(function(node) {
                                    controlElement._control.setLabelContainer(node);
                                });

                            }
                        });
                    });
                });
            });
        },

        /* private */


        _parseAttString: function(attString) {
            var tempAttr = attString.split('|');
            var prop = {};
            for(var i=0; i<tempAttr.length; i++) {
                tempAttr[i].replace(/^([^=]+)(|=(.+))$/im, function(a1, b1, c1, d1) {
                    prop['att_'+b1] = d1!=undefined ? d1 : true;
                });
            }
            return prop;
        },

        /**
         * parses a field template and extracts its properties
         *
         * @param  {[type]} fieldT [description]
         * @return {[type]}        [description]
         */
        _parseFieldProperties: function(fieldT) {
            var prop = {};
            var renderer = this;
            fieldT.replace(/^(<raw>.*?<\/raw>|[^<:|>@!]+)(:(?=)([0-9%empx]*)){0,1}(:(?=)([0-9%empx]*)){0,1}(\|[^\n\r;:@,]*)*(|@(|!|[0-9]+))$/im, function(a, b, c, d, e, f, g, h, span) {

                if(b.match(/<raw>.*?<\/raw>/im) || b.match(/^[ \t]*$/im)) //consider empty fieldsets as empty (works spanning content doing ,,,field)
                     prop['rawContent'] = b;
                else prop['name'] = b.replace(/[ ]/img,'');

                //w & h : 100px:200%
                if(d) { prop['width'] = d; }
                if(f) { prop['height'] = f; }

                //other modifiers and attributes |flag|attrib=value|...
                if(g) {
                    prop = lang.mixin(prop, renderer._parseAttString(g))
                }

                //colspan @ for 1, @1..n for n, @! for "to max"
                if(h) {
                    prop['colspan'] = span ? span : 1;
                }
            });
            return prop;
        },

        /**
         * decomposes a fieldSet in an array of rows
         *
         * @param  {[type]} fieldSet [description]
         * @return {[type]}          [description]
         */
        _parseFieldSet: function(fieldSet) {
            var renderer = this;
            var rows = [];
            var fieldProps = {};
            var maxCol = 0;

            var tempRows = fieldSet.split("\n");
            for(var i=0; i<tempRows.length; i++) {
                var rowLength = 0;
                var rowFields = tempRows[i].replace(/[\t\r\n]/img,'').split(',');

                var row = [];
                for(var f=0; f<rowFields.length; f++) {
                    // trim the field
                    rowFields[f] = rowFields[f].replace(/^[\t ]*(.+?)(?=[ \t]*$)/im,function(whole, a){ return a; });

                    var fieldsAdded = false;
                    var colspan = 0; //extract this from rowFields[f] before passing it to the function below
                    var cellControls = [];
                    var cellProps = false;
                    var fieldControls = rowFields[f].split(';;');

                    //the same slot may contain several controls, split the content by double semicolon to find out
                    //first we grab properties for the current set
                    rowFields[f].replace(/\((.+?)\)(\|[^\n\r;:@,]*)*(|@(|!|[0-9]+))$/im, function(whole, rg_controls, rg_props, rg_span, rg_spancount) {
                        cellProps = renderer._parseAttString(rg_props);
                        if(rg_span) {
                            cellProps['colspan'] = rg_spancount ? rg_spancount : 1;
                        }
                        fieldControls = rg_controls.split(';;');
                    });

                    for(var fc=0; fc<fieldControls.length; fc++) {
                        var props = this._parseFieldProperties(fieldControls[fc]);
                        if(props.rawContent) {
                            cellControls.push({rawContent: props.rawContent, properties: props});
                            if(!cellProps)
                                cellProps = props;
                            fieldsAdded = true;
                        } else if(this.canAddField(props.name)) {
                            cellControls.push({name: props.name, properties: props});
                            if(!cellProps)
                                cellProps = props;
                            fieldsAdded = true;
                        } else if(props.name) {
                            cellControls.push({rawContent: "<span style=\"color:red\">[NON EXISTENT: " + props.name + "]</span>", properties: props});
                            if(!cellProps)
                                cellProps = props;
                            fieldsAdded = true;
                        }
                    }

                    if(fieldsAdded) {
                        rowLength++;
                        row.push({
                            properties: cellProps, //cell props
                            controls: cellControls
                        });
                        if(colspan) {
                            rowLength+=parseInt(props.colspan)-1;
                        }
                    }

                }
                rows.push(row);
                maxCol = rowLength > maxCol ? rowLength : maxCol;
            }
            var ret = {maxCol: maxCol, rows: rows};
            return ret;
        },

        /**
         * extract fieldsets (<FIELDS>...</FIELDS>) in objects
         * fieldset parameters:
         *
         * title : fieldset title
         * c1w, c2w, c[n]w : column css width
         *
         * @param  {string} layout The form layout to parse
         * @return {array}        an array of objects, representing the fieldSet
         */
        _parseLayout: function(layout) {
            var rd = this;
            var fieldSets = [];
            layout.replace(/<FIELDS([^>]*)>[ \t\r\n]*([\s\S]+?)[ \t\r\n]*<\/FIELDS>/img, function(whole, fsProperties, fs){
                fieldSets.push({
                    replaceToken: whole,
                    rawFieldSet: fs,
                    properties: rd._parseProperties(fsProperties),
                    parsedFieldSet: rd._parseFieldSet(fs)
                });
            });
            return fieldSets;
        },

        /**
         * parses a generic string in the format
         *
         * prop1="val1" prop2=val2 prop3=val3
         *
         * and returns an object
         * @param propsString
         * @private
         */
        _parseProperties: function(propsString) {

            var ret = {};
            propsString.replace(/([a-z0-9]+) *= *("(.+?)"|'(.+?)'|(.+?))(?=( |$))/img, function(blank, propName, rawPropValue, pvDblQuot, pvSingleQuot, pvNoQuot) {
                ret[propName] = pvDblQuot || pvSingleQuot || pvNoQuot;
            });

            return ret;
        }
    });

});
