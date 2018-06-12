define("cool/controls/HTMLEditorCK",
	[
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/_base/array",
    "cool/controls/_control",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/query"
], function(declare, lang, array, _control, dom, domConstruct, query) {
 
    return declare("cool.controls.HTMLEditorCK", [_control], {

        tempValue: null,

        editor: null,

    	coolInit : function() {
            var self = this;
            this.inherited(arguments);

            var ckParams = {};
            var uploadRepoId = this.getDefinitionParameter('ck_upload_repo_id');
            if(uploadRepoId)
                ckParams.filebrowserUploadUrl = Routing.generate('frepoCKUpload', {
                    repositoryId: uploadRepoId,
                    filePath: this.getDefinitionParameter('ck_upload_repo_path')
                });

            this.editor = CKEDITOR.appendTo(this.fieldNode, ckParams);

            this.editor.on('loaded', function(evt){
                var width = self.getParameter('width');
                var height = self.getParameter('height');
                if(width && height)
                    self.editor.resize(width, height);
            });

            //TODO: readonly, onchange...

            if(this.definition.value !== undefined) {
                this.set('value', this.definition.value);
                this.emit("valueInit", {});
            }
  		},

  		_setValueAttr: function(value) {
            this.editor.setData(value, function() { this.updateElement() });
  		},

  		_getValueAttr: function() {
  			return this.editor.getData();
  		},

        insertAtCursor: function(text) {
            this.editor.insertText(text);
        }

    });
 
});