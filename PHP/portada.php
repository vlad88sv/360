<?php
/*
  Generar la portada
  * Mostrar la imagen de inicio.
  * categorias seleccionadas.
  * promociones.
*/
// PROCESAMIENTO
IF (_F_usuario_cache('nivel') == _N_administrador)
{

    if (isset($_FILES['txt_cambiar_imagen']) && $_FILES['txt_cambiar_imagen']['error'] == 0)
    {
        move_uploaded_file($_FILES['txt_cambiar_imagen']['tmp_name'],'IMG/portada/principal.jpg');
    }

    if (isset($_POST['btn_cambiar_enlace'])) {
        escribir_opcion('portada_enlace_imagen_principal',$_POST['txt_enlace_principal']);
    }

    if (isset($_FILES['txt_cambiar_imagen_superior_central']) && $_FILES['txt_cambiar_imagen_superior_central']['error'] == 0)
    {
        move_uploaded_file($_FILES['txt_cambiar_imagen_superior_central']['tmp_name'],'IMG/portada/superior_central.jpg');
    }

    if (isset($_POST['btn_cambiar_enlace_superior_central'])) {
        escribir_opcion('portada_enlace_imagen_superior_central',$_POST['txt_enlace_superior_central']);
    }

    if (isset($_POST['btn_cambiar_nFilas_inferior'])) {
        escribir_opcion('portada_nFilas_inferior',$_POST['txt_numero_filas_inferior']);
    }

    if (isset($_POST['btn_cambiar_contenedor_portada']) && isset($_POST['txt_nuevo_contenedor']) && isset($_POST['hdd_posicion']))
    {
        list($categoria, $contenedor) = split(',',$_POST['txt_nuevo_contenedor'],2);
        $c = sprintf('REPLACE INTO flores_opciones (campo,valor,subvalor) VALUES("%s","%s","%s")', 'portada_posicion_'.$_POST['hdd_posicion'],$categoria,$contenedor);
        $r = db_consultar($c);
    }

    if (isset($_POST['btn_cambiar_contenedor_top10']) && isset($_POST['txt_nuevo_contenedor']) && isset($_POST['hdd_posicion']))
    {
        list($categoria, $contenedor) = split(',',$_POST['txt_nuevo_contenedor'],2);
        if (isset($_POST['usar_filtro']))
        {
            $nombre_filtro = db_codex($_POST['cmb_filtro']);
        }
        $c = sprintf('REPLACE INTO flores_opciones (campo,valor,subvalor,subvalor2) VALUES("%s","%s","%s","%s")', 'portada_top10_posicion_'.$_POST['hdd_posicion'],$categoria,$contenedor,@$nombre_filtro);
        $r = db_consultar($c);
    }
}
/********************ELEMENTOS******************/
// Titulo de la categoria
// Contenedor (RAND) para cada categoria
// Variedad mas cara del contenedor seleccionado
// MAX_precio(obtener_variedades(obtener_contenedores_con_categoria(obtener_categorias)));

// SELECT DISTINCT codigo_categoria, codigo_producto FROM x WHERE codigo_categoria

// Seleccionar las categorias aleatoriamente (solo categorias en uso), no repetir mismo producto
//$c = sprintf('SELECT fvariedad.foto AS "foto_variedad", fvariedad.descripcion AS "titulo_variedad", fprocon.titulo AS "titulo_contenedor", fprocat.codigo_categoria, fprocat.codigo_producto, fcat.titulo AS "titulo_categoria" FROM %s fvariedad LEFT JOIN (%s AS fprocon LEFT JOIN (%s AS fprocat LEFT JOIN %s AS fcat ON fprocat.codigo_categoria=fcat.codigo_categoria) ON fprocat.codigo_producto = fprocon.codigo_producto) ON fvariedad.codigo_producto = fprocon.codigo_producto',db_prefijo.'producto_variedad',db_prefijo.'producto_contenedor',db_prefijo.'productos_categoria',db_prefijo.'categorias',opcion('portada_cnt_img_mostrar'));
//$r = db_consultar($c);
/**********************************************/

