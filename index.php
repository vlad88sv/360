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
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-12744164-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<?php $HEAD = ob_get_clean(); ?>
<?php
/* MOSTRAR TODO */
if(isset($GLOBAL_TIDY_BREAKS))
    echo $HEAD.$BODY;
else
{
    $tidy_config = array('output-xhtml' => true);
    $tidy = tidy_parse_string($HEAD.$BODY,$tidy_config,'UTF8');
    $tidy->cleanRepair();
    echo  trim($tidy);
}
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
global $HEAD_titulo, $HEAD_descripcion;
//if (!isset($_GET['peticion']) || in_array($_GET['peticion'],array('categoria')))
?>
<div id="inscribete">
<form target="_blank" action="<?php echo PROY_URL?>verificar" method="post">
Inscribe tu correo para recibir ofertas especiales <input name="ce" type="text" value="" /> <input name="inscribir" type="submit" class="btnlnk btnlnk-mini" value="Inscribirme" />
</form>

</div>
<div id="pie-pagina">
<?php cargar_editable('portada'); ?>
</div>
<?php
}
?>
