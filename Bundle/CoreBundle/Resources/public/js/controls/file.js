define("cool/controls/file",
	[
        "dojo/dom",
        "dojo/query",
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/_base/array",

        "dojo/_base/fx",
        "dojo/fx",
        "dojo/fx/Toggler",

        "cool/controls/_control",

        'dojox/form/Uploader',
        'dojox/form/uploader/plugins/Flash',
        'dojox/form/uploader/plugins/HTML5',

        'dijit/_TemplatedMixin',
        'dijit/_WidgetsInTemplateMixin',

        "dojo/text!./templates/file.html"

], function(dom, query, declare, lang, array,
            fx, coreFx, Toggler,
            _control, Uploader, UploaderFlash, UploaderHTML5, _TemplatedMixin, _WidgetsInTemplateMixin, widgetTpl) {
 
    return declare("cool.controls.file", [_control, _TemplatedMixin, _WidgetsInTemplateMixin], {

        templateString: widgetTpl,

    	type: 'file',

        viewData: {},

        uploadedFiles: {},

        operations: {},

        waiting: false,

        maxFiles: -1,

        coolInit : function() {
            this.inherited(arguments);

            this.containerTable.style.cssText = this.cssStyles.join(';');

            this.progressBarToggler = new Toggler({
                node: this.progressBarNode,
                showFunc: fx.fadeIn,
                hideFunc: fx.fadeOut
            });
            this.progressBarToggler.hide();

            this.uploadedFiles = {};
            this.viewData = {};
            this.operations = {};

            if(this.definition.parameters.maxFiles) {
                this.maxFiles = this.definition.parameters.maxFiles;
            }
            var t = this;

            var field = new dojox.form.Uploader({
                multiple: this.definition.parameters.multiple,
                uploadOnSelect: true,
                url: Routing.generate('_coolUploader'),
                label: this.definition.parameters.buttonLabel || GlobalTranslator.trans('file_control_upload'+(this.isMultiple() ? '_multiple' : '')),
                showProgress: true
            },  dojo.doc.createElement('div')); // disregard reference argument
            this.own(field);
            field.startup();

            if(!this.isReadOnly)
                this.addDropTarget(this.detailsNode);

            this.uploadButtonNode.appendChild(field.domNode);

            field.on("change", function(fileArray){
                t.field.label = GlobalTranslator.trans('file_control_wait');
                t.waiting = fileArray.length > 0;
                for(var i=0;i<fileArray.length;i++) {
                    t.uploadedFiles[fileArray[i].name] = {
                        tempId: false,
                        size:fileArray[i].size
                    }
                }
                t._refreshView();
            });

            field.on("begin", function(dataArray){
                t.progressBarToggler.show();
            });

            field.on("progress", function(dataArray){
                t.progressBarWidget.set({value: Math.ceil(dataArray.decimal*100)});
            });

            field.on("complete", function(dataArray){
                t.progressBarToggler.hide();
                t.field.label = t.definition.parameters.buttonLabel;
                t.waiting = false;

                for(var fileName in dataArray) {
                    t.uploadedFiles[fileName].tempId = dataArray[fileName];
                }
                t._refreshView();
            });


            this.field = field;

            this._refreshView();

            if(this.definition.value !== undefined) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
		},


        //needed because there's a hard limit on 20 files somewhere in dojo/chrome/whoknows
        //this splits the upload in chunks of 15 files, which is safe
        _drop: function(e){
            var self = this;
            dojo.stopEvent(e);
            var dt = e.dataTransfer;
            var chunk = [];
            array.forEach(dt.files, function(file){
                chunk.push(file);
                if(chunk.length == 15) {
                    self.field._files = chunk;
                    self.field.onChange(self.field.getFileList());
                    chunk = [];
                }
            });
            if(chunk.length > 0) {
                self.field._files = chunk;
                self.field.onChange(self.field.getFileList());
                chunk = [];
            }
        },

        /*************************
         *	   Public Methods	 *
         *************************/

        addDropTarget: function(node, /*Boolean?*/ onlyConnectDrop){
            // summary:
            //		Add a dom node which will act as the drop target area so user
            //		can drop files to this node.
            // description:
            //		If onlyConnectDrop is true, dragenter/dragover/dragleave events
            //		won't be connected to dojo.stopEvent, and they need to be
            //		canceled by user code to allow DnD files to happen.
            //		This API is only available in HTML5 plugin (only HTML5 allows
            //		DnD files).
            if(!onlyConnectDrop){
                this.connect(node, 'dragenter', dojo.stopEvent);
                this.connect(node, 'dragover', dojo.stopEvent);
                this.connect(node, 'dragleave', dojo.stopEvent);
            }
            this.connect(node, 'drop', '_drop');
        },

        isWaiting : function() {
            return this.waiting;
        },

        canAddFiles: function() {
            return !(
                    this.isWaiting() ||
                    (this.maxFiles > 0 && this.countUploadedFiles() >= this.maxFiles) ||
                    this.operations.removeStoredFile ||
                    this.isReadOnly()
                   );
        },

        doUpload : function() {
            this.field.upload();
        },

        isMultiple: function () {
            return this.definition.parameters.multiple
        },

        countUploadedFiles: function () {
            return Object.keys(this.uploadedFiles).length;
        },

        _refreshView: function() {
            var t = this;
            t.viewDataNode.innerHTML = '';

            if(this.viewData.hasOwnProperty('name') && (this.countUploadedFiles() == 0)) {

                var storedFileNode = dojo.doc.createElement('span');

                storedFileNode.innerHTML = this.viewData.name+' ('+this._hFileSize(this.viewData.size)+')';
                storedFileNode.style.cursor = "hand";

                if(!this.isReadOnly()) {
                    if(this.operations.removeStoredFile) {
                        storedFileNode.style.textDecoration = "line-through";
                        var undoBtn = dojo.doc.createElement('img');
                        undoBtn.src = "/bower_components/fugue/icons/arrow-curve-180-left.png";
                        undoBtn.onclick = function() {
                            t._unRemoveStoredFile();
                        }
                    } else {
                        var removeBtn = dojo.doc.createElement('img');
                        removeBtn.src = "/bower_components/fugue/icons/minus-circle-frame.png";
                        removeBtn.onclick = function() {
                            t._removeStoredFile();
                        }
                    }
                }

                storedFileNode.onclick = function() {
                    t._downloadFile();
                };

                this.viewDataNode.appendChild(storedFileNode);
                if(removeBtn) {
                    this.viewDataNode.appendChild(removeBtn);
                }
                if(undoBtn) {
                    this.viewDataNode.appendChild(undoBtn);
                }

            }

            this.field.set('disabled', !this.canAddFiles());

            if(!this.isReadOnly())
                this.detailsNode.innerHTML = Object.keys(this.uploadedFiles).length == 0 ?
                    this.definition.parameters.detailsPaneLabel || GlobalTranslator.trans('file_control_drag_here'+(this.isMultiple() ? '_multiple' : ''))
                    : '';
            else this.detailsNode.style.display = 'none';

            for(var fileName in this.uploadedFiles) {
                this._addDetailRow( this.detailsNode,  fileName );
            }

            this.emit("change", {});
        },

        _addDetailRow: function(node, fileName) {
            var t = this;
            var rowNode = dojo.doc.createElement('div');
            var fileElement = this.uploadedFiles[fileName];
            rowNode.innerHTML = fileName+' ('+this._hFileSize(fileElement.size)+')';

            if(fileElement.tempId) {
                var removeBtn = dojo.doc.createElement('img');
                removeBtn.src = "/bower_components/fugue/icons/minus-circle-frame.png";
                removeBtn.onclick = function() {
                    t._removeFile(fileName);
                };
                rowNode.appendChild(removeBtn);
            }
            node.appendChild(rowNode);
        },

        _removeFile: function(fileName) {
            delete this.uploadedFiles[fileName];
            this._refreshView();
        },

        _removeStoredFile: function() {
            this.operations.removeStoredFile = true;
            this._refreshView();
        },

        _unRemoveStoredFile: function() {
            delete this.operations.removeStoredFile;
            this._refreshView();
        },

        _downloadFile: function() {
            this.getContainerWidget().callAction('downloadField', function(data){
                document.location = data.downloadUrl;
            }, {fieldName: this.getName()}, {dontLock: true});
        },

		_setValueAttr: function(value) {
            if(value && typeof value == 'string') {
                try{
                    var arr = JSON.parse(value);
                    if(arr.hasOwnProperty('viewData')) {
                        this.viewData = arr.viewData;
                    }
                    if(arr.hasOwnProperty('uploadedFiles')) {
                        this.uploadedFiles = arr.uploadedFiles;
                    }
                    if(arr.hasOwnProperty('operations')) {
                        this.operations = arr.operations;
                    }
                } catch(e) {
                    this.viewData = {};
                    this.uploadedFiles = {};
                    this.operations = {};
                }
            } else {
                this.viewData = {};
                this.uploadedFiles = {};
                this.operations = {};
            }
            this._refreshView();
		},

		_getValueAttr: function() {
            return JSON.stringify({
                viewData : this.viewData,
                uploadedFiles : this.uploadedFiles,
                operations: this.operations
            });
		},

        _hFileSize: function(size) {
            var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            var i = 0;
            while(size >= 1024) {
                size /= 1024;
                ++i;
            }
            return size.toFixed(1) + ' ' + units[i];
        }


    });
 
});