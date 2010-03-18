<?php
require_once ("PHP/vital.php");
// Auxiliar para HEAD
$arrHEAD = array();
$arrJS = array();

// Inclusiones JS
$arrJS[] = 'jquery-1.3.2.min';

// Inclusiones CSS
$arrCSS[] = 'estilo';

// Mostrar o no el pie - necesario para la pagina de compras
$GLOBAL_MOSTRAR_PIE = true;

$HEAD_titulo = PROY_NOMBRE;
$HEAD_descripcion = 'Floristerias en El Salvador regalos de arreglos florales con entrega a domicilio. Peluches, ramos florales, bouquet o ramo de novia, botonier para novio, bodas, novias, dia de la madre.';
?>
<?php ob_start(); ?>
<body>
<div id="wrapper">
<div id="header"><?php GENERAR_CABEZA(); ?></div>
<div id="secc_general">
<?php require_once('PHP/traductor.php'); ?>
<?php GENERAR_PIE(); ?>
</div> <!-- secc_general !-->
</div> <!-- wrapper !-->
<?php echo GENERAR_GOOGLE_ANALYTICS(); ?>
</body>
</html>
<?php $BODY = ob_get_clean(); ?>

<?php if (!empty($_LOCATION)) header ("Location: $_LOCATION"); ?>

<?php
/* CAPTURAR <head> */
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
    <title><?php echo $HEAD_titulo; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Style-type" content="text/css" />
    <meta http-equiv="Content-Script-type" content="text/javascript" />
    <meta http-equiv="Content-Language" content="es" />
    <meta name="description" content="<?php echo $HEAD_descripcion; ?>" />
    <meta name="keywords" content="regalos originales, regalos empresariales, navidad, flores a domicilio, envio de flores, san valentin, boda, regalos personalizados, ramos de flores, cumpleaños, promocionales, especiales, aniversario, romantico, cuadros de flores, para mujeres, corporativos, flores artificiales, regalos para bebes, flores secas" />
    <meta name="robots" content="index, follow" />
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
<?php HEAD_CSS(); ?>
<?php HEAD_JS(); ?>
<?php HEAD_EXTRA(); ?>
</head>
<?php $HEAD = ob_get_clean(); ?>
<?php
/* MOSTRAR TODO */
echo  $HEAD,$BODY;
?>
<?php
/* ---------------------------------------------------------------------------*/
/* Funciones adicionales */
function GENERAR_CABEZA()
{
    require_once('PHP/menu_superior.php');
}

function GENERAR_PIE()
{
global $GLOBAL_MOSTRAR_PIE, $HEAD_titulo, $HEAD_descripcion;
if (!$GLOBAL_MOSTRAR_PIE) return;
if (isset($_GET['peticion']) && !in_array($_GET['peticion'],array('vitrina','categoria'))) return;
?>
<h1>¡Comparte <strong>esta</strong> página en la web para que más Salvadoreños alegren su día!</h1>
<center>
<?php
// FaceBook
echo ui_href('',sprintf('http://www.facebook.com/sharer.php?u=%s&t=%s&src=sp',urlencode(PROY_URL_ACTUAL_DINAMICA), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/facebook.gif" title="FaceBook" alt="FaceBook" />','','target="_blank"');
// del.icio.us
echo ui_href('',sprintf('http://del.icio.us/post?url=%s&title=%s',urlencode(PROY_URL), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/delicious.gif" title="del.icio.us" alt="del.icio.us" />','','target="_blank"');
// Digg
echo ui_href('',sprintf('http://digg.com/submit?phase=2&url=%s&title=%s',urlencode(PROY_URL), urlencode(utf8_decode($HEAD_titulo))),'<img class="social" src="IMG/social/digg.gif" title="Digg" alt="Digg" />','','target="_blank"');
// StumbleUpon
echo ui_href('',sprintf('http://www.stumbleupon.com/submit?url=%s&title=%s',urlencode(PROY_URL), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/stumbleupon.gif" title="StumbleUpon" alt="StumbleUpon" />','','target="_blank"');
// Twitter
echo ui_href('',sprintf('http://twitter.com/home?status=Actualmente viendo %s, %s',urlencode(PROY_URL_ACTUAL_DINAMICA), urlencode($HEAD_titulo)),'<img class="social" src="IMG/social/twitter.gif" title="Twitter" alt="Twitter" />','','target="_blank"');
?>
</center>
<div id="inscribete">
<form action="<?php echo PROY_URL?>verificar" method="post">
Inscribe tu correo para recibir ofertas especiales <input name="ce" type="text" value="" /> <input name="inscribir" type="submit" class="btnlnk btnlnk-mini" value="Inscribirme" />
</form>
</div>
<div id="pie-pagina">
<?php cargar_editable('portada'); ?>
</div>
<?php
}

function GENERAR_GOOGLE_ANALYTICS()
{
    return '
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-12744164-1");
pageTracker._trackPageview();
} catch(err) {}</script>
';
}
?>
