define("cool/file/repoGallery",
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

    "cool/store/xhrstore",

    "dojo/text!./templates/repoGallery.html"
], function(lang, declare, array, dom, domStyle, _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin,
            Dialog, Lightbox,
            xhrStore,
            template) {
 
    return declare("cool/file/repoGallery", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin], {
        templateString: template,

        repositoryId: null,

        files: null,

        //how many thumbnails to show
        maxThumbnails: 3,

        postCreate : function() {
            this.inherited(arguments);
            this.folder = '';
            this.files = [];
            this.hideCounter();
        },

        _setValueAttr: function(value) {
            this.files = lang.exists('files',value) ? value.files : [];
            this._repaint();
        },

        showPreview: function() {

            var parameters = {
                repositoryId: this.repositoryId,
                filePath:null
            };

            var dialog = new Lightbox.LightboxDialog({});
            dialog.startup();

            for(var i=0;i<this.files.length;i++) {
                parameters.filePath = this.files[i].id;
                var imgHref = Routing.generate('frepoServe', parameters);
                dialog.addImage({ title:this.files[i].name, href:imgHref }, "gallery");
            }

            dialog.show({ group:"gallery", title:'Gallery' });
        },

        _repaint: function() {

            if(this.files.length==0) {
                this.thumbContainer.style.display = 'none';
            } else {
                this.thumbContainer.style.display = 'inline';
                this.thumbnail.src = this.files[0].iconSrc;

                this.counter.innerHTML = '('+this.files.length+')';
            }
        },

        showCounter: function() {
            this.counter.style.display = 'inline';
        },

        hideCounter: function() {
            this.counter.style.display = 'none';
        }

    });
 
});