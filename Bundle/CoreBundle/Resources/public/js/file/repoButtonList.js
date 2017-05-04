define("cool/file/repoButtonList",
	[
    "dojo/_base/lang",
    "dojo/_base/declare",
    "dojo/_base/array",
    "dojo/dom",
    "dojo/dom-style",
    "dijit/_WidgetBase",
    "dijit/_TemplatedMixin",
    'dijit/_WidgetsInTemplateMixin',

    "dijit/form/DropDownButton",
    "dijit/DropDownMenu",
    "dijit/MenuItem",

    "cool/file/repository",

    "dojo/text!./templates/repoButtonList.html"
], function(lang, declare, array, dom, domStyle, _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin,
            DropDownButton, DropDownMenu, MenuItem,
            fileRepository,
            template) {
 
    return declare("cool/file/repoButtonList", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin], {
        templateString: template,

        repositoryId: null,

        files: [],
        filesCount: null,

        fileRepo: null,

        postCreate : function() {
            this.inherited(arguments);
            this.fileRepo = new fileRepository({
                repositoryId: this.repositoryId
            });
        },

        _setValueAttr: function(value) {
            this.files = value.files;
            this.filesCount = value.filesCount;
            this._repaint();
        },

        _repaint: function() {
            if(this.filesCount > 0) {
                this.container.style.display = 'inline';
                this.dropDownNode.set('label',"<img src='/bower_components/fugue/icons/paper-clip.png' style='vertical-align: middle'>("+this.filesCount+')');
                this.dropDownNode.dropDown = this._buildDropDown();
            } else this.container.style.display = 'none';
        },

        _buildDropDown: function() {
            var t = this;
            var menu = new DropDownMenu();
            array.forEach(this.files, function(file) {

                var menuItem = new MenuItem({
                    label: file.name,
                    onClick: function(){ t.fileRepo.previewOrDownload(file); }
                });
                menu.addChild(menuItem);

            });

            return menu;
        }

    });
 
});