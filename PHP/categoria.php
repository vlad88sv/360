<?php

    // Escribir las opciones si es administrador unicamente
    if (_F_usuario_cache('nivel') == _N_administrador && isset($_POST['btn_nElementos_por_fila']) && isset($_POST['txt_nElementos_por_fila']) && is_numeric($_POST['txt_nElementos_por_fila']))
        escribir_opcion('categoria_articulos_por_fila',$_POST['txt_nElementos_por_fila']);

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

    // Obtenemos el modo de operacion
    // Superior = mostrar todos los tipo/categoria/especial que pertenezcan al menu
    // Filtro = ejecutar el query con where = filtro
    // Normal (por defecto) = mostrar los productos dentro del codigo_categoria seleccionado
    if (isset($_GET['modo']) && $_GET['modo'] == 'superior')
    {
        $variante = '';
        $WHERE = 'WHERE cat.codigo_menu='.db_codex($_GET['codigo_categoria']);

        // Para titulo y refinado
        $titulo = db_obtener(db_prefijo.'menu','titulo','codigo_menu="'.db_codex($_GET['codigo_categoria']).'"');
        $HEAD_titulo = PROY_NOMBRE . ' - las mas frescas flores y plantas para toda ocasión! - ' . $titulo;
        $HEAD_descripcion = "Flor360 la primera floristería en línea de El Salvador te ofrece una gran variedad de $titulo frescas para eventos, bodas, dia de la madre, funerales y todo evento social." ;
    }
    elseif (isset($_GET['modo']) && $_GET['modo'] == 'filtro')
    {
        $c = sprintf('SELECT nombre_filtro, filtro_sql, descripcion_filtro FROM %s WHERE nombre_filtro="%s"',db_prefijo.'filtros',db_codex($_GET['codigo_categoria']));
        $FILTRO = mysql_fetch_assoc(db_consultar($c));
        if (empty($FILTRO['filtro_sql']))
        {
            echo 'Filtro inválido';
            return;
        }
        $WHERE = 'WHERE ' . $FILTRO['filtro_sql'];

        $HEAD_titulo = PROY_NOMBRE . ' - exclusividad en flores';
        $HEAD_descripcion = 'Flor360 - '.$FILTRO['descripcion_filtro'];
    }
    else
    {
        $variante = 'codigo_categoria='.db_codex($_GET['codigo_categoria']);
        $WHERE = sprintf('WHERE %s',$variante);

        // Para titulo y refinado
        $c = 'SELECT tipo, titulo, descripcion FROM '.db_prefijo.'categorias'.' WHERE codigo_categoria="'.db_codex($_GET['codigo_categoria']).'" LIMIT 1';
        $CATEGORIA = mysql_fetch_assoc(db_consultar($c));
        if ($CATEGORIA['tipo'] != 'especial')
        {
            $ocultar_ocasion = ($CATEGORIA['tipo'] == 'ocasion');
            $ocultar_tipo = !$CATEGORIA['tipo'];
        }
        $HEAD_titulo = PROY_NOMBRE . ' - las mas frescas flores y plantas para toda ocasión! -  ' . $CATEGORIA['titulo'];
        $HEAD_descripcion = $CATEGORIA['descripcion'];

    }

    switch (@$_GET['refinado'])
    {
        case 'color':
            $REFINADO = ' AND pc.color="'.$_GET['valor'].'"';
            break;
        case 'precio':
            $pregs = array('/^\-(\d+)/' => ' AND pv.precio<$1','/(\d+)\-(\d+)/' => 'AND pv.precio BETWEEN $1 AND $2','/^[\+\s](\d+)/' => ' AND pv.precio>$1');
            $precio = preg_replace(array_keys($pregs),array_values($pregs),$_GET['valor']);
            $REFINADO = $precio;
            break;
        case 'categoria':
            $REFINADO = ' AND pc.codigo_producto IN (SELECT codigo_producto FROM flores_productos_categoria WHERE codigo_categoria="'.$_GET['valor'].'")';
            break;
        default:
            $REFINADO = '';
    }

    $bELEMENTOS = '';

    $FROM = sprintf('FROM flores_producto_contenedor AS pc LEFT JOIN (SELECT * FROM flores_producto_variedad ORDER BY precio ASC) AS pv USING(codigo_producto) LEFT JOIN flores_productos_categoria AS pcat USING(codigo_producto) LEFT JOIN flores_categorias AS cat USING(codigo_categoria) %s',$WHERE);
    $GROUP_BY = 'GROUP BY pv.codigo_producto';
    $c = 'SELECT '. join(', ',$CAMPOS) . ' ' . $FROM . ' AND descontinuado="no" '. $REFINADO .' ' . $GROUP_BY . ' ORDER BY RAND(curdate()+0)';
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
                    $f['variedad_foto'] = imagen_URL($f['variedad_foto'],133,200);
                }
                $bELEMENTOS .= '<div class="categoria-elemento">
                <a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']).'">
                <img title="'.$f['contenedor_descripcion'].'" src="'.$f['variedad_foto'].'" />
                </a>';
                $bELEMENTOS .= '<div class="titulo">'.$f['contenedor_titulo'].'</div>';
                $bELEMENTOS .= '<div class="precio">'.$f['precio_combinado'].'</div>';
                if(isset($_GET['preparacion']))
                {
                    if (isset($_GET['no_cantidad']))
                        $f['variedad_receta'] = preg_replace(array('/[^\w,\s]/','/\d\s{0,1}/'),'',$f['variedad_receta']);
                    $bELEMENTOS .= '<center><div style="text-align:center;width:133px;height:60px;">'.$f['variedad_receta'].'</div></center>';
                }
                $bELEMENTOS .= '</div>';
            }
        $bELEMENTOS .= '</td>';
        }
        $bELEMENTOS .= '</tr>';
    }
    $bELEMENTOS .= '</table>';

    /***** Opciones de refinado ****************/

    $bFiltro = '<div id="caja-refinado">';

    $bFiltro .= '<h1>Refinado</h1>';
    if (@!$ocultar_ocasion)
    {
        $c = 'SELECT COUNT(*) AS cuenta, tcat.codigo_categoria, tcat.titulo AS "titulo_categoria"'. ' FROM flores_producto_contenedor LEFT JOIN flores_productos_categoria USING(codigo_producto) LEFT JOIN flores_categorias AS tcat USING(codigo_categoria) WHERE codigo_producto IN (SELECT pc.codigo_producto ' . $FROM .') AND tcat.tipo="ocasion" GROUP BY codigo_categoria ORDER BY tcat.titulo ASC';
        $r = db_consultar($c);

        $bFiltro .= '<h2>Ocasiones</h2>';
        $bFiltro .= '<table>';
        while ($f = mysql_fetch_array($r))
            $bFiltro .= '<tr><td><a rel="nofollow" href="'.PROY_URL_ACTUAL.'?refinado=categoria&valor='.$f['codigo_categoria'].'">'.$f['titulo_categoria'].'</a></td><td>['.$f['cuenta'].']</td></tr>'."\n";
        $bFiltro .= '</table>';

    }

    if (@!$ocultar_tipo)
    {
        $c = 'SELECT COUNT(*) AS cuenta, tcat.codigo_categoria, tcat.titulo AS "titulo_categoria"'. ' FROM flores_producto_contenedor LEFT JOIN flores_productos_categoria USING(codigo_producto) LEFT JOIN flores_categorias AS tcat USING(codigo_categoria) WHERE codigo_producto IN (SELECT pc.codigo_producto ' . $FROM .') AND tcat.tipo="tipo" GROUP BY codigo_categoria';
        $r = db_consultar($c);

        $bFiltro .= '<h2>Tipo</h2>';

        $bFiltro .= '<table>';
        while ($f = mysql_fetch_array($r))
            $bFiltro .= '<tr><td><a rel="nofollow" href="'.PROY_URL_ACTUAL.'?refinado=categoria&valor='.$f['codigo_categoria'].'">'.$f['titulo_categoria'].'</a></td><td>['.$f['cuenta'].']</td></tr>'."\n";
        $bFiltro .= '</table>';
    }

    $c = 'SELECT rango_precio, COUNT(DISTINCT codigo_producto) AS cuenta FROM (SELECT pc.codigo_producto, pv.precio, CASE WHEN MAX(pv.precio) < 15 THEN "-15" WHEN MAX(pv.precio) BETWEEN 15 AND 30 THEN "15-30" WHEN MAX(pv.precio) BETWEEN 30 AND 45 THEN "30-45" WHEN MAX(pv.precio) > 45 THEN "+45" END AS rango_precio ' . $FROM . ' GROUP BY pc.codigo_producto) AS latabla GROUP BY rango_precio ORDER BY latabla.precio';
    $r = db_consultar($c);
    $bFiltro .= '<h2>Precio</h2>';

    $bFiltro .= '<table>';
    while ($f = mysql_fetch_array($r))
    {
        $pregs = array('/^\-(\d+)/' => 'De \$$1 o menos','/(\d+)\-(\d+)/' => 'De \$$1 a \$$2','/^\+(\d+)/' => 'De \$$1 a más');

        $f['precio_explicado'] = preg_replace(array_keys($pregs),array_values($pregs),$f['rango_precio']);

        $bFiltro .= '<tr><td><a rel="nofollow" href="'.PROY_URL_ACTUAL.'?refinado=precio&valor='.$f['rango_precio'].'">'.$f['precio_explicado'].'</a></td><td>['.$f['cuenta'].']</td></tr>'."\n";
    }
    $bFiltro .= '</table>';

    /* COLOR */
    $c = 'SELECT COUNT(DISTINCT pc.codigo_producto) AS cuenta, pc.color ' . $FROM . ' GROUP BY pc.color ORDER BY CAST(pc.color AS CHAR) ASC';
    $r = db_consultar($c);
    $bFiltro .= '<h2>Color</h2>';

    $bFiltro .= '<table>';
    while ($f = mysql_fetch_array($r))
    {
        $bFiltro .= '<tr><td><a rel="nofollow" href="'.PROY_URL_ACTUAL.'?refinado=color&valor='.$f['color'].'">'.$f['color'].'</a></td><td>['.$f['cuenta'].']</td></tr>'."\n";
    }
    $bFiltro .= '</table>';

    $bFiltro .= '</div>';

