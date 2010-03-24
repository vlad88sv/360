<?php
ini_set('memory_limit', '128M');
set_time_limit(0);
if (!isset($_GET['tipo'])) $tipo = 'normal';

switch($_GET['tipo'])
{
    case 'normal':
        IMAGEN_tipo_normal();
        break;
    case 'tcredito':
        IMAGEN_tipo_tcredito();
        break;
    case 'random':
        IMAGEN_tipo_random();
        break;
}

function IMAGEN_tipo_tcredito()
{
    require_once('PHP/vital.php');
    protegerme();
    $c = sprintf('SELECT AES_DECRYPT(`n_credito`,"%s") AS n_credito_DAES FROM `flores_SSL_compra_contenedor` WHERE transaccion="%s"',db__key_str,$_GET['pin']);
    $r = mysql_query($c);
    $f = mysql_fetch_assoc($r);
    $string = $f['n_credito_DAES'];
    $im    = ImageCreate((int)(strlen($string) * 6.25), 12);
    $background_color = ImageColorAllocate ($im, 224, 230, 255);
    $text_color = ImageColorAllocate ($im, 0, 0, 0);
    ImageString ($im, 2, 1, 0, "$string", $text_color);
    header("Content-type: image/png");
    ImagePNG($im);
    ImageDestroy($im);
}
function IMAGEN_tipo_normal()
{
    $escalado = ('IMG/i/m/'.$_GET['ancho'].'_'.$_GET['alto'].'_'.$_GET['sha1']);
    $origen = 'IMG/i/'.$_GET['sha1'];
    $ancho = $_GET['ancho'];
    $alto = $_GET['alto'];

    if (!file_exists($escalado))
    {
       $im=new Imagick($origen);

       $im->setCompression(Imagick::COMPRESSION_JPEG);
       $im->setCompressionQuality(90);
       $im->setImageFormat('jpeg');
       $im->stripImage();
       $im->sharpenImage(0.5,1);
       $im->setInterlaceScheme(Imagick::INTERLACE_PLANE);
       $im->thumbnailImage($ancho,$alto,false);
       $im->writeImage($escalado);
    }

    $im=new Imagick($escalado);
    $output = $im->getimageblob();
    $outputtype = $im->getFormat();
    header("Content-type: $outputtype");
    header("Content-length: " . filesize($escalado));
    echo $output;
}

function IMAGEN_tipo_random()
{
    require_once('PHP/vital.php');

    $archivo = 'estatico/imagen_sms.todosv.com.jpg';
    
    $c = 'SELECT DISTINCT codigo_producto, foto FROM flores_producto_variedad ORDER BY RAND() LIMIT 3';
    $r = db_consultar($c);

    $canvas = imagecreatetruecolor(336,168);
    $x = 0;
    while($f = mysql_fetch_assoc($r))
    {
        $foto  = imagecreatefromjpeg('IMG/i/'.$f['foto']);
        imagecopyresampled($canvas,$foto,$x,0,0,0,112,168,imagesx($foto),imagesy($foto));
        imagedestroy($foto);
        $x += 112;
    }
    $logo = imagecreatefrompng("estatico/logo_difuso.png");
    imagecopy($canvas,$logo,0,0,0,0,336,168);
    imagejpeg($canvas,$archivo,65);
    imagedestroy($canvas);
}
exit;
?>
