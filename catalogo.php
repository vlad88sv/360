<?php
require_once ("PHP/vital.php");
ini_set('memory_limit', '128M');
set_time_limit(0);
$arrCSS[] = 'estilo';
$HEAD_titulo = PROY_NOMBRE . ' Catalogo multipropositos (Facebook, Foros, etc.)';
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
    /* Tablas */
    // pc = _producto_contenedor
    // pv = _producto_variedad
    // pcat = _productos_categoria
    // cat = _categorias

    /* Campos que utilizaremos */
    // Diff. de precios
    $CAMPOS[] = 'CONCAT("$",(IF(MIN(pv.precio)=MAX(pv.precio),pv.precio,CONCAT(MIN(pv.precio), " - $",MAX(pv.precio))))) AS "precio_combinado"';
    // Nombre de archivo de la foto de la variedad
    $CAMPOS[] = 'pv.foto AS "variedad_foto"';
    $CAMPOS[] = 'pv.receta AS "variedad_receta"';
    $CAMPOS[] = 'IF(pc.titulo="","sin titulo",pc.titulo) AS "contenedor_titulo"';
    $CAMPOS[] = 'pc.descripcion AS "contenedor_descripcion"';
    $CAMPOS[] = 'pc.codigo_producto';
    $CAMPOS[] = 'cat.codigo_categoria';
    $CAMPOS[] = 'cat.titulo AS "titulo_categoria"';

    $HEAD_titulo = PROY_NOMBRE . ' - ¡catalogo completo!';

    $bELEMENTOS = '';

    $FROM = sprintf('FROM flores_producto_contenedor AS pc LEFT JOIN (SELECT * FROM flores_producto_variedad ORDER BY precio ASC) AS pv USING(codigo_producto) LEFT JOIN flores_productos_categoria AS pcat USING(codigo_producto) LEFT JOIN flores_categorias AS cat USING(codigo_categoria)');
    $GROUP_BY = 'GROUP BY pv.codigo_producto';
    $c = 'SELECT '. join(', ',$CAMPOS) . ' ' . $FROM . $GROUP_BY . ' ORDER BY RAND("20100219")';
    $r = db_consultar($c);

    if (!mysql_num_rows($r))
    {
        echo '<div style="display:block">Lo sentimos, por el momento se nos han agotado las existencias de estos productos.</div>';
        return;
    }

    /* Workhorse */
    $nElementos = mysql_num_rows($r);
    $nFilas = ceil($nElementos / opcion('categoria_articulos_por_fila',4));

    $bELEMENTOS .= '<table style="width:100%;table-layout:fixed;border-collapse:collapse;margin:0;border:none;padding:0">';
    for($i=0;$i<$nFilas;$i++)
    {
        $bELEMENTOS .= '<tr>';
        for($j=0;$j<opcion('categoria_articulos_por_fila',4);$j++)
        {
        $bELEMENTOS .= '<td style="text-align:center;vertical-align:top;">';
            if($f = mysql_fetch_assoc($r))
            {
                if (empty($f['variedad_foto']))
                {
                    $f['variedad_foto'] = 'IMG/stock/sin_imagen_133_200.jpg';
                }
                else
                {
                    $f['variedad_foto'] = 'imagen_133_200_'.$f['variedad_foto'];
                }
                $bELEMENTOS .= '<div class="categoria-elemento">
                <a href="'.PROY_URL.'vitrina-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']).'">
                <img style="width:133px;height:200px;" title="'.$f['contenedor_descripcion'].'" src="'.$f['variedad_foto'].'" />
                </a>';
                $bELEMENTOS .= '<div class="titulo">'.$f['contenedor_titulo'].'</div>';
                $bELEMENTOS .= '<div class="precio">'.$f['precio_combinado'].'</div>';
                if (isset($_GET['no_cantidad']))
                    $f['variedad_receta'] = preg_replace(array('/[^\w,\s]/','/\d\s{0,1}/'),'',$f['variedad_receta']);
                    $bELEMENTOS .= '<center><div style="text-align:center;width:133px;height:60px;">'.$f['variedad_receta'].'</div></center>';
                $bELEMENTOS .= '</div>';
            }
        $bELEMENTOS .= '</td>';
        }
        $bELEMENTOS .= '</tr>';
    }
    $bELEMENTOS .= '</table>';

echo '<table style="width:100%;border-collapse:collapse;margin:0;border:none;padding:0">';
echo '<tr>';
echo '<td style="width:100%;vertical-align:top;">'.$bELEMENTOS.'</td>';
echo '</tr>';
echo '</table>';
echo '</div></body></html>';
?>
</div>
</body>
</html>
