<?php
protegerme();

if(!isset($_GET['objetivo']))
    exit;

$GLOBAL_IMPRESION = true;
switch($_GET['objetivo'])
{
    case 'pedido':
        IMPRIMIR_pedido();
        break;
}


function IMPRIMIR_pedido()
{
    if(!isset($_GET['transaccion']))
        die();

    $c = sprintf('SELECT provar.foto, provar.descripcion AS "variedad_titulo", provar.receta, procon.codigo_producto, procon.titulo AS "contenedor_titulo",`codigo_compra`, `codigo_usuario`, `codigo_variedad`, `precio_grabado`, `precio_envio`, `tipo_t_credito`, `fecha_exp_t_credito`, `nombre_t_credito`, `pin_4_reverso_t_credito`, `correo_contacto`, `direccion_entrega`, `fecha`, `fecha_entrega`, DATE_FORMAT(fecha,"%%e de %%M de %%Y [%%r]") fecha_formato, DATE_FORMAT(fecha_entrega,"%%e de %%M de %%Y") fecha_entrega_formato, `telefono_destinatario`, `telefono_remitente`, `tarjeta_de`, `tarjeta_para`, `tarjeta_cuerpo`, `estado`, `estado_notas`, `usuario_notas`, `transaccion` FROM `flores_SSL_compra_contenedor` AS comcon LEFT JOIN flores_producto_variedad AS provar USING(codigo_variedad) LEFT JOIN flores_producto_contenedor AS procon USING(codigo_producto) WHERE transaccion="%s" ORDER BY `fecha` DESC, `estado` DESC',db_codex($_GET['transaccion']));
    $r = db_consultar($c);
    $f = mysql_fetch_assoc($r);

    $buffer = '';

    $info_producto_foto =
    '<a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']).'">'.
    '<img style="width:133px;height:200px" src="'.imagen_URL($f['foto'],133,200).'" /></a>'.
    '<p class="medio-oculto">
    <strong>Cod. Producto: </strong>'.$f['codigo_producto'].BR.
    '<strong>Nombre producto</strong>'.BR.$f['contenedor_titulo'].BR.
    '<strong>Nombre variedad</strong>'.BR.$f['variedad_titulo'].BR.
    '</p>';

    $info_importante =
    '<table class="tabla-estandar" style="height:55px;width:99%">'.
    '<tr>'.
    '<td>'.
    '<p class="medio-oculto">'.
    '<strong>Fecha entrega:</strong><br />'.$f['fecha_entrega_formato'] . BR.
    '<strong>Fecha pedido:</strong><br />'.$f['fecha_formato'] . BR.
    '<strong>Correo contacto</strong><br />'.$f['correo_contacto'].
    '</p>'.
    '</td>'.
    '<td>'.
    '<p class="medio-oculto">'.
    '<strong>Tarjeta De</strong><br />'.$f['tarjeta_de'] . BR.
    '<strong>Telefono remitente</strong><br />'.$f['telefono_remitente'] . BR.
    '</p>'.
    '</td>'.
    '<td>'.
    '<p class="medio-oculto">'.
    '<strong>Tarjeta Para </strong><br />'.$f['tarjeta_para'] . BR.
    '<strong>Telefono destinatario</strong><br />'.$f['telefono_destinatario'].
    '</p>'.
    '</td>'.
    '</tr>'.
    '</table>'.
    '<p class="medio-oculto">'.
    '<strong>Tarjeta Cuerpo</strong>'.BR.ui_textarea('',$f['tarjeta_cuerpo'],'','width:98%;height:110px;'). BR.
    '<strong>Dirección entrega</strong>'.BR.ui_textarea('',$f['direccion_entrega'],'','width:98%;height:110px;') . BR.
    '<strong>Notas del comprador</strong>'.BR.ui_textarea('',$f['usuario_notas'],'','width:98%;height:110px;') . BR.
    '<strong>Elementos para preparación</strong>'.BR.ui_textarea('',$f['receta'],'','width:98%;height:55px;').
    '</p>';

    echo '<style>*{background-color:#FFF !important;color:#000 !important}</style>';

    echo sprintf('
    <table style="height:350px;">
    <tr>
    <td style="border-right:1px solid #CCC;padding:0 0.1em;vertical-align:top;">
    %s
    </td>

    <td style="width:800px;">
    %s
    </td>
    </tr>
    </table>
    <hr /><br />
    <center><img src="IMG/portada/logo.jpg" alt="Logotipo Flor360.com"/></center>
    <p>Yo <strong>'.$f['tarjeta_para'].'</strong>, firmo en constancia que he recibido un arreglo floral de <i>'.PROY_NOMBRE.'</i> el día <strong>'.strftime('%A %e de %B de %Y').'</strong>.</p>
    <br /><br />
    _______________________<br />
    <strong>'.$f['tarjeta_para'].'</strong>
    ',$info_producto_foto,$info_importante);
}
?>
