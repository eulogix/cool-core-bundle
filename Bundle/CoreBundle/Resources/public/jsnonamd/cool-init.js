/**
 * creates some global objects and preloads widgets needed by widgetInTemplate widgets
 * (which does not auto require)
 */

var COOL = {
    whenReady: function(lambda) {
        require(["cool/cool", "cool/functions/waiter"], function(cool, coolWaiter) {
            lambda();
        });
    }
};

var GlobalTranslator = {};

require([
    "cool/cool",
    "cool/translator",
    "cool/file/repository",
    "cool/file/repoThumbnail",
    "cool/file/repoGallery",
    "cool/file/repoButtonList",
    "cool/renderers/truncator"
], function(cool, ctr){
    COOL = cool;
    COOL.ready = true;
    COOL.whenReady = function(lambda) { lambda(); };
    GlobalTranslator = new ctr({domain:'COOL_GLOBAL_TRANSLATOR'});
});

//fix for chosen
jQuery(function () {
    var els = jQuery(".chosen-select");
    els.on("chosen:showing_dropdown", function () {
        jQuery(this).parents("div").css("overflow", "visible");
    });
    els.on("chosen:hiding_dropdown", function () {
        var $parent = jQuery(this).parents("div");

        // See if we need to reset the overflow or not.
        var noOtherExpanded = jQuery('.chosen-with-drop', $parent).length == 0;
        if (noOtherExpanded)
            $parent.css("overflow", "");
    });
});