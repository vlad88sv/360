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
$HEAD_descripcion = 'Somos la mas destacada de las floristerias en El Salvador, enviamos a todo el pais de Lunes a Sabado. Aceptamos pedidos nacionales e internacionales, pague con tarjeta de credito o debito, deposito a cuenta o contraentrega. Ofrecemos regalos de arreglos florales, peluches, ramos florales, bouquet o ramo de novia, botonier para novio, decoracion y montaje de bodas, regalos para el dia de la madre y dia del padre.';
?>
<?php ob_start(); ?>
<?php require_once('PHP/traductor.php'); ?>
<?php $BODY = ob_get_clean(); ?>

<?php ob_start(); ?>
<body>

<?php if(!isset($GLOBAL_IMPRESION)) { ?>
    <div id="wrapper">
    <div id="header"><?php GENERAR_CABEZA(); ?></div>
    <div id="secc_general">
    <?php echo $BODY; ?>
    <?php GENERAR_PIE(); ?>
    </div> <!-- secc_general !-->
    </div> <!-- wrapper !-->
<?php } else { ?>
    <style>
    body{background:none !important;background-image:none !important;}
    #wrapper{border:none !important;margin:0 !important;padding:1em !important;}
    .medio-oculto{font-size:11pt;}
    </style>
    <div id="wrapper">
    <div id="secc_general">
    <?php echo $BODY; ?>
    </div> <!-- secc_general !-->
    </div> <!-- wrapper !-->
<?php } ?>
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
<iframe src="http://www.facebook.com/plugins/likebox.php?id=348293355878&amp;width=970&amp;connections=17&amp;stream=false&amp;header=true&amp;height=287" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:970px; height:200px;" allowTransparency="true"></iframe>
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
