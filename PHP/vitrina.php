<?php
    // Comprobación básica: cancelar si no es valido el codigo del contenedor o no existe
    if (!isset($_GET['codigo_contenedor']) || !is_numeric($_GET['codigo_contenedor']))
    {
        header('Location: ' . PROY_URL);
        echo '<p>El código de producto es inválido, redirigiendo a '.ui_href('',PROY_URL,PROY_URL).'</p>';
        return;
    }

    // Primero obtenemos toda la información del contenedor
    $cContenedor = sprintf('SELECT `codigo_producto`, `titulo`, `descripcion`, `vistas`, `descontinuado` FROM `%s` WHERE codigo_producto=%s LIMIT 1',db_prefijo.'producto_contenedor',db_codex($_GET['codigo_contenedor']));
    $rContenedor = db_consultar($cContenedor);

    // Comprobación extendida: cancelar si no se encontró...
    if (!mysql_num_rows($rContenedor))
    {
        header('Location: ' . PROY_URL);
        echo '<p>El código de producto no fue encontrado, redirigiendo a '.ui_href('',PROY_URL,PROY_URL).'</p>';
        return;
    }

    // Existe, entonces obtengamos todos los datos
    $contenedor = mysql_fetch_assoc($rContenedor);

    // Si es admin procesar cualquier cambio
    if (_F_usuario_cache('nivel') == _N_administrador)
    {
        // Si cancelo, anulemos la salida
        if (isset($_POST['PME_sys_canceladd']) || isset($_POST['PME_sys_cancelchange']))
            unset($_POST['referencia']);

        PROCESAR_CATEGORIAS();
        PROCESAR_VARIEDADES();
        PROCESAR_CONTENEDOR();
    }

    // actualizamos la información del contenedor por si PROCESAR_CONTENEDOR() hizo algo...
    $contenedor = mysql_fetch_assoc(db_consultar($cContenedor));

    // Revisamos si la URL es correcta - por los bromistas
    $titulo_SEO = SEO($contenedor['titulo']);
    if ($titulo_SEO != $_GET['titulo'].'.html')
    {
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '. PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($contenedor['titulo'].'-'.$contenedor['codigo_producto']));
        ob_end_clean();
        exit;
    }

    // Titulo de la pagina
    $HEAD_titulo = PROY_NOMBRE . ' - ' . $contenedor['titulo'];
    $HEAD_descripcion = strip_tags($contenedor['descripcion']);

    /*************** variedades ********************************************/
    // Luego obtenemos toda la información de sus variedades
    $c = sprintf('SELECT `codigo_variedad`, `codigo_producto`, `foto`, `descripcion`, `precio` FROM `%s` WHERE codigo_producto="%s" ORDER BY precio ASC, descripcion ASC',db_prefijo.'producto_variedad',$contenedor['codigo_producto']);
    $variedad = db_consultar($c);

    $VARIEDADES_ADMIN = '<h2>Administración de variedades</h2>';
    $VARIEDADES = '';
    $VARIEDADES .= '<table style="width:98%">';
    $PRECIO = 0;
    for ($i=0; $i<mysql_num_rows($variedad); $i++) {
        $f = mysql_fetch_assoc($variedad);
        $VARIEDADES .=  '<tr>';
        $VARIEDADES .= '<td style="width:480px;"><input type="radio" class="variedad" name="variedad"';
        if (empty($flag_selected))
        {
            $PRECIO = $f['precio'];
            $VARIEDADES .= ' checked="checked"';
            $IMG_CONTENEDOR = '<img id="imagen_contenedor" style="width:400px;min-height:300px;display:block;margin:auto;" src="'.imagen_URL($f['foto'],400,0,'img0.').'" />';
        }
        $VARIEDADES .= ' rel="'.imagen_URL($f['foto'],400,0,'img0.').'"';
        $VARIEDADES .= ' id="'.$f['foto'].'"';
        $VARIEDADES .= ' value="'.$f['codigo_variedad'].'" /></td>';
        $VARIEDADES .= '<td style="width:100%">'.htmlentities($f['descripcion']).'</td>'.
        '<td style="text-align:right">$'.$f['precio'].'</td>';
        $VARIEDADES_ADMIN .= '<form action="'.PROY_URL_ACTUAL.'" method="POST"><p style="white-space:nowrap;clear:both;display:block;"><span style="float:left">' . $f['descripcion'] .'</span> <span style="float:right">'. ui_input('codigo_variedad',$f['codigo_variedad'],'hidden').' '.ui_input('btn_editar_variedad','Editar','submit','btnlnk btnlnk-mini').ui_input('btn_eliminar_variedad','Eliminar','submit','btnlnk btnlnk-mini').ui_input('btn_clonar_foto_variedad','Clonar Foto','submit','btnlnk btnlnk-mini').'</span></p></form>';
        $VARIEDADES .= '</tr>';
        $flag_selected=true;
    }
    $VARIEDADES .= '</table>';
    $VARIEDADES_ADMIN = '<div style="display:block;clear:both">'. $VARIEDADES_ADMIN . '</div><form action="'.PROY_URL_ACTUAL.'" method="POST">'.BR . ui_input('btn_agregar_variedad','Agregar variedad', 'submit', 'btnlnk btnlnk-mini').'</form>';

    /********************** bCategoria***************************************/
    $bCategoria= '';

    // Obtengamos las categorias del producto!!!
    $MOSTRAR_EN_VITRINA = SI_ADMIN('AND b.mostrar_en_vitrina=1');
    $c = sprintf('SELECT a.codigo_categoria, b.titulo, b.descripcion FROM %s AS a LEFT JOIN %s AS b ON a.codigo_categoria = b.codigo_categoria WHERE a.codigo_producto="%s" %s ORDER BY b.titulo ASC', db_prefijo.'productos_categoria', db_prefijo.'categorias',$contenedor['codigo_producto'],$MOSTRAR_EN_VITRINA);
    $rCategoria = db_consultar($c);
    $bCategoria.= '<div style="text-align:center;margin:5px">';
    while ($f = mysql_fetch_assoc($rCategoria))
    {
        $bCategoria.= '<span class="etiqueta-categoria">'.$f['titulo'].SI_ADMIN(' <form style="display:inline;" action="'.PROY_URL_ACTUAL.'" method="POST">'.ui_input('codigo_categoria',$f['codigo_categoria'],'hidden').ui_input('btn_eliminar_categoria','x','submit','btnlnk').'</form>').'</span> ';
    }
    $bCategoria.= '</div>';
    //$bCategoria.= SI_ADMIN(BR.flores_db_ui_obtener_categorias_cmb('cmb_agregar_categoria',$contenedor['codigo_producto']).ui_input('btn_agregar_categoria','Agregar','submit'));
    $bCategoria.= SI_ADMIN(BR.'<form action="'.PROY_URL_ACTUAL.'" method="POST">'.flores_db_ui_obtener_categorias_chkbox('chk_agregar_categoria',$contenedor['codigo_producto']).ui_input('btn_agregar_categoria_v2','Agregar','submit','btnlnk').'</form>');

    $cProducto_similar = sprintf('SELECT procon.codigo_producto, procon.titulo, provar.foto, provar.descripcion FROM flores_producto_contenedor AS procon LEFT JOIN flores_producto_variedad AS provar USING(codigo_producto) WHERE codigo_producto <> %s AND precio BETWEEN (%s)*0.60 AND (%s)*1.40 GROUP BY provar.codigo_producto ORDER BY RAND() LIMIT 9',$contenedor['codigo_producto'],$PRECIO,$PRECIO);
    $bProducto_similar = '';
    $rProducto_similar = db_consultar($cProducto_similar);
    if (mysql_num_rows($rProducto_similar))
        while ($fsimilar = mysql_fetch_assoc($rProducto_similar))
            $bProducto_similar .= sprintf('<a href="%s"><img style="width:100px;height:150px;" src="'.imagen_URL($fsimilar['foto'],100,150,'img0.').'" title="Producto similar: %s" /></a> ',PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($fsimilar['titulo'].'-'.$fsimilar['codigo_producto']),$fsimilar['descripcion']);

    /* Desplegar lo que conseguimos */
    if( $contenedor['descontinuado'] == "si" )
        echo '<p class="error">Lo sentimos, este producto esta descontinuado y no se encuentra disponible.</p>';

    // Tabla
    echo '<table style="width:100%;">';
    echo '<tr>';
    echo '<td style="width:50%;vertical-align:top">';

    // Mostrar los datos del contenedor
    echo '<h1>', '#', $contenedor['codigo_producto'], '. ', $contenedor['titulo'], '</h1>';

    if (!isset($IMG_CONTENEDOR))
    {
        $IMG_CONTENEDOR = '<img src="IMG/stock/sin_imagen.jpg" title="Sin Imagen" />';
    }

    $nVistas = SI_ADMIN('<p class="medio-oculto">Veces visto: '. db_contar(db_prefijo.'visita','codigo_producto='.$contenedor['codigo_producto']).'</p>');
    $fbLike = '<iframe src="http://www.facebook.com/widgets/like.php?href='.curPageURL(true).'" scrolling="no" frameborder="0" style="border:none; width:450px; height:80px"></iframe>';
    echo  '<div style="width:100%;text-align:center">'.$IMG_CONTENEDOR.BR.$fbLike.BR.$nVistas;
    echo '</div></td>';
    echo '<td style="width:50%;vertical-align:top">';
    echo '<h1>Detalles</h1>';
    echo '<h2>Descripción</h2>';
    echo '<center><p style="width:90%;text-align:justify;font-size:10pt;">'.nl2br($contenedor['descripcion']).'</p></center>';
