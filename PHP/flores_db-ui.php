<?php
function flores_db_ui_obtener_categorias_cmb($id_gui='cmb_categorias',$no_en=0)
{
    if (!empty($no_en) && is_numeric($no_en))
    {
        $no_en = sprintf('WHERE codigo_categoria NOT IN (SELECT codigo_categoria FROM %s WHERE codigo_producto=%s)',db_prefijo.'productos_categoria',db_codex($no_en));
    }
    return ui_combobox($id_gui,db_ui_opciones('codigo_categoria','titulo',db_prefijo.'categorias',$no_en));
}

function flores_db_ui_obtener_categorias_chkbox($id_gui='chk_categorias',$no_en='')
{
    if (!empty($no_en) && is_numeric($no_en))
    {
        $no_en = sprintf('WHERE codigo_categoria NOT IN (SELECT codigo_categoria FROM %s WHERE codigo_producto=%s)',db_prefijo.'productos_categoria',db_codex($no_en));
    }

    $c = sprintf('SELECT codigo_categoria, titulo FROM %s %s ORDER BY titulo ASC', db_prefijo.'categorias', $no_en);
    $r = db_consultar($c);

    $buffer = '';
    while ($f = mysql_fetch_assoc($r))
    {
        $buffer .= ui_input($id_gui.'[]',$f['codigo_categoria'],'checkbox') . ' ' . $f['titulo'].BR;
    }
    return $buffer;
}

function flores_db_ui_obtener_categorias_y_contenedores_cmb($id_gui='cmb_categorias_y_contenedores')
{
    $c = sprintf('SELECT fprocon.titulo AS "titulo_contenedor", fprocat.codigo_categoria, fprocat.codigo_producto, fcat.titulo AS "titulo_categoria" FROM %s AS fprocon LEFT JOIN (%s AS fprocat LEFT JOIN %s AS fcat ON fprocat.codigo_categoria=fcat.codigo_categoria) ON fprocat.codigo_producto = fprocon.codigo_producto ORDER BY fcat.codigo_categoria',db_prefijo.'producto_contenedor',db_prefijo.'productos_categoria',db_prefijo.'categorias');
    $r = db_consultar($c);

    if (!mysql_num_rows($r))
        return '';

    while ($f = mysql_fetch_assoc($r))
    {
        $categoria[$f['titulo_categoria']][] = array('codigo_categoria' => $f['codigo_categoria'], 'codigo_producto' => $f['codigo_producto'],'titulo_contenedor' => $f['titulo_contenedor']);
    }
    $combobox = '';
    foreach($categoria as $titulo_categoria => $contenedores)
    {
        $combobox .=  "<optgroup label='$titulo_categoria'>";
        foreach($contenedores as $contenedor)
        {
            $combobox .=  '<option value="' . $contenedor['codigo_categoria']. ',' . $contenedor['codigo_producto'] . '">' . $contenedor['titulo_contenedor'] . '</option>';
        }
        $combobox .=  '</optgroup>';
    }

    return $combobox;
}
?>
