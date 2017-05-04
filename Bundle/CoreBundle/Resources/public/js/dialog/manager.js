define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/on",
        "dojo/mouse",

        "dojo/dom",
        "dojo/dom-construct",
        "dojo/dom-style",
        "dojo/window",

        "dijit/Tooltip",
        "dojox/widget/DialogSimple"

    ], function(declare, lang, on, mouse, dom, domConstruct, domStyle, win, Tooltip, Dialog) {

    return {

        openWidgetDialog: function(serverId, title, parameters, onClose, onWidgetInstantiation, onWidgetBindSuccess, config) {

            parameters = parameters || {};
            config = config || {};
            title = title || "TITLE?";

            var t = this;

            var vs = win.getBox();

            var w = config.w || Math.max(vs.w-100, 1200);
            var h = config.h || Math.max(vs.h-200, 700);

            var d = t._getModalDialog(title);

            domStyle.set(d.domNode, {
                width:  w + "px",
                height: h + "px"
            });
            d.domNode.style['overflow'] = config.overflow || "auto";

            COOL.widgetFactory(serverId, parameters, function(widget) {

                if(lang.isFunction(onWidgetInstantiation))
                    onWidgetInstantiation(widget);

                widget.onlyContent = true;
                d.addChild( widget );

                widget.dialog = d;

                d.show();

            }, function(widget) {
                if(lang.isFunction(onWidgetBindSuccess))
                    onWidgetBindSuccess(widget);
            });

            if(typeof(onClose)=='function')
                d.connect(d, "hide", function(e){
                    onClose();
                });

            return d;

        },

        _getModalDialog: function(title, href, sizeRatio) {
            sizeRatio = sizeRatio || 80;
            var vs = win.getBox();

            var w = Math.floor((vs.w/100)*sizeRatio);
            var h = Math.floor((vs.h/100)*sizeRatio);

            var d = new Dialog({
                title: title,
                content: "",
                draggable: true,
                closable: true,
                href: href,
                maxRatio: sizeRatio,
                scriptHasHooks: true,
                style: "background-color: #FFFFFF;"
            });

            d._forcedWidth = w;
            d._forcedHeight = h;

            domStyle.set(d.containerNode, 'position', "relative");
            domStyle.set(d.containerNode, 'padding', "0px");

            return d;
        },

        trackMouseOver: function(node) {
            if(!node._mouseEnterSignal) {
                node._mouseEnterSignal = on(node, mouse.enter, function(){ node._mouseOver = true;});
                node._mouseLeaveSignal = on(node, mouse.leave, function(){ node._mouseOver = false;});
                return false;
            }
            return true;
        },

        showTooltip: function(node, content) {
            Tooltip.show(content, node);

            on.once(node, mouse.leave, function(){
                Tooltip.hide(node);
            });
            console.log("showTooltip is deprecated, use bindTooltip")
        },

        hideTooltip: function(node) {
            Tooltip.hide(node);
        },

        bindTooltip: function(node, content, maxWidth) {

            if(maxWidth)
                content = "<div style='max-width:"+maxWidth+"px'>" + content + "</div>";

            this.unbindTooltip(node);

            node._toolTipEnterSignal = on(node, mouse.enter, function(){
                Tooltip.show(content, node);
            });

            node._toolTipLeaveSignal = on(node, mouse.leave, function(){
                Tooltip.hide(node);
            });

            if(this.trackMouseOver(node)) {
                if(node._mouseOver)
                    on.emit(node, "mouseover", {
                        bubbles: true,
                        cancelable: true
                    });
            } else {
                console.log("node was not tracked. To solve this issue, call COOL.getDialogManager().trackMouseOver() over it before calling bindTooltip the first time");
            }
        },

        unbindTooltip: function(node) {
            if(node._toolTipEnterSignal) {
                node._toolTipEnterSignal.remove();
                node._toolTipLeaveSignal.remove();
            }
        },

        showXhrError: function (title, url, text) {
            errDialog = new Dialog({
                title: title,
                content: '<div style="padding:5px; border:1px solid black; margin-bottom:10px;">'+url+'</div>'+text,
                style: "width: 1000px; height:600px; overflow:auto;"
            });
            errDialog.show();
        }

    }
 
});