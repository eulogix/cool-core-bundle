define("cool/file/repository",
	[
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        'dojo/Deferred',
        "dojox/image/Lightbox"

    ], function(declare, lang, request, Deferred,
                Lightbox) {

    return declare("cool.file.repository", [], {
        /**
         * client side functionalities linked to a server side file repository
         */

        repositoryId: null,
        workingDir: null,
        allArgs: {},

        //TODO: use "cool/file/fileUtils" and refactor this class

        constructor: function(/*Object*/ args){
            lang.mixin(this, args);
            this.allArgs = args
        },

        /**
         * if the file type can be shown on screen (such as a picture), it is shown using the appropriate viewer.
         * Otherwise it is downloaded
         */
        previewOrDownload: function(file) {
            switch(file.extension.toLowerCase()) {
                case 'jpg':
                case 'png':
                case 'gif':
                case 'bmp': {
                    this.showImagePreview(file);
                    break;
                }
                default: this.downloadToClient(file);
            }

        },

        showImagePreview: function(file) {
            var imgHref = Routing.generate('frepoServe', this._buildParameters({filePath: file.id}));

            var dialog = new Lightbox.LightboxDialog({});
            dialog.addImage({ title:file.name, href:imgHref }, "gallery");
            dialog.startup();

            dialog.show({ group:"gallery", title:'Gallery' });
        },

        downloadToClient: function(file) {
            window.location = Routing.generate('frepoDownload', this._buildParameters({filePath: file.id}));
        },

        getAvailableProperties: function(filePath) {
            var d = new Deferred();

            COOL.callCommand('frepoGetAvailableFileProperties',
                function(data) { d.resolve(data); },
                this._buildParameters({filePath: filePath})
            );

            return d;
        },

        _buildParameters: function(parameters) {
            return lang.mixin({}, this.allArgs, parameters);
        }

    });
 
});