IF (_F_usuario_cache('nivel') == _N_administrador)
{
// OPCIONES DE ADMINISTRACIÓN --- edición de portada --- //
echo '<div class="admin360" style="display:block;width:100%;text-align:center">';
echo '<hr />';
echo '<form action="'.PROY_URL_ACTUAL.'" method="POST" enctype="multipart/form-data"/>';
echo '<h1>Opciones de administración</h1>';

echo '<h2>Cambio de imagen principal</h2>
<p>El tamaño ideal para la imagen principal es de 692px × 446px, formato JPG.</p>'.ui_input('txt_cambiar_imagen','','file').ui_input('btn_cambiar_imagen','Cambiar imagen','submit');
echo '<p>La imagen principal es un enlace para la siguiente dirección:<br />'.ui_input('txt_enlace_principal',opcion('portada_enlace_imagen_principal',''),'text','','width:80%').ui_input('btn_cambiar_enlace','Cambiar enlace','submit').'</p>';

echo '<h2>Cambio de imagen superior central</h2>
<p>El tamaño ideal para la imagen es de 370px x 80px, formato JPG.</p>'.ui_input('txt_cambiar_imagen_superior_central','','file').ui_input('btn_cambiar_imagen_superior_central','Cambiar imagen','submit');
echo '<p>La imagen superior central es un enlace para la siguiente dirección:<br />'.ui_input('txt_enlace_superior_central',opcion('portada_enlace_imagen_superior_central',''),'text','','width:80%').ui_input('btn_cambiar_enlace_superior_central','Cambiar enlace','submit').'</p>';

echo '<h2>Opciones parte inferior</h2>
<p>Cantidad de filas en la parte inferior: <input name="txt_numero_filas_inferior" type="text" value="'.opcion('portada_nFilas_inferior','1').'" /> '.ui_input('btn_cambiar_nFilas_inferior','Cambiar','submit').'</p>';

echo '</form>';
echo '</div>';
echo '<hr />';
}

$conpor = "select `fprocat`.`codigo_categoria` AS `codigo_categoria`,`fprocat`.`codigo_producto` AS `codigo_producto`,`op`.`campo` AS `campo` from (`flores_productos_categoria` `fprocat` left join `flores_opciones` `op` on(((`fprocat`.`codigo_categoria` = `op`.`valor`) and (`fprocat`.`codigo_producto` = `op`.`subvalor`)))) where (`op`.`campo` like _utf8'portada_posicion_%')";

$c = "SELECT conpor.codigo_producto, conpor.codigo_categoria, IF(procon.titulo='','sin titulo',procon.titulo) AS 'titulo_contenedor', procon.descripcion AS 'descripcion_contenedor', cat.titulo AS 'titulo_categoria', cat.descripcion AS 'descripcion_categoria', MAX(provar.precio), provar.foto FROM ($conpor) AS conpor LEFT JOIN flores_producto_contenedor AS procon USING(codigo_producto) LEFT JOIN flores_categorias cat USING (codigo_categoria) LEFT JOIN flores_producto_variedad provar USING (codigo_producto) GROUP BY provar.codigo_producto ORDER BY conpor.campo ASC";
$r = db_consultar($c);
$COL0=$COL1='';

if (mysql_num_rows($r))
    mysql_data_seek($r,0);

for($i=0;$i<4;$i++)
{
    $contenido = '';
    $contenido .= '<div class="portada-categoria">';

    if (mysql_num_rows($r) && $f = mysql_fetch_assoc($r))
    {
    $contenido .= '<a href="'.PROY_URL.'categoria-'.SEO($f['titulo_categoria'].'-'.$f['codigo_categoria']).'">';
    $contenido .= '<img class="bloque" title="'.$f['titulo_categoria'].'" alt="'.$f['titulo_categoria'].'" src="'.imagen_URL($f['foto'],133,200,'img0.').'" />';
    $contenido .= '</a><div class="titulo-categoria">'.$f['titulo_categoria'].'</div>';
    }

    $contenido .= '</div>';
    $contenido .= SI_ADMIN('<FORM class="admin360" action="'.PROY_URL_ACTUAL.'" method="POST">'.ui_input('hdd_posicion',$i,'hidden').ui_combobox("txt_nuevo_contenedor",flores_db_ui_obtener_categorias_y_contenedores_cmb(),$f['codigo_categoria'].','.$f['codigo_producto'],'','width:100%').ui_input('btn_cambiar_contenedor_portada','Cambiar','submit').'</form>');
    $var = 'COL'.($i%2);
    $$var .= $contenido;
}
$COL0 = preg_replace('/<\/div><div /','</div><div class="portada-divisor"></div><div ',$COL0,1);
$COL1 = preg_replace('/<\/div><div /','</div><div class="portada-divisor"></div><div ',$COL1,1);

$IMG_CENTRAL = '<a href="'.opcion('portada_enlace_imagen_principal','').'"><img title="Floristerias en El Salvador" alt="Floristerias en El Salvador" src="IMG/portada/principal.jpg" /></a>';


// Fila inferior
$conpor = "select `fprocat`.`codigo_categoria` AS `codigo_categoria`,`fprocat`.`codigo_producto` AS `codigo_producto`,`op`.`campo` AS `campo`,`op`.`subvalor2` AS `subvalor2` from (`flores_productos_categoria` `fprocat` left join `flores_opciones` `op` on(((`fprocat`.`codigo_categoria` = `op`.`valor`) and (`fprocat`.`codigo_producto` = `op`.`subvalor`)))) where (`op`.`campo` like 'portada_top10_posicion_%')";

