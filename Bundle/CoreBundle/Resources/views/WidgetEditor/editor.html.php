<?php $view->extend('EulogixCoolCoreBundle::_base/cool_base.html.php') ?>

<div id="editorDiv" style="position: relative; width:100%; height:100%; overflow: auto"></div>

<script>
require(["cool/cool","dojo/domReady!"], function(cool){
                    
    cool.widgetFactory('<?php echo $editorServerId ?>', {
            databaseName:'core',
            tableName:'core.<?php echo $editorTable ?>',
            edit_serverid: window.opener.widgetInEdit.serverId,
            edit_parameters:JSON.stringify( window.opener.widgetInEdit.definition.parameters )
         },
         function(widgetInstance){
            widgetInstance.placeAt(document.getElementById("editorDiv"));
            //target.addChild(widgetInstance);
         });
});
</script>