/* SALIDA */
if (_F_usuario_cache('nivel') == _N_administrador)
{
echo '<div style="display:block;clear:both;width:100%;">'.ui_input('js_admin','Mostrar/Ocultar opciones de administración',"button").'</div>';
echo JS_onload('$(".admin360").hide();$("#js_admin").click(function () {$(".admin360").toggle();});');
    echo '<div class="admin360" style="display:block;width:100%;text-align:center">';
    echo '<hr />';
    echo '<form action="'.PROY_URL_ACTUAL.'" method="POST" enctype="multipart/form-data"/>';
    echo '<h1>Opciones de administración</h1>';
    echo 'Número de elementos por fila: '.ui_input('txt_nElementos_por_fila',opcion('categoria_articulos_por_fila',4)).' '.ui_input('btn_nElementos_por_fila','Establecer','submit','btnlnk');
    '</form>';
    echo '<hr />';
    echo '</div>';
}
echo '<h1>Flores, regalos, decoraciones y arreglos florales en El Salvador.<br />Tel. '.PROY_TELEFONO.'</h1>';
echo '<table style="width:100%;border-collapse:collapse;margin:0;border:none;padding:0">';
echo '<tr>';
echo '<td style="width:170px;vertical-align:top;">'.$bFiltro.'</td>';
echo '<td style="width:800px;vertical-align:top;">'.$bELEMENTOS.'</td>';
echo '</tr>';
echo '</table>';

?>
