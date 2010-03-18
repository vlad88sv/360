<?php
require_once ("PHP/vital.php");
ini_set('memory_limit', '128M');
set_time_limit(0);
$arrCSS[] = 'estilo';
$HEAD_titulo = PROY_NOMBRE . ' Lista multipropositos (Facebook, Foros, etc.)';
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
</head>
<body>
<div id="secc_general">
<?php
$c = 'SELECT var.foto, con.`codigo_producto`, con.`titulo`, con.`descripcion` AS "contenedor_descripcion", CONCAT("$",(IF(MIN(var.precio)=MAX(var.precio),var.precio,CONCAT(MIN(var.precio), " - $",MAX(var.precio))))) AS "precio_combinado" FROM `flores_producto_contenedor` AS con LEFT JOIN `flores_producto_variedad` AS var USING(codigo_producto) WHERE 1 GROUP BY var.codigo_producto ORDER BY codigo_producto';
$r = db_consultar($c);
echo '<table>';

while ($f = mysql_fetch_assoc($r))
{
    $escalado = 'IMG/fb/'.$f['codigo_producto'].'.jpg';
    $origen = 'IMG/i/'.$f['foto'];
    $ancho = 350;
    $alto = 525;

    if (!file_exists($escalado))
    {
       $im=new Imagick($origen);

       $im->setCompression(Imagick::COMPRESSION_JPEG);
       $im->setCompressionQuality(90);
       $im->setImageFormat('jpeg');
       $im->stripImage();
       $im->sharpenImage(0.5,1);
       $im->thumbnailImage($ancho,$alto,false);
       $im->writeImage($escalado);
    }
    $img = '<img src="IMG/fb/'.$f['codigo_producto'].'.jpg" />';
    $txt = '<textarea onClick="select();" style="height:20em;width:60em;">'.$f['titulo']."\n-\n".$f['contenedor_descripcion']."\n-\nCódigo del producto: #".$f['codigo_producto']."\n-\n".PROY_URL.'</textarea><br /><input  onClick="select();" style="width:60em;" type="text" value="[url='.PROY_URL.'vitrina-'.SEO($f['titulo'].'-'.$f['codigo_producto']).'][img]'.PROY_URL.'imagen_350_525_'.$f['foto'].'.jpg[/img][/url]"/><br />'.$f['precio_combinado'];
    echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',$f['codigo_producto'],$img,$txt);
}

echo '</table>';
?>
</body>
</html>
