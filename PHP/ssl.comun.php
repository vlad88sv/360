<?php
/*****************************************************************************/
/*
Emite facturas virtuales para la compra.
$salida='enlinea'|'pdf'
*/
function SSL_COMPRA_FACTURA($transaccion,$salida='enlinea')
{
    $c = sprintf('SELECT procon.`codigo_producto`, procon.`titulo` AS "titulo_contenedor", provar.`descripcion` AS "titulo_variedad", provar.foto, comcon.`codigo_compra`, comcon.`codigo_usuario`, comcon.`codigo_variedad`, FORMAT(comcon.`precio_grabado`,2) AS precio_grabado, FORMAT(comcon.`precio_envio`,2) AS precio_envio, comcon.`direccion_entrega`, comcon.`fecha_entrega`, comcon.`tarjeta_de`, comcon.`tarjeta_para`, comcon.`tarjeta_cuerpo`, comcon.`usuario_notas`, comcon.`transaccion`, comcon.`fecha`, `estado`, `correo_contacto`, `telefono_remitente`, `usuario_notas`, `nombre_t_credito`,`estado_notas` FROM `flores_SSL_compra_contenedor` AS comcon LEFT JOIN `flores_producto_variedad` AS provar USING(codigo_variedad) LEFT JOIN `flores_producto_contenedor` AS procon USING(codigo_producto)  WHERE transaccion="%s"',db_codex($transaccion));
    $r = db_consultar($c);

    if (!mysql_num_rows($r))
    {
        echo '<p>Lo sentimos, tal factura no existe</p>';
        return;
    }

    $f = mysql_fetch_assoc($r);

    $buffer = '<style>';
    $buffer .= 'table {border-collapse:collapse;}';
    $buffer .= 'table th{border-top:thin solid #c0c0c0;border-left:thin solid #c0c0c0;border-right:thin solid #c0c0c0;background-color:#eee;}';
    $buffer .= 'table td{border-top:thin solid #c0c0c0;border:1px solid #c0c0c0;}';
    $buffer .= '</style>';
    $buffer .= '<table style="width:100%">';
    $campo = array(
    'Factura' => $f['transaccion'],
    'F360' => $f['codigo_producto'].':'.$f['codigo_variedad'],
    'Producto' => $f['titulo_contenedor'],
    'Variedad' => $f['titulo_variedad'],
    'Precio' => '$'.$f['precio_grabado'],
    'Recargo de envio' => '$'.$f['precio_envio'],
    'Total' => '$'.number_format(($f['precio_grabado']+$f['precio_envio']),2,'.',','),
    'Remitente' => $f['tarjeta_de'],
    'Destinatario' => $f['tarjeta_para'],
    'Tarjeta' => $f['tarjeta_cuerpo'],
    'Enviar a' => $f['direccion_entrega'],
    'Fecha pedido' => date('d/m/Y'),
    'Fecha de entrega' => date('d/m/Y',strtotime($f['fecha_entrega'])),
    'Correo contacto' => $f['correo_contacto'],
    'Teléfono remitente' => $f['telefono_remitente'],
    'Notas adicionales del comprador' => $f['usuario_notas'] ? $f['usuario_notas'] : '[No especificó nada en especial]'
    );
    foreach($campo AS $clave => $valor)
        $buffer .= sprintf('<tr><td>%s</td><td style="font-weight:bold">%s</td></tr>',$clave, $valor);
    $buffer .= '</table>';

    switch($salida)
    {
        case 'enlinea':
            return array($buffer,$f);
            break;
        case 'pdf':
            $buffer = '<html><body>'.$buffer.'</body></html>';
            require_once('PHP/dompdf/dompdf_config.inc.php');
            $dompdf = new DOMPDF();
            $dompdf->load_html($buffer);
            //$dompdf->render();
            //$dompdf->stream("factura-$transaccion.pdf");
    }
}
?>
