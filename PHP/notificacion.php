<?php
require_once('PHP/ssl.comun.php');
$arrHEAD[] = '
<script type="text/javascript" src="JS/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
        plugins : \'style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,\'+
        \'searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras\',
        themes : \'advanced\',
        languages : \'es\',
        disk_cache : true,
        debug : false
});
</script>
<script type="text/javascript">
tinyMCE.init({
    language : "es",
    elements : "mensaje",
    theme : "advanced",
    mode : "exact",
    plugins : "safari,style,layer,table,advhr,advimage,advlink,media,paste,directionality,fullscreen,visualchars,nonbreaking,xhtmlxtras,template",
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,cleanup,code",
    theme_advanced_buttons2 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,advhr,|,ltr,rtl,|,fullscreen",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    button_tile_map : true,
});</script>
';
$GLOBAL_TIDY_BREAKS = true;

list($factura,$f) = SSL_COMPRA_FACTURA($_GET['transaccion']);

switch(@$_GET['plantilla'])
{
case 'facturacion_correcta':
    $buffer =
    '
    <p>Sr./Sra. '.$f['nombre_t_credito'].',</p>
    <p>Gracias por su compra en <strong>'.PROY_NOMBRE.'</strong>. Sus datos de facturacion:</p>
    <p>
    Referencia:<br />
    Autorizacion:<br />
    Factura:<br />
    </p>
    <p><strong>¡Esperamos atendenderle nuevamente!</strong></p>
    <hr />
    '.$factura.'
    <hr />
    <p style="color:#555">
    <i>
    Atención al cliente Flor360.com El Salvador<br />
    Teléfono (+503) 2243-6017<br />
    </i>
    </p>
    ';
    $titulo = 'Datos de facturacion de su compra ['.$f['transaccion'].']';
break;
case 'enviado':
    $buffer =
    '
    <p>Sr./Sra. '.$f['nombre_t_credito'].',</p>
    <p>Gracias por su compra en <strong>'.PROY_NOMBRE.'</strong>. Se le notifica que su pedido ha sido entregado.</p>
    <p><strong>¡Esperamos atendenderle nuevamente!</strong></p>
    <hr />
    '.$factura.'
    <hr />
    <p style="color:#555">
    <i>
    Atención al cliente Flor360.com El Salvador<br />
    Teléfono (+503) 2243-6017<br />
    </i>
    </p>
    ';
    $titulo = 'Su arreglo natural de flores ha sido entregado ['.$f['transaccion'].']';
break;
case 'datos_basicos':
    $buffer =
    '
    <p>Sr./Sra. '.$f['nombre_t_credito'].',</p>
    <p>Gracias por su compra en <strong>'.PROY_NOMBRE.'</strong>. Se le reenvian los datos básicos de su compra.</p>
    <p>Normalmente este correo se envía si Ud. solicitó algún cambio, por lo que se le pide amablemente que corrobore los datos nuevamente</p>
    <p><strong>¡Esperamos atendenderle nuevamente!</strong></p>
    <hr />
    '.$factura.'
    <hr />
    <p style="color:#555">
    <i>
    Atención al cliente Flor360.com El Salvador<br />
    Teléfono (+503) 2243-6017<br />
    </i>
    </p>
    ';
    $titulo = 'Datos básicos de su compra ['.$f['transaccion'].']';
break;
case 'error_entrega':
    list($factura,$f) = SSL_COMPRA_FACTURA($_GET['transaccion']);
    $buffer =
    '<p>Sr./Sra. '.$f['nombre_t_credito'].',</p>
    <p>Este correo se le envia por su compra en <strong>'.PROY_NOMBRE.'</strong>. Este correo es para informarle que hubo un error la entrega de su pedido.</p>
    <hr />
    '.$factura.'
    <hr />
    <p style="color:#555">
    <i>
    Atención al cliente Flor360.com El Salvador<br />
    Teléfono (+503) 2243-6017<br />
    </i>
    </p>
    ';
    $titulo = 'Error en su pedido ['.$f['transaccion'].']';
break;
}

if(isset($_POST['enviar']))
{
    correo($f['correo_contacto'] .', cartero@flor360.com', $titulo, $_POST['mensaje']);
    echo '<p>Enviado</p>';
    return;
}

$textarea = $buffer;

echo '<p>'.$f['correo_contacto'].' ~ '.$f['estado_notas'].'</p>';
echo '<form action="'.PROY_URL_ACTUAL_DINAMICA.'" method="post" >';
echo ui_input('enviar','Enviar','submit');
echo '<textarea id="mensaje" name="mensaje" style="width:100%;height:50em;">'.$textarea.'</textarea>';
echo '</form>';
?>
