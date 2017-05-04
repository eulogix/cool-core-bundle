define("cool/controls/repofile",
	[
        "dojo/dom",
        "dojo/query",
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",

    'dojox/form/Uploader',
    'dojox/form/uploader/plugins/Flash'
], function(dom, query, declare, lang, array, _control, Uploader, UploaderFlash) {
 
    return declare("cool.controls.repofile", [_control], {
		
    	type: 'file',

        value: {},

        waiting: false,

        detailsNode: {},

        coolInit : function() {
            this.inherited(arguments);

            var t = this;

            var field = new dojox.form.Uploader({
                label: this.definition.parameters.buttonLabel,
                multiple: false,
                uploadOnSelect: true,
                url: Routing.generate('_coolUploader')
            }, "uploader"); // disregard reference argument

            this.fieldNode.appendChild(field.domNode);

            field.startup();

            this.detailsNode = dojo.doc.createElement('div');
            this.fieldNode.appendChild(this.detailsNode);

            field.on("change", function(fileArray){
                t.field.disabled = true;
                t.field.label = "Wait";
                console.log(fileArray);
                t.waiting = fileArray.length > 0;
                for(var i=0;i<fileArray.length;i++) {
                    t.value[fileArray[i].name] = {
                        tempId: false,
                        size:fileArray[i].size
                    }
                }
                t._refreshView();
            });

            field.on("complete", function(dataArray){
                t.field.disabled = false;
                t.field.label = t.definition.parameters.buttonLabel;
                t.waiting = false;

                for(var fileName in dataArray) {
                    t.value[fileName].tempId = dataArray[fileName];
                }

                t._refreshView();
            });

           	this.own(field);
            this.field = field;

            if(this.isReadOnly()) {
                field.set('readOnly', true);
                this.disable();
            }

            this._refreshView();

            if(this.definition.value !== undefined) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
		},

        isChanged: function() {
            return false;
        },

        isWaiting : function() {
            return this.waiting;
        },

        doUpload : function() {
            this.field.upload();
        },

        _refreshView: function() {
            this.detailsNode.innerHTML = '';
            for(var fileName in this.value) {
                this._addDetailRow( this.detailsNode,  fileName );
            }
        },

        _addDetailRow: function(node, fileName) {
            var t = this;
            var rowNode = dojo.doc.createElement('div');
            var fileElement = this.value[fileName];
            rowNode.innerHTML = fileName+' ('+this._hFileSize(fileElement.size)+') ';
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
            delete this.value[fileName];
            this._refreshView();
        },

		_setValueAttr: function(value) {
            if(value && typeof value == 'string') {
                this.value = JSON.parse(value);
            } else this.value = {};
            this._refreshView();
		},

		_getValueAttr: function() {
			if(this.value) {
                return JSON.stringify(this.value);
            }
            return null;
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