<?php
require_once ("PHP/vital.php");
if (empty($_POST['pajax']))
{
    @ob_end_clean();
    exit;
}

switch ($_POST['pajax'])
{
    case 'modificar_orden':
        AJAX_cambio_en_estado_de_orden();
        break;
}

function AJAX_cambio_en_estado_de_orden()
{
    protegerme(true);
    $DATOS['estado'] = $_POST['estado'];
    $DATOS['estado_notas'] = $_POST['estado_notas'];
    db_actualizar_datos(db_prefijo.'SSL_compra_contenedor',$DATOS,'codigo_compra="'.db_codex($_POST['codigo_compra']).'"');
    echo '{"codigo_compra":"'.$_POST['codigo_compra'].'","estado":"'.$_POST['estado'].'","estado_notas":"'.$_POST['estado_notas'].'"}';
}
?>
