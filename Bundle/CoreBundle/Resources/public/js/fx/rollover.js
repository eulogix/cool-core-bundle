define([
    "dojo/dom-style",
    "dijit/Tooltip"
], function(domStyle, Tooltip) {
 
    var rollover = {

        addImgRollover: function(imgElement, replace, tooltip) {
            var osrc = imgElement.src;
            var dot = osrc.lastIndexOf('.');
            var filename = osrc.substr(0,dot);
            var extension = osrc.substr(dot,osrc.length-dot);
            domStyle.set(imgElement, "cursor", "pointer");

            if(replace) {
                imgElement.onmouseover = function(){this.src=filename+'_over'+extension; };
                imgElement.onmouseout = function(){this.src=osrc;};
            } else {
                dojo.connect(imgElement, "onmouseover", function(evt){
                    imgElement.src = filename+'_over'+extension;
                });
                dojo.connect(imgElement, "onmouseout", function(evt){
                    imgElement.src = osrc;
                });
            }
            if(tooltip) {
                new Tooltip({
                    connectId: imgElement,
                    label: tooltip,
                    showDelay: 150,
                    hideDelay: 0,
                    position: ["below","before","after","above"]
                });
            }
        },

        addAlphaRollover: function(element, alpha, tooltip) {

            dojo.connect(element, "onmouseover", function(evt){
                domStyle.set(element, 'opacity', alpha);
            });
            dojo.connect(element, "onmouseout", function(evt){
                domStyle.set(element, 'opacity', 1);
            });

            if(tooltip) {
                new Tooltip({
                    connectId: element,
                    label: tooltip,
                    showDelay: 1000
                });
            }
        }

    };

    return rollover;

});