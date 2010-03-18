<?php
/* carchivo.php - Cambio de Archivo */
protegerme();
$arrHEAD[] = '
<script type="text/javascript" src="JS/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
        plugins : \'style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,\'+
        \'searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras\',
        themes : \'advanced\',
        languages : \'es\',
        disk_cache : true,
        debug : false
});
</script>
<script type="text/javascript">
tinyMCE.init({
    language : "es",
    elements : "archivo",
    theme : "advanced",
    mode : "exact",
    plugins : "safari,style,layer,table,advhr,advimage,advlink,media,paste,directionality,fullscreen,visualchars,nonbreaking,xhtmlxtras,template",
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,cleanup,code",
    theme_advanced_buttons2 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,advhr,|,ltr,rtl,|,fullscreen",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    button_tile_map : true,
});</script>
';

if (isset($_POST['archivo']))
    file_put_contents('TXT/'.$_GET['archivo'].'.editable',$_POST['archivo']);
echo '<form action="'.PROY_URL_ACTUAL_DINAMICA.'" method="POST"/>';
echo ui_input('guardar','Guardar','submit','btnlnk');
echo '<textarea style="width:100%;height:60em;" name="archivo" id="archivo">';
cargar_editable($_GET['archivo'],false);
echo '</textarea></form>';
?>
