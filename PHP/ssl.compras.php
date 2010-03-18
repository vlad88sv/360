﻿<?php
set_time_limit(0);
ini_set('memory_limit',         '128M');
ini_set('max_input_time',       '6000');
ini_set('max_execution_time',   '6000');
ini_set('upload_max_filesize',  '50M');
ini_set('post_max_size',        '50M');

if (isset($_GET['tipo']) && isset($_GET['pin']))
{
    switch ($_GET['tipo'])
    {
        case 'estado':
            $estado=db_obtener(db_prefijo.'SSL_compra_contenedor','estado','transaccion="'.db_codex($_GET['pin']).'"');
            if ($estado)
            {
                echo '<h1>Rastreo del estado de su pedido</h1>';
                echo sprintf('<p>Pedido <strong>%s</strong><br />Su pedido se encuentra en el siguiente estado: <strong>%s</strong></p>',$_GET['pin'],$estado);
                echo sprintf('<p>Si necesita mas información no dude en comunicarse con nosotros en cualquiera de las siguientes formas:<br/>
                             <ul><li>Llamandonos al <strong>2243-6017</strong> o al <strong>2531-4899</strong> [Sucursal Galerías Escalón]</li><li>Usando nuestro <a href="'.PROY_URL.'contactanos">formulario de contacto</a></li><li>Enviarnos un correo a <a href="mailto:informacion@flor360.com">informacion@flor360.com</a></li><li>Visitarnos en <strong>Centro Comercial Galerías Escalón, Nivel 3</strong></li></ul></p>');
                echo '<p>¿Necesita enviar otro regalo?, ¿le gustan nuestros arreglos?, entonces lo invitamos a <a href="'.PROY_URL.'">regresar a página principal</a>.</p>';
            }
            else
            {
                echo 'Pin erroneo';
                echo '<p><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
            }
            break;

        case 'factura':
            echo SSL_COMPRA_FACTURA($_GET['pin']);
            //ob_end_clean();
            //exit;
            break;

        default:
            echo '<p>Petición erronea</p>';

    }
    return;
}
if (empty($_POST['variedad']))
{
    echo 'Error de selección';
    echo '<p><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
    return;
}

if (!is_numeric($_POST['variedad']))
{
    echo '<p>Lo siento, hubo un error en la transaccion y no se puede continuar</p>';
    echo '<p><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
    return;
}

$variedad_sql = sprintf('SELECT procon.`codigo_producto`, procon.`titulo` AS "contenedor_titulo", procon.`descripcion` AS "contenedor_descripcion", procon.`vistas`, procon.`color`, provar.`codigo_variedad`, provar.`codigo_producto`, provar.`foto`, provar.`descripcion` AS "variedad_titulo", provar.`precio`, provar.`receta` FROM `%s` AS provar LEFT JOIN `%s` AS procon USING(`codigo_producto`) WHERE provar.codigo_variedad=%s LIMIT 1',db_prefijo.'producto_variedad',db_prefijo.'producto_contenedor', $_POST['variedad']);
$variedad_r = db_consultar($variedad_sql);

if (mysql_num_rows($variedad_r) == 0)
{
    echo '<p>Lo siento, hubo un error en la transaccion y no se puede continuar</p>';
    echo '<p><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
    return;
}

// Todo bien...
$variedad = mysql_fetch_assoc($variedad_r);

// Tratemos de procesar la compra...
$id_factura = SSL_COMPRA_PROCESAR();
if ($id_factura)
{
    SSL_MOSTRAR_FACTURA($id_factura);
    echo '<hr />';
    echo '<p><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
    return;
}

$arrJS[] = 'jquery.ui.core';
$arrJS[] = 'jquery.ui.datepicker';
$arrJS[] = 'jquery.ui.datepicker-es';
$arrCSS[] = 'CSS/css/ui-lightness/jquery-ui-1.7.2.custom';

$HEAD_titulo = PROY_URL.' - Modulo de compras para ' . $variedad['contenedor_titulo'];
echo '<h1>Modulo de compra - ¡Gracias por preferir a ' . PROY_NOMBRE . '!</h1>';

//le mostramos la variedad que escogio para que no haya engaños...
echo '<p class="info">Por favor revise que el resumen a continuación concuerde con el producto que Ud. desea comprar.</p>';
echo '<table id="compras-resumen" style="width:100%">';
echo '<tr><td id="compras-resumen-fotografia" rowspan="5"><img src="'.imagen_URL($variedad['foto'],0,200).'" /></td></tr>';
echo sprintf('<tr><td><strong>Producto:</strong> %s</td></tr>',$variedad['contenedor_titulo']);
echo sprintf('<tr><td><strong>Variedad:</strong> %s</td></tr>',$variedad['variedad_titulo']);
echo sprintf('<tr><td><strong>Precio:</strong> $%s</td></tr>',$variedad['precio']);
echo sprintf('<tr><td><strong>Descripción:</strong> %s</td></tr>',$variedad['contenedor_descripcion']);
echo '</table>';

echo '<form id="formulario-compra" autocomplete="off" action="'.PROY_URL_ACTUAL.'" method="POST">';

echo ui_input('transaccion',sha1(microtime()),'hidden');
echo ui_input('variedad',$variedad['codigo_variedad'],'hidden');

echo '<p class="info">Datos de entrega</p>';
echo '<table class="tabla-estandar">';
echo '<tr><th>Fecha de entrega</th><th>Teléfono del destinatario</th></tr>';
echo '<tr>
<td>Fecha '. ui_input('txt_fecha_entrega', @$_POST['txt_fecha_entrega']).'<p class="medio-oculto">La fecha se ingresa en el formato "día/mes/año". Haga clic en la caja de texto para utilizar el calendario virtual.</p></td>
<td>Teléfono '.ui_input('txt_telefono_destinatario', @$_POST['txt_telefono_destinatario']).'<p class="medio-oculto">Deje este campo en blanco si no desea que contactemos con la persona que recibirá el regalo.</p></td>
</tr>';
echo '<tr>';
echo '<th>Dirección de entrega</th><th>Otras especificaciones</th>';
echo '</tr>';
echo '<tr>';
echo '<td style="width:50%">' . ui_textarea('txt_direccion_entrega',@$_POST['txt_direccion_entrega'],'','width:100%').'<p class="medio-oculto">Datos básicos: municipio, colonia/poligono, calle y número de casa. Recuerde que por el momento le atendemos únicamente en el área metropolitana.</p></td>';
echo '<td style="width:50%">' . ui_textarea('txt_usuario_notas',@$_POST['txt_usuario_notas'],'','width:100%').'<p class="medio-oculto">Ej. horas en las que puede encontrarse la persona o instrucciones especiales como tocar fuerte, etc.</p></td>';
echo '</tr>';
echo '</table>';

echo '<p class="info">Personalice los detalles de su tarjeta</p>';
echo '<table class="tabla-estandar">';
echo '<tr><th colspan="2">Mensaje de la tarjeta</th></tr>';
echo '<tr><td colspan="2">'.ui_textarea('txt_tarjeta_cuerpo',@$_POST['txt_tarjeta_cuerpo'],'','width:100%') . '</td></tr>';
echo '<tr><th>De</th><th>Para</th></tr>';
echo '<td style="width:50%">Nombre remitente ' . ui_input('txt_tarjeta_de',@$_POST['txt_tarjeta_de']).'</td>';
echo '<td style="width:50%">Nombre destinatario ' . ui_input('txt_tarjeta_para',@$_POST['txt_tarjeta_para']).'</td>';
echo '</table>';

echo '<table class="tabla-estandar">';
echo '<p class="info">Ingrese los datos de facturación. Recuerde que esta bajo una conexión segura.</p>';
echo '<tr><th colspan="2">Su correo electronico</th><th colspan="2">Teléfono de contacto</th></tr>
<tr>
<td colspan="2">' . ui_input('txt_correo_contacto',@$_POST['txt_correo_contacto'],'','','width:100%').'<p class="medio-oculto"><strong>Su email</strong>, asegurese de que este activo por si necesitamos contactarlo.</p></td>
<td colspan="2">'.ui_input('txt_telefono_remitente',@$_POST['txt_telefono_remitente'],'','','width:100%').'<p class="medio-oculto">Su télefono es necesario para poder confirmar el pago</p></td>
</tr>';

echo '<tr><th>Número de tarjeta de crédito</th><th colspan="2">Nombre del titular</th></tr>';
echo '<tr>';
echo '<td>' . ui_input('txt_numero_t_credito'). ' <p class="medio-oculto">Favor ingresarlo sin espacios ni guiones</p></td>';
echo '<td colspan="2">' . ui_input('txt_nombre_t_credito',@$_POST['txt_nombre_t_credito']). ' <p class="medio-oculto"><strong>Su nombre</strong> tal como aparece en su tarjeta de crédito</p></td>';
echo '</tr>';

echo '<tr><th>Número de verificación CCV</th><th>Fecha expiración</th><th>Tipo tarjeta de crédito</th></tr>';
echo '<tr>';
echo '<td>' . ui_input('txt_ccv',@$_POST['txt_ccv']). ' <p class="medio-oculto">Identifique este número con las instrucciones mas adelante</p></td>';
echo '<td>'. ui_input('txt_fecha_expiracion',@$_POST['txt_fecha_expiracion']). ' <p class="medio-oculto">Formato MM/YY</p></td>';
echo '<td>' . ui_combobox('cmb_tipo_t_credito','<option value="visa">Visa</option><option value="mastercard">Mastercard</option>').'</td>';
echo '</table>';

echo '
<p>
<strong>¿Cuál es el número de verificación de la tarjeta de crédito o débito (CVV) y dónde aparece?</strong>
Para ofrecerle la máxima seguridad, le solicitamos que introduzca el número de verificación de su tarjeta de crédito o débito (CVV) antes de procesar el pago.<br />
<br />
Tal como se muestra a continuación, el número de verificación de la tarjeta (CVV) corresponde a los tres últimos dígitos que se encuentran impresos sobre la banda de firma, situada en el reverso de la tarjeta de crédito.<br />
<br />
CVV es un elemento de seguridad que permite tanto a Flor360.com como al proveedor de la tarjeta de crédito identificar al pasajero como el titular de la tarjeta y proporcionarle seguridad adicional para protegerlo contra fraudes.
<center><img src="estatico/cvv_4digits.gif" style="width:240px;height:115px;" /> <img src="estatico/cvv_16digits.gif" style="width:240px;height:115px;" /></center>
</p>
';


echo '<hr />';
echo '<p class="confirmacion">Al hacer clic en el botón "Comprar" acepto que ' . PROY_NOMBRE . ' cargue a mi cuenta de credito la cantidad exacta de $<strong>'.$variedad['precio'].'</strong> sin derecho a devolución en caso de que decida cancelar el pedido en un futuro.</p>';

echo '<center>'.ui_input('btn_comprar', 'Comprar', 'submit').ui_input('btn_cancelar', 'Cancelar', 'submit').'</center><br /><center>[recibira un recibo virtual imprimible]</center>';

echo '<p class="medio-oculto">' . PROY_NOMBRE . ' almacenará su información financiera de forma segura hasta por 6 meses, luego de este periodo todos su datos financieros serán eliminados de nuestro sistema.</p>';
echo '</form>';

global $GLOBAL_MOSTRAR_PIE;
$GLOBAL_MOSTRAR_PIE = false;

echo JS_onload('
    $.datepicker.regional["es"];
    $("#txt_fecha_entrega").datepicker({minDate: 0, constrainInput: true, dateFormat : "yy-mm-dd", defaultDate: +1});
    ');



/*****************************************************************************/
function SSL_COMPRA_PROCESAR()
{
    global $variedad;

    if (isset($_POST['btn_cancelar']))
    {
        if (isset($_POST['variedad']))
        {
            $c = 'SELECT titulo, descripcion FROM flores_producto_contenedor LEFT JOIN flores_producto_variedad USING (codigo_producto) WHERE codigo_variedad="'.db_codex($_POST['variedad']).'"';
            $r = mysql_fetch_assoc(db_consultar($c));
            $location = PROY_URL.'vitrina-'.SEO($f['titulo'].'-'.$f['codigo_producto']);
        }
        else
            $location = PROY_URL;

        header("Location: " . $location);
        ob_end_clean;
        exit;
    }

    if (!isset($_POST['btn_comprar']) || !isset($_POST['variedad']))
    {
        return false;
    }

    // Revisamos si ya envió la compra, no vaya a ser doble compra.
    if (db_contar(db_prefijo.'SSL_compra_contenedor', 'transaccion="'.db_codex($_POST['transaccion']).'"'))
    {
        header("Location: " . PROY_URL);
        exit;
    }

    // Verificamos que todos los datos sean válidos
    $ERRORES = array();

    //Medio validamos el número de la tarjeta

    if (!preg_match('/^\d{16}$/',$_POST['txt_numero_t_credito']))
    {
        $ERRORES[] = 'El numero de tarjeta de credito no parece valido, Ud. ingresó '.$_POST['txt_numero_t_credito'].' ('.strlen($_POST['txt_numero_t_credito']).' digitos), por favor verifiquelo.';
    }

    //Válidamos el tipo de tarjeta de credito
    if (!in_array($_POST['cmb_tipo_t_credito'],array('visa','mastercard')))
    {
        $ERRORES[] = 'Parece que de alguna manera escogió un tipo inválido de tarjeta de crédito.';
    }

    // Tratamos de ver si la direccion de entrega es valida
    if (strlen(preg_replace('[^\w]','',$_POST['txt_direccion_entrega'])) < 10)
    {
        $ERRORES[] = 'Por favor revise que la dirección de entrega sea correcta y suficimientemente detallada.';
    }    // Tratamos de ver si la direccion de entrega es valida

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$_POST['txt_fecha_entrega']))
    {
        $ERRORES[] = 'Por favor revise que la fecha de entrega sea en este formato: año-mes-dia.';
    }

    if (!preg_match('/^\d{2}\/\d{2}$/',$_POST['txt_fecha_expiracion']))
    {
        $ERRORES[] = 'Por favor revise que la fecha de expiración de la tarjeta de crédito sea en el formato MES/AÑO incluyendo la pleca (/).';
    }

    if (!preg_match('/^\d{3}$/',$_POST['txt_ccv']))
    {
        $ERRORES[] = 'Por favor revise que el número de verificación de la tarjeta de crédito sean tres (3) números. Sirvase de las instrucciones para encontrar este número en su tarjeta de crédito.';
    }

    if (strlen($_POST['txt_nombre_t_credito']) < 10)
    {
        $ERRORES[] = 'El nombre del acreedor de la tarjeta de crédito parece inválido';
    }

    if (!validcorreo($_POST['txt_correo_contacto']))
    {
        $ERRORES[] = 'El correo ingresado no parece valido, por favor compruebelo.';
    }

    if ($_POST['txt_numero_t_credito'] == str_repeat('0',16))
    {
        $ERRORES[] = 'Número de tarjeta de crédito inválido.';
    }
    
    if (count($ERRORES) > 0)
    {
        echo '<h1>Lo sentimos, hay errores en los datos ingresados</h1>';
        echo '<p>Hemos detectado los siguientes errores en los datos introducidos y no podremos procesar su compra a menos que sean corregidos:</p>';
        echo '<p class="error">'.join('</p><p class="error">',$ERRORES).'</p>';
        return;
    }

    // Encriptamos la tarjeta de credito
    $t_credito = db_codex(preg_replace('/[^\d]/','',trim($_POST['txt_numero_t_credito'])));
    $c = sprintf('SELECT AES_ENCRYPT("%s","%s") AS t_credito_AES',$t_credito,db__key_str);
    $r = db_consultar($c);
    $f = mysql_fetch_assoc($r);

    $DATOS['codigo_compra'] = '0';
    $DATOS['codigo_usuario'] = '0';
    $DATOS['codigo_variedad'] = $variedad['codigo_variedad'];
    $DATOS['precio_grabado'] = $variedad['precio'];
    $DATOS['n_credito'] = $f['t_credito_AES'];
    $DATOS['tipo_t_credito'] = $_POST['cmb_tipo_t_credito'];
    $DATOS['telefono_destinatario'] = $_POST['txt_telefono_destinatario'];
    $DATOS['telefono_remitente'] = $_POST['txt_telefono_remitente'];
    $DATOS['fecha_exp_t_credito'] = $_POST['txt_fecha_expiracion'];
    $DATOS['nombre_t_credito'] = $_POST['txt_nombre_t_credito'];
    $DATOS['pin_4_reverso_t_credito'] = $_POST['txt_ccv'];
    $DATOS['direccion_entrega'] = $_POST['txt_direccion_entrega'];
    $DATOS['fecha_entrega'] = $_POST['txt_fecha_entrega'];
    $DATOS['tarjeta_de'] = $_POST['txt_tarjeta_de'];
    $DATOS['tarjeta_para'] = $_POST['txt_tarjeta_para'];
    $DATOS['tarjeta_cuerpo'] = $_POST['txt_tarjeta_cuerpo'];
    $DATOS['usuario_notas'] = $_POST['txt_usuario_notas'];
    $DATOS['correo_contacto'] = $_POST['txt_correo_contacto'];
    $DATOS['estado'] = 'nuevo';
    $DATOS['transaccion'] = $_POST['transaccion'];
    $DATOS['fecha'] = mysql_datetime();
    
    if ($_POST['txt_numero_t_credito'] == str_repeat('1',16))
    {
        return '<p>ERROR</p>';
    }
    return db_agregar_datos(db_prefijo.'SSL_compra_contenedor',$DATOS);
}

