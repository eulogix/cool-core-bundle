define("cool/dijit/Button",
	[
    "dojo/_base/declare",
    "dojo/dom-style",
    "dijit/form/Button",
    "dojo/text!./templates/Button.html"
], function(declare, domStyle, Button, template) {
 
    return declare("cool.dijit.Button", [Button], {

        iconSrc: '',
        iconSrcRight: '',

        templateString: template,

        postCreate: function(){
            this.inherited(arguments);
            if(this.iconSrc)
                domStyle.set(this.iconImg, {
                    "display": null
                });

            if(this.iconSrcRight)
                domStyle.set(this.iconImgRight, {
                    "display": null
                });
        }

    });
 
});