if( $contenedor['descontinuado'] == "no" )
{
    echo '<h2>Categoría(s)</h2>';
    echo $bCategoria;
    echo SI_ADMIN($VARIEDADES_ADMIN);
    echo '<h2>Seleccione la variedad</h2>';
    // Fin del area administrativa, inicio de las opciones de compra
    echo '<form action="'.PROY_URL.'comprar-articulo-'.SEO($contenedor['titulo']).'" method="POST">';
    echo $VARIEDADES;
    echo '<hr />';

    $bInfoCompra = '<table>
    <td id="izq-compra" class="medio-oculto">Podrá escoger el texto de la tarjeta (¡gratuita!) en la página de compra. Se aceptan todas las tarjetas de crédito y débito a nivel nacional e internacional.</td>
    <td>' . ui_input('btn_comprar_ahora','Comprar ahora','submit','btn').'<br /><img src="'.PROY_URL.'IMG/stock/credit_card_logos_4.gif"/></td>
    </table>

    <h2>¿Deseas realizar la compra vía telefónica?</h2>
    <p class="medio-oculto">Realiza tu compra vía llamada telefónica al número <strong>2243-6017</strong></p>

    <p class="medio-oculto">No olvides tener listos los siguientes datos:</p>

    <ul class="medio-oculto">
    <li>El código del producto que deseas comprar es: <strong>'.$contenedor['codigo_producto'].'</strong></li>
    <li>La dirección <strong>exacta</strong> de entrega</li>
    <li>La forma de cancelar el producto (contra-entrega, etc.)</li>
    </ul>

    <h2>¿Prefieres los depositos a cuenta?</h2>
    <img style="float:left; margin-right:1em;" src="https://www.bac.net/regional/img/home/elbac.gif" />
    <p class="medio-oculto">
    <strong>Banco de America Central</strong><br />
    Número de cuenta: <strong>200721538</strong><br />
    </p>
    <br />
    <p class="medio-oculto">
    Luego de realizar el deposito comuniquese al '.PROY_TELEFONO.' para hacer su pedido. Se le solicitará el número del deposito.
    </p>

    <h2>¿Deseas realizar la compra en nuestras oficinas?</h2>
    <p class="medio-oculto">
    Visítanos en
    Residencial Cumbres de la Esmeralda, calle Teotl, #20.<br />
    Misma calle de la entrada principal U. Albert Einstein.
    </p>
    ';
}
else
{
    $bInfoCompra = '<h2>Producto descontinuado</h2>';
    $bInfoCompra .= '<p>La elaboracion de este producto ha sido descontinuada.</p>';
    $bInfoCompra .= '<p>Una causa usual es una imposibilidad reciente de conseguir alguna materia prima necesaria para su elaboracion.</p>';
}

