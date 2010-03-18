<?php
require_once ("PHP/vital.php");
ini_set('memory_limit', '128M');
set_time_limit(0);
unlinkRecursive('catalogo360/pages/',false);

$c = 'SELECT codigo_categoria, titulo FROM flores_categorias WHERE codigo_menu IN (1,2) ORDER BY titulo';
$r = db_consultar($c);

$XML = simplexml_load_file('catalogo360/marcadores-base.xml');
$XMLP = simplexml_load_file('catalogo360/config-base.xml');
$general = $XML->addChild('seccion');
$general->addAttribute("titulo","Catalogo");
$general->addAttribute("pagina","");

$pagina_actual = 1;
while ($f = mysql_fetch_assoc($r))
{
    $ref = $general->addChild('ref');
    //$ref->addAttribute("titulo",iconv('ISO-8859-1','UTF-8//TRANSLIT',$f['titulo']));
    $ref->addAttribute("titulo",$f['titulo']);
    $ref->addAttribute("pagina",$pagina_actual);


    $cp = 'SELECT foto, pc.titulo FROM flores_productos_categoria LEFT JOIN flores_producto_contenedor AS pc USING(codigo_producto) LEFT JOIN flores_producto_variedad USING(codigo_producto) WHERE codigo_categoria='.$f['codigo_categoria'].' GROUP BY codigo_producto';
    $rp = db_consultar($cp);
    $pagina_actual += 1+mysql_numrows($rp);

    while ($fp = mysql_fetch_assoc($rp))
    {
        if (!file_exists('catalogo360/pages/'.$fp['foto']))
        {
        $im=new Imagick('IMG/i/'.$fp['foto']);
        $im->setCompression(Imagick::COMPRESSION_JPEG);
        $im->setCompressionQuality(90);
        $im->setImageFormat('jpeg');
        $im->stripImage();

        $draw = new ImagickDraw();
        $pixel = new ImagickPixel( 'gray' );
        $pixel->setColor('black');
        $draw->setFont('flower.ttf');
        $draw->setFontSize( 30 );

        $im->thumbnailImage(350,525,false);
        $im->annotateImage($draw, 10, 45, 0, $fp['titulo']);
        $im->writeImage('catalogo360/pages/'.$fp['foto']);
        $im->clear();
        $im->destroy();
        unset($im);
        }
        $XMLP->pages->addChild('page','pages/'.$fp['foto']);
    }

}

file_put_contents('catalogo360/marcadores.xml',$XML->asXML());
file_put_contents('catalogo360/config.xml',$XMLP->asXML());
echo $XML->asXML();
echo $XMLP->asXML();

exit;
?>
