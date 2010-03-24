<?php
protegerme(false,array(_N_vendedor));
$arrJS[] = 'jquery.form';
$arrHEAD[] = JS_onload("
var options = {
target: '#info_estado',
dataType: 'json',
success: function (responseText, statusText) {
\$('#codigo_compra_'+responseText.codigo_compra+' .estado').html(responseText.estado);
\$('#codigo_compra_'+responseText.codigo_compra+' .estado_notas').html(responseText.estado_notas);
}
};
$('.ajax_estado').ajaxForm(options);
");
$GLOBAL_MOSTRAR_PIE = false;

$buffer = '';
$total = 0;
$c = 'SELECT provar.foto, provar.descripcion AS "variedad_titulo", provar.receta, procon.codigo_producto, procon.titulo AS "contenedor_titulo",`codigo_compra`, `codigo_usuario`, `codigo_variedad`, `precio_grabado`, `tipo_t_credito`, `fecha_exp_t_credito`, `nombre_t_credito`, `pin_4_reverso_t_credito`, `correo_contacto`, `direccion_entrega`, `fecha_entrega`, `telefono_destinatario`, `telefono_remitente`, `tarjeta_de`, `tarjeta_para`, `tarjeta_cuerpo`, `estado`, `estado_notas`, `usuario_notas`, `transaccion` FROM `flores_SSL_compra_contenedor` AS comcon LEFT JOIN flores_producto_variedad AS provar USING(codigo_variedad) LEFT JOIN flores_producto_contenedor AS procon USING(codigo_producto) WHERE 1 ORDER BY `fecha` DESC, `estado` DESC';
$r = db_consultar($c);
while ($f = mysql_fetch_assoc($r))
{
    $total += $f['precio_grabado'];
    $info_producto_foto =
    '<a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['contenedor_titulo'].'-'.$f['codigo_producto']).'">'.
    '<img style="width:133px;height:200px" src="'.imagen_URL($f['foto'],133,200).'" /></a>';

    $info_producto =
    '<strong>Cod. Producto: </strong>'.$f['codigo_producto'].BR.BR.
    '<strong>Nombre producto: </strong>'.BR.$f['contenedor_titulo'].BR.BR.
    '<strong>Nombre variedad: </strong>'.BR.$f['variedad_titulo'].BR.BR.
    '<strong>Elementos para preparación</strong>'.BR.ui_textarea('',$f['receta']);

    $info_estado =
    'El estado de esta orden es: <strong><span class="estado">'.$f['estado'].'</span></strong>'.BR.BR.
    '<strong>Notas del estado</strong><br /><span style="display:inline" class="estado_notas">'.$f['estado_notas'].'</span>';

    $info_estado_admin =
    '<form class="ajax_estado" action="'.PROY_URL.'ajax" method="post">'.
    ui_input('codigo_compra',$f['codigo_compra'],'hidden').
    ui_input('pajax','modificar_orden','hidden').
    ui_combobox('estado', ui_array_a_opciones(array('nuevo' => 'Nuevo','aprobado' => 'Aprobado','cobrado' => 'Cobrado','transito' => 'En tránsito','enviado' => 'Enviado','error_pago' => 'Error en el pago','error_direccion' => 'Error en la dirección','error_flor360' => 'Error interno')),$f['estado']).BR.
    ui_textarea('estado_notas',$f['estado_notas'],'','width:100%;height:130px;').BR.
    '<input type="submit" class="btnlnk btnlnk-mini" value="Modificar orden" />'.
    '</form>';

    $info_importante =
    '<strong>Precio:</strong> $' . $f['precio_grabado'] . BR.
    '<strong>Fecha entrega:</strong> '.$f['fecha_entrega'] . BR.
    '<strong>Correo contacto:</strong> '.$f['correo_contacto'] . BR;

    // Clasificado
    if (_F_usuario_cache('nivel') == _N_administrador)
    $info_importante .=
    '<strong>Nombre en tarjeta:</strong> '.$f['nombre_t_credito'] . BR.
    '<strong>Tipo de tarjeta:</strong> '.$f['tipo_t_credito'] . BR.
    '<strong>CCV:</strong> '.$f['pin_4_reverso_t_credito'] . BR.
    '<strong>Fecha de expiración:</strong> '.$f['fecha_exp_t_credito'] . BR.
    '<strong>Número tarjeta de crédito:</strong>'.BR.'<img src="'.PROY_URL.'imagen_SSL_'.$f['transaccion'] . '" />' . BR. BR;

    $info_importante .=
    '<strong>Dirección entrega</strong>'.BR.ui_textarea('',$f['direccion_entrega']) . BR.
    '<strong>Tarjeta De: </strong> '.$f['tarjeta_de'] . BR.
    '<strong>Telefono remitente: </strong> '.$f['telefono_remitente'] . BR.
    '<strong>Tarjeta Para: </strong> '.$f['tarjeta_para'] . BR.
    '<strong>Telefono destinatario: </strong> '.$f['telefono_destinatario'] . BR.
    '<strong>Tarjeta Cuerpo</strong>'.BR.ui_textarea('',$f['tarjeta_cuerpo']). BR. BR.
    '<strong>Notas del comprador</strong>'.BR.ui_textarea('',$f['usuario_notas']);

    $buffer .= sprintf('
    <div id="codigo_compra_'.$f['codigo_compra'].'" style="height:200px;clear:both;display:block;">
    <div style="float:left;width:133px;">%s</div>

    <div style="float:left;margin:0 10px;width:230px;height:200px;overflow:auto;">
    <p class="medio-oculto">%s</p>
    </div>

    <div style="float:left;margin:0 10px;width:150px;">
    <p class="medio-oculto">%s</p>
    </div>

    <div style="float:left;margin:0 10px;width:210px;">
    <p class="medio-oculto">%s</p>
    </div>

    <div style="float:right;margin:0 10px;">
    <p class="medio-oculto">%s</p>
    </div>

    </div>',$info_producto_foto,$info_importante,$info_producto,$info_estado,$info_estado_admin);
}
$total = number_format($total,2);
echo "<h1>Total historico de ventas: \$$total + envíos</h1>";
echo $buffer;
?>
