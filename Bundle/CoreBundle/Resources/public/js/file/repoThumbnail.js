define("cool/file/repoThumbnail",
	[
    "dojo/_base/lang",
    "dojo/_base/declare",
    "dojo/_base/array",
    "dojo/dom",
    "dojo/dom-style",
    "dijit/_WidgetBase",
    "dijit/_TemplatedMixin",
    'dijit/_WidgetsInTemplateMixin',

    "dojox/widget/DialogSimple",
    "dojox/image/Lightbox",

    "dojo/text!./templates/repoThumbnail.html"
], function(lang, declare, array, dom, domStyle, _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin,
            Dialog, Lightbox,
            template) {
 
    return declare("cool/file/repoThumbnail", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin], {
        templateString: template,

        title: '',
        repositoryId: null,
        filePath: null,
        thumbWidth: 50,
        fileId: null,

        postCreate : function() {
            this.inherited(arguments);
        },

        _setValueAttr: function(value) {
            this.fileId = value;
            var parameters = {
                width: this.thumbWidth,
                repositoryId: this.repositoryId,
                filePath:this.fileId
            };
            this.thumbnail.src = Routing.generate('frepoGetPreviewImage', parameters);
        },

        showPreview: function() {
            var parameters = {
                repositoryId: this.repositoryId,
                filePath:this.fileId
            };
            var imgHref = Routing.generate('frepoServe', parameters);

            var dialog = new Lightbox.LightboxDialog({});
            dialog.startup();
            dialog.show({ title:this.title, href:imgHref });
        }

    });
 
});