$c = sprintf('SELECT CONCAT("$",(IF(MIN(pv.precio)=MAX(pv.precio),pv.precio,CONCAT(MIN(pv.precio), " - $",MAX(pv.precio))))) AS "precio_combinado", pv.foto AS "variedad_foto", IF(pc.titulo="","sin titulo",pc.titulo) AS "contenedor_titulo", pc.descripcion AS "contenedor_descripcion", pc.codigo_producto, cat.titulo AS "categoria_titulo", cat.codigo_categoria, conpor.subvalor2  AS "filtro", fil.descripcion_filtro FROM (%s) AS conpor LEFT JOIN flores_filtros AS fil ON (conpor.subvalor2 = fil.nombre_filtro) LEFT JOIN flores_producto_contenedor AS pc USING(codigo_producto) LEFT JOIN flores_categorias AS cat USING(codigo_categoria) LEFT JOIN flores_producto_variedad AS pv USING(codigo_producto) GROUP BY pv.codigo_producto ORDER BY right(conpor.campo,length(conpor.campo)-length("portada_top10_posicion_")) ASC',$conpor);
$r = db_consultar($c);

$nFilas = opcion('portada_nFilas_inferior',1);
$TOP10 = '<table id="top10">';
for($i=0;$i<$nFilas;$i++)
{
    $TOP10 .= '<tr>';
    for($j=0;$j<5;$j++)
    {
        $TOP10 .= '<td style="text-align:center;vertical-align:top;">';
        $TOP10 .= '<div class="categoria-elemento">';
        if($f = mysql_fetch_assoc($r))
        {
            if (empty($f['filtro']))
            {
                $TOP10 .= '<div class="titulo" style="font-size:1.4em;font-weight:bolder;">'.$f['categoria_titulo'].'</div>';

                $TOP10 .= '<a href="'.PROY_URL.'categoria-'.SEO($f['categoria_titulo'].'-'.$f['codigo_categoria']).'"><img title="'.$f['categoria_titulo'].'" alt="'.$f['categoria_titulo'].'" src="'.imagen_URL($f['variedad_foto'],0,200,'img0.').'" /></a>';
                $checked = '';
            }
            else
            {
            $TOP10 .= '<div class="titulo" style="font-size:1.4em;font-weight:bolder;">'.$f['descripcion_filtro'].'</div>';

                $TOP10 .= '<a href="'.PROY_URL.'categoria-'.$f['filtro'].'-especial.html"><img title="'.$f['categoria_titulo'].'" alt="'.$f['categoria_titulo'].'" src="'.imagen_URL($f['variedad_foto'],0,200,'img0.').'" /></a>';
                $checked = 'checked="checked"';
            }

            $TOP10 .= '<div class="precio">'.$f['precio_combinado'].'</div>';
        }

        $TOP10 .= SI_ADMIN('<FORM class="admin360" action="'.PROY_URL_ACTUAL.'" method="POST">Categoría<br />'.
        ui_input('hdd_posicion',($j+1)+($i*5),'hidden').
        ui_combobox("txt_nuevo_contenedor",flores_db_ui_obtener_categorias_y_contenedores_cmb(),$f['codigo_categoria'].','.$f['codigo_producto'],'','width:100%').
    '<input type="checkbox" '.$checked.' name="usar_filtro" /> Usar filtro<br />'.
    ui_combobox("cmb_filtro",db_ui_opciones('nombre_filtro','nombre_filtro',db_prefijo.'filtros'),$f['filtro'],'','width:100%').
    ui_input('btn_cambiar_contenedor_top10','Cambiar','submit').'</form>');
        $TOP10 .= '</div>';
        $TOP10 .= '</td>';
    }
    $TOP10 .= '</tr>';
}
$TOP10 .= '</table>';

// Mostremos las categorias activas
echo '<table id="portada-tabla">';
echo sprintf('<tr><td class="portada-td">%s</td><td class="portada-centro">%s</td><td class="portada-td">%s</td></tr>',$COL0,$IMG_CENTRAL,$COL1);
echo '</table>';

// Mostremos las otras 10 x_X
echo $TOP10;

// =) Google is our friend
/*
<h1>Flor360.com, la mejor de las floristerias de el Salvador!</h1>
<p>Gracias a nuestros clientes ahora somos <strong>la mejor de las floristerias de el Salvador</strong>.<br />¡haga clic en nuestro link y compruebe la veracidad!</p>
<iframe scrolling="no" src="http://www.google.com.sv/search?q=mejor+floristeria+en+el+salvador" width="960px" frameborder="0" height="300px">Tu navegador no soporte iframes</iframe>
*/
?>
