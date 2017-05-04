define("cool/dijit/iconButton",
	[
    "dojo/_base/declare",
    "dijit/form/Button",
    "dojo/text!./templates/iconButton.html"
], function(declare, Button, template) {
 
    return declare("cool.dijit.iconButton", [Button], {

        iconSrc: '',

        templateString: template

    });
 
});