/*****************************************************************************/
/*
Emite facturas virtuales para la compra.
$salida='enlinea'|'pdf'
*/
function SSL_COMPRA_FACTURA($transaccion,$salida='enlinea')
{
    $c = sprintf('SELECT procon.`codigo_producto`, procon.`titulo` AS "titulo_contenedor", provar.`descripcion` AS "titulo_variedad", provar.foto, comcon.`codigo_compra`, comcon.`codigo_usuario`, comcon.`codigo_variedad`, FORMAT(comcon.`precio_grabado`,2) AS precio_grabado, comcon.`direccion_entrega`, comcon.`fecha_entrega`, comcon.`tarjeta_de`, comcon.`tarjeta_para`, comcon.`tarjeta_cuerpo`, comcon.`usuario_notas`, comcon.`transaccion`, comcon.`fecha`, `estado` FROM `flores_SSL_compra_contenedor` AS comcon LEFT JOIN `flores_producto_variedad` AS provar USING(codigo_variedad) LEFT JOIN `flores_producto_contenedor` AS procon USING(codigo_producto)  WHERE transaccion="%s"',db_codex($transaccion));
    $r = db_consultar($c);

    if (!mysql_num_rows($r))
    {
        echo '<p>Lo sentimos, tal factura no existe</p>';
        return;
    }

    $f = mysql_fetch_assoc($r);

    $buffer = '';
    $buffer .= '<table style="width:100%">';
    $campo = array(
    'Factura' => $f['transaccion'],
    'F360' => $f['codigo_producto'].':'.$f['codigo_variedad'],
    'Producto' => $f['titulo_contenedor'],
    'Variedad' => $f['titulo_variedad'],
    'Precio' => '$'.$f['precio_grabado'],
    'Remitente' => $f['tarjeta_de'],
    'Destinatario' => $f['tarjeta_para'],
    'Enviar a' => $f['direccion_entrega'],
    );
    foreach($campo AS $clave => $valor)
        $buffer .= sprintf('<tr><td>%s</td><td style="font-weight:bold">%s</td></tr>',$clave, $valor);
    $buffer .= '</table>';

    switch($salida)
    {
        case 'enlinea':
            return $buffer;
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

/*****************************************************************************/
function SSL_MOSTRAR_FACTURA($id_factura)
{
    $transaccion=db_obtener(db_prefijo.'SSL_compra_contenedor','transaccion','codigo_compra="'.$id_factura.'"');
    $factura = SSL_COMPRA_FACTURA($transaccion);

    $to      = 'Contactos Flor360.com <contacto@flor360.com>, Alejando Molina <a.molina@flor360.com>, Laura Cañas <l.canas@flor360.com>';
    $subject = 'Nueva COMPRA en Flor360.com - ' . dechex(crc32(microtime()));
    $message =
                "Una venta ha sido realizada a travez de ".PROY_URL_ACTUAL."<br />\n" .
                $factura . "<hr />\n".
                "<br /><br />\n\nNo responda a contacto@flor360.com.<br />\n".
                'En su lugar visite <a href="' . PROY_URL . 'ventas">~ventas</a>'."<br />\n";
    @correo($to, $subject, $message);

    echo '<h1>Transaccion completada</h1>';
    echo '<p>¡Gracias por su compra!, el equipo de Flor360.com comenzara a elaborar su pedido con las flores mas frescas disponibles en este preciso momento.</p>';
    echo '<hr />';
    echo '<h2>Factura</h2>';
    echo $factura;    
    echo '<hr />';
    echo sprintf('<p>Puede consultar el estado de su orden desde la siguiente dirección Web:<br /> <input type="text" value="%s" width="100%" /></p>',PROY_URL.'informacion?tipo=estado&pin='.$transaccion);
    echo sprintf('<p>Su copia del recibo virtual se encuentra en la siguiente dirección web<br /> <input type="text" value="%s" width="100%" /></p>',PROY_URL.'informacion?tipo=factura&pin='.$transaccion);
    echo '<p>Por favor guarde las direcciones anteriores para poder consultarlas en un futuro o si desea cancelar su orden</p>';
    echo '<p class="medio-oculto"><strong>Nota:</strong> es posible que lo contactemos telefonicamente para aclarar la transaccion si algun dato es invalido</p>';
    return;
}
?>