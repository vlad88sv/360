<?php
require_once('PHP/stemm_es.php');
$modo = 'iconos';

function __variar_color_callback($color)
{
    return '/'.$color.'[aeiou]s{0,1}/i';
}

function __stemm($palabra)
{
    $stemm = stemm_es::stemm($palabra);
    return $stemm.'*';
}

$COMUN = 'SELECT CONCAT("$",(IF(MIN(pv.precio)=MAX(pv.precio),pv.precio,CONCAT(MIN(pv.precio), " - $",MAX(pv.precio))))) AS "precio_combinado", pv.foto AS "variedad_foto", IF(pc.titulo="","sin titulo",pc.titulo) AS "contenedor_titulo", pc.descripcion AS "contenedor_descripcion", pc.codigo_producto, pc.color';

$GROUP = 'GROUP BY pv.codigo_producto';

$ORDER_BY = '';

$busqueda = db_codex(trim($_GET['busqueda']));
if (empty($busqueda))
{
    echo 'Búsqueda inválida';
    return;
}

if (is_numeric($busqueda))
{

    // Hacemos el intento de búsqueda directa del código
    $WHERE = 'pc.codigo_producto = "'.$busqueda.'"';
    $r = buscar();

    if (mysql_num_rows($r))
    {
        $f = mysql_fetch_assoc($r);
        header('Location: ' . PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']));
        ob_end_clean();
        exit;
    }

    // No encontramos coincidencia directa, no intentar parcial en codigo_producto;

}

// Ok, no era un numero, intento un texto, pero no debe ser menor de 3 letras
if (strlen($busqueda)<3)
{
    echo 'Texto de búsqueda demasiado corto';
    return;
}

if (isset($_POST['btn_refinar']))
{
    $modo = 'detallado';
    // Búsqueda detallada
    echo '<form action="'.PROY_URL_ACTUAL_DINAMICA.'" method="post" >';
    echo ui_input('btn_modo_normal','Mostrar resultado de búsqueda en iconos','submit','btnlnk');
    echo '</form>';
}
else
{
    // Búsqueda detallada
    echo '<form action="'.PROY_URL_ACTUAL_DINAMICA.'" method="post" >';
    echo ui_input('btn_refinar','Mostrar resultado de búsqueda detallado','submit','btnlnk');
    echo '</form>';
}

// Hasta el momento todo bien, preparemos el texto para encontrar lo mas posible
$busqueda_regexed = preg_replace(array_map('__regexar',unserialize(STOPWORDS)),' ',$busqueda);
$busqueda_regexed = preg_replace('/[^\w\dñÑ]/',' ',$busqueda_regexed);
// Remplazemos variaciones de los colores para acertar aun mejor
$VARIACIONES = array('color(es){0,1} variad' => 'multicolor', 'rosad' => 'rosa', 'amarill' => 'amarillo', 'roj' => 'rojo');
$busqueda_regexed = preg_replace(array_map('__variar_color_callback',array_keys($VARIACIONES)),array_values($VARIACIONES),$busqueda_regexed);
$busqueda_regexed = preg_replace('/\s\s*/',' ',trim($busqueda_regexed));

// ¿Será que búsca un color?
$colores = array_intersect(array_map('strtolower', split (' ',$busqueda_regexed)), array_map('strtolower', unserialize(COLORES)));

if (count($colores))
{

    $WHERE = 'pc.color LIKE "' . join('" OR pc.color LIKE "',$colores) . '"';
    $ORDER_BY = 'ORDER BY pc.color ASC';
    $r = buscar();
    if (mysql_num_rows($r))
    {
        echo '<h1>Encontrados por color</h1>';
        echo '<p>Se detectaron los colores "'.join(', ',$colores).'" en su texto de búsqueda, a continuación se muestran los resultados.</p>';
        echo BUSCAR_mostrar_resultados($r);
    }
}

// Probemos con el titulo y descripcion de los contenedores...

//Steem it! -- nuevo conocimiento =D
/* Explicación:
  * Toma $busqueda_regexed y le aplica stemm a cada palabra para luego volver a unir un array "stemm"-ado
  * Para callbacks, los metodos estaticos (stemm_es::stemm para el caso) deben ser partidos como en el ej.
*/
$busqueda_regexed_fase_2 = join(' ',array_map('__stemm',split(' ',$busqueda_regexed)));

$COMUN = $COMUN.', MATCH(pc.titulo) AGAINST("'.$busqueda_regexed_fase_2.'" IN BOOLEAN MODE) AS puntaje_titulo, MATCH(pc.descripcion) AGAINST("'.$busqueda_regexed_fase_2.'" IN BOOLEAN MODE) AS puntaje_descripcion';

$WHERE = ' (MATCH(pc.titulo) AGAINST("'.$busqueda_regexed_fase_2.'" IN BOOLEAN MODE) OR MATCH(pc.descripcion) AGAINST("'.$busqueda_regexed_fase_2.'" IN BOOLEAN MODE))';

$ORDER_BY = 'ORDER BY puntaje_titulo DESC, puntaje_descripcion DESC';

$r = buscar();
$nResultadosPorTexto = mysql_num_rows($r);
if ($nResultadosPorTexto)
{
    echo '<h1>Encontrados por título/descripción</h1>';
    echo '<p>Se encontraron los siguientes productos que concuerdan con su texto de búsqueda ('.$busqueda.' => '.$busqueda_regexed.' => '.$busqueda_regexed_fase_2.'), a continuación se muestran los resultados.</p>';
    echo BUSCAR_mostrar_resultados($r);
}

// Totalmente refinado -- Si hubieron colores entonces tratar de super refinar
if (count($colores) && $nResultadosPorTexto)
{

    $WHERE .= ' AND pc.color LIKE "' . join('" OR pc.color LIKE "',$colores) . '"';
    $ORDER_BY = 'ORDER BY pc.color ASC';
    $r = buscar();
    if (mysql_num_rows($r))
    {
        echo '<h1>Encontrados por [titulo ó descripción] y color</h1>';
        echo '<p>Se detectaron los colores "'.join(', ',$colores).'" en su texto de búsqueda, a continuación se muestran los resultados de los productos que sean del color escogido.</p>';
        echo BUSCAR_mostrar_resultados($r);
    }
}


// Registremos la búsqueda y fin!
/* INET_ATON = Adress 2 Number; INET_NTOA = Number 2 Adress */

@$c = sprintf('INSERT INTO %s (codigo_busqueda,ip,texto_buscado,texto_buscado_regexed,texto_buscado_regexed_fase_2,fecha,referencia) VALUES (NULL,INET_ATON("%s"),"%s","%s","%s",NOW(),"%s")',db_prefijo.'busquedas',$_SERVER['REMOTE_ADDR'], $busqueda, $busqueda_regexed, $busqueda_regexed_fase_2, db_codex(@$_SERVER['HTTP_REFERER']));
@db_consultar($c);

function buscar()
{
    global $COMUN, $WHERE, $GROUP, $ORDER_BY;
    $c = sprintf('%s FROM flores_producto_variedad AS pv LEFT JOIN flores_producto_contenedor AS pc USING(codigo_producto) WHERE %s %s %s', $COMUN, $WHERE, $GROUP, $ORDER_BY);
    return db_consultar($c);
}

function BUSCAR_mostrar_resultados($r)
{
    global $modo;
    if ($modo == 'iconos')
    {
        $nElementos = mysql_num_rows($r);
        $nFilas = ceil($nElementos / opcion('categoria_articulos_por_fila',4));

        $bELEMENTOS = '<table style="width:100%;table-layout:fixed;border-collapse:collapse;margin:0;border:none;padding:0">';
        for($i=0;$i<$nFilas;$i++)
        {
            $bELEMENTOS .= '<tr>';
            for($j=0;$j<opcion('categoria_articulos_por_fila',4);$j++)
            {
            $bELEMENTOS .= '<td style="text-align:center;vertical-align:top;">';
                if($f = mysql_fetch_assoc($r))
                {
                    $bELEMENTOS .= '<div class="categoria-elemento">
                    <a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']).'">
                    <img style="height:200px;" title="'.$f['contenedor_descripcion'].'" src="'.imagen_URL($f['variedad_foto'],0,200).'" />
                    </a>';
                    $bELEMENTOS .= '<div class="titulo">'.$f['contenedor_titulo'].'</div>';
                    $bELEMENTOS .= '<div class="precio">'.$f['precio_combinado'].'</div>';
                    $bELEMENTOS .= '</div>';
                }
            $bELEMENTOS .= '</td>';
            }
            $bELEMENTOS .= '</tr>';
        }
        $bELEMENTOS .= '</table>';
        echo $bELEMENTOS;
    }
    else
    {
        echo '<table class="ancha">';
        echo '<tr><th>Fotografía</th><th>Título</th><th>Descripción</th><th>Color</th><th>Precio</th></tr>';
        while($f=mysql_fetch_assoc($r))
        {
            echo sprintf('<tr><td>%s</td><td nowrap="nowrap">%s</td><td>%s</td><td>%s</td><td nowrap="nowrap">%s</td></tr>',
                         '<img style="height:100px" src="'.imagen_URL($f['variedad_foto'],0,100).'" />',
                         '<a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']).'">'.$f['contenedor_titulo'].'</a>',
                         nl2br($f['contenedor_descripcion']),
                         $f['color'],
                         $f['precio_combinado']
                         );
        }
        echo '</table>';
    }
}
?>
