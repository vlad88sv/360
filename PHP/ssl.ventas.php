<?php
protegerme(false,array(_N_vendedor));
$arrJS[] = 'jquery.form';
$arrJS[] = 'jquery.ui.core';
$arrJS[] = 'jquery.ui.datepicker';
$arrJS[] = 'jquery.ui.datepicker-es';
$arrCSS[] = 'CSS/css/ui-lightness/jquery-ui-1.7.2.custom';
$GLOBAL_MOSTRAR_PIE = false;

$buffer = '';
$total = 0;
$WHERE  = '';

if(isset($_GET['fecha']))
    $WHERE = sprintf('AND DATE(fecha)="%s"',mysql_date($_GET['fecha']));

if(isset($_GET['pendientes']))
    $WHERE = 'AND fecha_entrega>=DATE(NOW())';

if(isset($_GET['fecha_entrega']))
    $WHERE = sprintf('AND fecha_entrega="%s"',mysql_date($_GET['fecha_entrega']));

if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_final']))
    $WHERE = sprintf('AND fecha BETWEEN "%s" AND "%s"',mysql_date($_GET['fecha_inicio']),mysql_date($_GET['fecha_final']));

if (isset($_GET['fecha_entrega_inicio']) && isset($_GET['fecha_entrega_final']))
    $WHERE = sprintf('AND fecha_entrega BETWEEN "%s" AND "%s"',mysql_date($_GET['fecha_entrega_inicio']),mysql_date($_GET['fecha_entrega_final']));

