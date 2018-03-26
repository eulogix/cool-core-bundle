define([
    "dojo/_base/lang",
    "dojo/_base/array", // array.filter array.forEach array.map
    "dojo/_base/declare",
    "dojo/dom-style",
    "dojo/request",
    "dojo/request/xhr",
    "dojo/request/util",

    "dojox/image/Lightbox"

], function(lang, array, declare, domStyle, request, xhr, util,
            Lightbox) {

    return {

        /**
         * if the file type can be shown on screen (such as a picture), it is shown using the appropriate viewer.
         * Otherwise it is downloaded
         */
        previewOrDownload: function (serveUrl, downloadUrl, extension, title, siblings) {
            title = title || serveUrl;
            siblings = siblings || [];

            switch (extension.toLowerCase()) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'bmp':
                {
                    this.showImagePreview(serveUrl, title, siblings);
                    break;
                }
                case 'pdf': {
                    window.open(serveUrl, '_blank', 'toolbar=0,location=0,menubar=0');
                    break;
                }
                default:
                    this.downloadToClient(downloadUrl, title);
            }

        },

        showImagePreview: function(url, title, siblings) {
            siblings = siblings || [];

            var dialog = new Lightbox.LightboxDialog({});
            dialog.addImage({ title:title, href:url }, "gallery");
            dialog.startup();

            for(var i=0; i<siblings.length; i++) {
                if(this._isImage(siblings[i].ext) && siblings[i].title != title) {
                    dialog.addImage({
                        title: siblings[i].title,
                        href: siblings[i].serveUrl
                    }, "gallery");
                }
            }

            dialog.show({ group:"gallery", title:'Gallery' });
        },

        downloadToClient: function(url) {
            window.location = url;
        },

        _isImage: function(extension) {
            switch (extension.toLowerCase()) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'bmp': return true;
            }
            return false;
        }

    };

});
