define("cool/renderers/truncator",
	[
    "dojo/_base/lang",
    "dojo/_base/declare",
    "dojo/_base/array",
    "dojo/dom",
    "dojo/dom-style",
    "dijit/_WidgetBase",
    "dijit/_TemplatedMixin",
    'dijit/_WidgetsInTemplateMixin',
    "dijit/TooltipDialog",
    "dijit/popup",
    "cool/cool",
    "cool/dialog/manager",
    "dojo/text!./templates/truncator.html"
], function(lang, declare, array, dom, domStyle, _WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin,
            TooltipDialog, popup, cool, dialogManager,
            template) {
 
    return declare("cool/renderers/truncator", [_WidgetBase, _TemplatedMixin, _WidgetsInTemplateMixin], {
        templateString: template,
        maxChars: 200,
        maxTooltipWidth: 500,
        value: null,
        strippedContent: null,

        postCreate : function() {
            this.inherited(arguments);
            dialogManager.trackMouseOver(this.domNode);
        },

        _setValueAttr: function(value) {
            this.value = value + '';
            this.strippedContent = this.extractContent(this.value);
            if(this.strippedContent.length > this.maxChars) {
                var template = Handlebars.compile('{{truncate value '+this.maxChars+'}}');
                this.aNode.innerHTML = template({value: this.strippedContent});

                dialogManager.bindTooltip(this.domNode, this.isHTML(this.value) ? this.value : this.nl2br(this.value), this.maxTooltipWidth);

            } else {
                this.aNode.innerHTML = this.value;
                dialogManager.unbindTooltip(this.domNode);
            }
        },

        extractContent: function(htmlChunk) {
            var span = document.createElement('span');
            span.innerHTML= htmlChunk;
            return span.textContent || span.innerText;
        },

        nl2br: function(str, is_xhtml) {
            var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
        },

        isHTML: function(str) {
            var a = document.createElement('div');
            a.innerHTML = str;
            for (var c = a.childNodes, i = c.length; i--; ) {
                if (c[i].nodeType == 1) return true;
            }
            return false;
        }

    });
 
});