$c = sprintf('SELECT provar.foto, provar.descripcion AS "variedad_titulo", provar.receta, procon.codigo_producto, procon.titulo AS "contenedor_titulo",`codigo_compra`, `codigo_usuario`, `codigo_variedad`, `precio_grabado`, `precio_envio`, `tipo_t_credito`, `fecha_exp_t_credito`, `nombre_t_credito`, `pin_4_reverso_t_credito`, `correo_contacto`, `direccion_entrega`, `fecha`, `fecha_entrega`, DATE_FORMAT(fecha,"%%e de %%M de %%Y [%%r]") fecha_formato, DATE_FORMAT(fecha_entrega,"%%e de %%M de %%Y") fecha_entrega_formato, `telefono_destinatario`, `telefono_remitente`, `tarjeta_de`, `tarjeta_para`, `tarjeta_cuerpo`, `estado`, `estado_notas`, `usuario_notas`, `transaccion` FROM `flores_SSL_compra_contenedor` AS comcon LEFT JOIN flores_producto_variedad AS provar USING(codigo_variedad) LEFT JOIN flores_producto_contenedor AS procon USING(codigo_producto) WHERE 1 %s ORDER BY `fecha` DESC, `estado` DESC',$WHERE);
$r = db_consultar($c);
while ($f = mysql_fetch_assoc($r))
{
    $info_estado = '';
    if(in_array($f['estado'],array('cobrado','enviado')));
        $total += $f['precio_grabado'];
    $info_producto_foto =
    '<a target="_blank" href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']).'">'.
    '<img style="width:133px;height:200px" src="'.imagen_URL($f['foto'],133,200).'" /></a>'.
    '<p class="medio-oculto">
    <strong>Cod. Producto: </strong>'.$f['codigo_producto'].BR.
    '<strong>Nombre producto: </strong>'.BR.$f['contenedor_titulo'].BR.
    '<strong>Nombre variedad: </strong>'.BR.$f['variedad_titulo'].BR.
    '</p><hr /><p class="medio-oculto">'.
    '<strong>Precio:</strong> $'.number_format($f['precio_grabado'],2,'.',',') . BR.
    '<strong>Recargo envio:</strong> $'.number_format($f['precio_envio'],2,'.',',') . BR.
    '<strong>Total: </strong>'.'$'.number_format(($f['precio_grabado']+$f['precio_envio']),2,'.',',').
    '</p>';

    // Clasificado
    if (_F_usuario_cache('nivel') == _N_administrador)
    $info_estado .=
    '<strong>Facturación:</strong>'.BR.'<img src="'.PROY_URL.'imagen_SSL_'.$f['transaccion'] . '" />' . BR.
    '<strong>Nombre en tarjeta</strong><br />'.$f['nombre_t_credito'] . BR.
    '<strong>Tipo de tarjeta</strong>: '.$f['tipo_t_credito'] . '<hr />';

    $info_estado_admin =
    '<form class="ajax_estado" action="'.PROY_URL.'ajax" method="post">'.
    ui_input('codigo_compra',$f['codigo_compra'],'hidden').
    ui_input('pajax','modificar_orden','hidden').
    ui_combobox('estado', ui_array_a_opciones(array('nuevo' => 'Nuevo','aprobado' => 'Aprobado','cobrado' => 'Cobrado','transito' => 'En tránsito','enviado' => 'Enviado','error_pago' => 'Error en el pago','error_direccion' => 'Error en la dirección','error_flor360' => 'Error interno')),$f['estado'],'','width:70%').'<input type="submit" class="btnlnk btnlnk-mini" style="width:30%" value="Guardar" />'.BR.
    ui_textarea('estado_notas',$f['estado_notas'],'','width:98%;height:55px;').
    '</form>';

    $info_estado_admin .=
    '<hr />'.
    '<p class="medio-oculto">Datos del pedido</p>'.
    '<form style="display:inline" action="'.PROY_URL.'~compras" method="post" target="_blank">'.
    ui_input('PME_sys_sfn[0]','0','hidden').
    ui_input('PME_sys_fl','0','hidden').
    ui_input('PME_sys_qfn','','hidden').
    ui_input('PME_sys_fm','0','hidden').
    ui_input('PME_sys_rec',$f['codigo_compra'],'hidden').
    ui_input('PME_sys_operation','Cambiar','hidden').
    ui_input('PME_sys_navfmdown','0','hidden').
    '<input type="submit" class="btnlnk btnlnk-mini" style="width:30%" value="Editar" />'.
    '</form>'.
    '<form style="display:inline" action="'.PROY_URL.'~compras" method="post" target="_blank">'.
    ui_input('PME_sys_sfn[0]','0','hidden').
    ui_input('PME_sys_fl','0','hidden').
    ui_input('PME_sys_qfn','','hidden').
    ui_input('PME_sys_fm','0','hidden').
    ui_input('PME_sys_rec',$f['codigo_compra'],'hidden').
    ui_input('PME_sys_operation','Suprimir','hidden').
    ui_input('PME_sys_navfmdown','0','hidden').
    '<input type="submit" class="btnlnk btnlnk-mini" style="width:30%" value="Eliminar" />'.
    '</form>'.
    '<form style="display:inline" action="'.PROY_URL.'~impresion" method="get" target="_blank">'.
    ui_input('objetivo','pedido','hidden').
    ui_input('transaccion',$f['transaccion'],'hidden').
    '<input type="submit" class="btnlnk btnlnk-mini" style="width:30%" value="Imprimir" />'.
    '</form>'.
    '<p class="medio-oculto">Correos y notificaciones</p>'.
    '<form action="'.PROY_URL.'~notificacion" method="get" target="_blank">'.
    ui_input('transaccion',$f['transaccion'],'hidden').
    ui_combobox('plantilla', ui_array_a_opciones(array('datos_basicos' => 'Datos básicos', 'facturacion_correcta' => 'Facturación correcta', 'facturacion_incorrecta' => 'Facturación incorrecta', 'pedido_aclarar' => 'Aclarar datos de pedido', 'enviado' => 'Enviado', 'error_entrega' => 'Error de entrega')),$f['estado'],'','width:70%').'<input type="submit" class="btnlnk btnlnk-mini" style="width:30%" value="Enviar" />'.
    '</form>';

    $info_importante =
    '<table class="tabla-estandar" style="width:99%;height:55px;overflow:auto;">'.
    '<tr>'.
    '<td style="width:280px;">'.
    '<p class="medio-oculto">'.
    '<strong>Fecha entrega:</strong> '.$f['fecha_entrega_formato'] . BR.
    '<strong>Fecha pedido:</strong> '.$f['fecha_formato'] . BR.
    '<strong>Correo contacto</strong><br />'.$f['correo_contacto'].
    '</p>'.
    '</td>'.
    '<td style="width:114px;">'.
    '<p class="medio-oculto">'.
    '<strong>Tarjeta De</strong><br />'.$f['tarjeta_de'] . BR.
    '<strong>Telefono remitente</strong><br />'.$f['telefono_remitente'] . BR.
    '</p>'.
    '</td>'.
    '<td>'.
    '<p class="medio-oculto">'.
    '<strong>Tarjeta Para: </strong><br />'.$f['tarjeta_para'] . BR.
    '<strong>Telefono destinatario</strong><br />'.$f['telefono_destinatario'].
    '</p>'.
    '</td>'.
    '</tr>'.
    '</table>'.
    '<p class="medio-oculto">'.
    '<strong>Tarjeta Cuerpo</strong>'.BR.ui_textarea('',$f['tarjeta_cuerpo'],'','width:98%;height:55px;'). BR.
    '<strong>Dirección entrega</strong>'.BR.ui_textarea('',$f['direccion_entrega'],'','width:98%;height:55px;') . BR.
    '<strong>Notas del comprador</strong>'.BR.ui_textarea('',$f['usuario_notas'],'','width:98%;height:55px;') . BR.
    '<strong>Elementos para preparación</strong>'.BR.ui_textarea('',$f['receta'],'','width:98%;height:55px;').
    '</p>';
    $buffer .= sprintf('
    <div id="codigo_compra_'.$f['codigo_compra'].'" style="height:350px;clear:both;display:block;border:1px solid #AAA;margin-bottom:10px;page-break-after: always;">
    <div style="float:left;overflow:auto;width:133px;height:350px;border-right:1px solid #CCC;padding:0 0.1em;">
    %s
    </div>

    <div style="float:left;margin:0 5px;width:600px;height:350px;overflow:auto;">
    %s
    </div>

    <div style="float:right;margin:0 5px;width:200px;height:350px;overflow:auto;border-left:1px solid #CCC;padding-left:0.5em;">
    <p class="medio-oculto">%s</p>
    <div>%s</div>
    </div>

    </div>',$info_producto_foto,$info_importante,$info_estado,$info_estado_admin);
}
$total = number_format($total,2);
echo "<h1>Ventas de Flor360.com</h1>";
echo '<table id="tabla-ventas" class="tabla-estandar">';
echo '<tr><th>Ventas ($)<t/h><th>Arreglos (#)</th><th>Pedidos [solicitado]</th><th>Pedidos [entrega]</th>';
echo sprintf('<tr><td>$%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $total, mysql_num_rows($r),'<a href="'.PROY_URL.'ventas?fecha=-1 day">ayer</a> / <a href="'.PROY_URL.'ventas?fecha=now">ahora</a> / <form style="display:inline" method="get" action="'.PROY_URL_ACTUAL.'"><input name="fecha" type="text" class="datepicker" value="'.mysql_date().'" /><input type="submit" value="Ir" class="ir"/></form>','<a href="'.PROY_URL.'ventas">todos</a> / <a href="'.PROY_URL.'ventas?fecha_entrega=-1 day">ayer</a> / <a href="'.PROY_URL.'ventas?fecha_entrega=now">ahora</a> / <a href="'.PROY_URL.'ventas?fecha_entrega=+1 day">mañana</a> / <a href="'.PROY_URL.'ventas?pendientes">pendiente</a> / <form style="display:inline" method="get" action="'.PROY_URL_ACTUAL.'"><input name="fecha_entrega" class="datepicker" type="text" value="'.mysql_date().'" /><input type="submit" value="Ir" class="ir"/></form>' );
echo '</table>';
echo $buffer;
echo JS_onload('
    $.datepicker.regional["es"];
    $(".datepicker").datepicker({constrainInput: true, dateFormat : "yy-mm-dd", defaultDate: +0});
    ');
echo JS_onload('
var options = {dataType: "json"};
$(".ajax_estado").ajaxForm(options);
');
?>