echo $bInfoCompra;
echo '</td></tr></table>';
echo '<h2>Productos similares</h2>';
echo $bProducto_similar;

    if (_F_usuario_cache('nivel') != _N_administrador && !db_contar(db_prefijo.'visita','ip=INET_ATON("'.$_SERVER['REMOTE_ADDR'].'") AND session_id="'.session_id().'" AND codigo_producto='.$contenedor['codigo_producto'].' AND (DATE_SUB(NOW(),INTERVAL 1 HOUR) < `fecha`)'))
    {
        $c = sprintf('INSERT INTO %s (ip,session_id,codigo_producto,fecha,referencia) VALUES (INET_ATON("%s"),"%s","%s",NOW(),"%s")',db_prefijo.'visita',$_SERVER['REMOTE_ADDR'],session_id(),$contenedor['codigo_producto'],db_codex(@$_SERVER['HTTP_REFERER']));
        db_consultar($c);
    }
    echo JS_onload('
    $(".variedad").click(function(){$("#imagen_contenedor").attr("src",$(this).attr("rel"));});
    ');

function PROCESAR_CONTENEDOR()
{
    if ((isset($_POST['btn_agregar_variedad']) || isset($_POST['btn_editar_variedad']) || isset($_POST['btn_eliminar_variedad'])) || (isset($_POST['referencia']) && $_POST['referencia'] == 'variedades')) return;
    // Si no hay ninguna referencia ó la referencia es explicitamente nuestra
    if (!isset($_POST['referencia']) || $_POST['referencia'] == 'contenedor')
    {
        global $db_link, $contenedor;
        $_POST['PME_sys_sfn[0]']='0';
        $_POST['PME_sys_fl']='0';
        $_POST['PME_sys_qfn']='';
        $_POST['PME_sys_fm']='0';
        $_POST['PME_sys_rec']=$contenedor['codigo_producto'];
        $_POST['PME_sys_operation']='Cambiar';
        $_POST['con_referencia']=true;
        require('PHP/gestor_contenedores.php');
    }
}

function PROCESAR_VARIEDADES()
{
    global $db_link, $contenedor;
    if (isset($_POST['btn_clonar_foto_variedad']))
    {
        $foto = db_obtener(db_prefijo.'producto_variedad', 'foto', 'codigo_variedad='.$_POST['codigo_variedad']);
        $c = 'UPDATE flores_producto_variedad SET foto="'.$foto.'" WHERE codigo_producto="'.$contenedor['codigo_producto'].'" AND codigo_variedad <> "'.$_POST['codigo_variedad'].'"';
        db_consultar($c);
        return;
    }

    // Determinar si necesitamos mostrar PME para agregar una variedad
    if ((isset($_POST['btn_agregar_variedad']) || isset($_POST['btn_editar_variedad']) || isset($_POST['btn_eliminar_variedad'])) || (isset($_POST['referencia']) && $_POST['referencia'] == 'variedades'))
    {
        $_POST['con_referencia']=true;
        $_POST['PME_sys_sfn[0]']='0';
        $_POST['PME_sys_fl']='0';
        $_POST['PME_sys_qfn']='';
        $_POST['PME_sys_fm']='0';
        if (isset($_POST['btn_agregar_variedad']))
        {
            $_POST['PME_sys_rec']='1';
            $_POST['PME_sys_operation']='Agregar';
            $_POST['f360_contenedor']=$contenedor['codigo_producto'];
        }

        if (isset($_POST['codigo_variedad']))
        {
            $_POST['PME_sys_rec']=$_POST['codigo_variedad'];
            if (isset($_POST['btn_editar_variedad']))
                $_POST['PME_sys_operation']='Cambiar';
            elseif (isset($_POST['btn_eliminar_variedad']))
                $_POST['PME_sys_operation']='Suprimir';
        }

        if (isset($_POST['PME_sys_saveadd']) || isset($_POST['PME_sys_savechange']) || isset($_POST['PME_sys_savedelete']))
        {
            ob_start();
        }
        require('PHP/gestor_variedades.php');
        if (isset($_POST['PME_sys_saveadd']) || isset($_POST['PME_sys_savechange']) || isset($_POST['PME_sys_savedelete']))
        {
            ob_end_clean();
            unset($_POST);
        }
    }
}

function PROCESAR_CATEGORIAS()
{
    global $contenedor;
    if (isset($_POST['btn_agregar_categoria']) && isset($_POST['cmb_agregar_categoria']) && is_numeric($_POST['cmb_agregar_categoria']))
    {
        $datos['codigo_producto'] = $contenedor['codigo_producto'];
        $datos['codigo_categoria'] = $_POST['cmb_agregar_categoria'];
        db_agregar_datos(db_prefijo.'productos_categoria',$datos);
        unset($datos);
    }

    if (isset($_POST['btn_agregar_categoria_v2']))
    {
        $join = (join('),('.$contenedor['codigo_producto'].',',array_values($_POST['chk_agregar_categoria'])));
        $c = sprintf('INSERT INTO %s (codigo_producto, codigo_categoria) VALUES (%s,%s)',db_prefijo.'productos_categoria',$contenedor['codigo_producto'],$join);
        //echo $c;
        db_consultar($c);
    }

    if (isset($_POST['btn_eliminar_categoria']) && isset($_POST['codigo_categoria']))
    {
        $c = sprintf("DELETE FROM %s WHERE codigo_categoria=%s AND codigo_producto=%s",db_prefijo.'productos_categoria',db_codex($_POST['codigo_categoria']), $contenedor['codigo_producto']);
        $r = db_consultar($c);
    }
}
?>
