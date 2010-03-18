<?php
$archivo = 'TXT/'.$_GET['tema'].'.ayuda.editable';

if (!file_exists($archivo))
{
    global $_LOCATION;
    $_LOCATION = PROY_URL;
}
$GLOBAL_MOSTRAR_PIE = false;
$HEAD_titulo = PROY_NOMBRE . ' - Ayuda del sitio: '.$_GET['tema'];
echo '<div style="text-align:justify">';
cargar_editable($_GET['tema'].'.ayuda');
echo '</div>';

?>
