<?php
function CONTENIDO_INICIAR_SESION()
{

if (isset($_POST['iniciar_proceder']))
{
    ob_start();
    $ret = _F_usuario_acceder($_POST['iniciar_campo_correo'],$_POST['iniciar_campo_clave']);
    $buffer = ob_get_clean();
    if ($ret != 1)
    {
        echo mensaje ("Datos de acceso erroneos, por favor intente de nuevo",_M_ERROR);
        echo mensaje ($buffer,_M_INFO);
    }
}

if (S_iniciado())
{
    if (!empty($_POST['iniciar_retornar']))
    {
        header("location: ".$_POST['iniciar_retornar']);
    }
    else
    {
        header("location: ./");
    }
    return;
}

$HEAD_titulo = PROY_NOMBRE . ' - Iniciar sesion';

if (isset($_GET['ref']))
    $_POST['iniciar_retornar'] = $_GET['ref'];

echo "Estimado usuario, si no posee una cuenta y desea registrar una... ". ui_href("","./registrar","¡entonces registrese ahora!") . ", es gratis, fácil y rápido.<br />";
$retorno = empty($_POST['iniciar_retornar']) ? PROY_URL : $_POST['iniciar_retornar'];
echo "<form action=\"iniciar\" method=\"POST\">";
echo ui_input("iniciar_retornar", $retorno, "hidden");
echo "<table>";
echo ui_tr(ui_td("Correo electronico (e-mail)") . ui_td(ui_input("iniciar_campo_correo")));
echo ui_tr(ui_td("Constraseña") . ui_td(ui_input("iniciar_campo_clave","","password")));
echo "</table>";
echo ui_input("iniciar_proceder", "Iniciar sesión", "submit")."<br />";
echo "</form>";
}
?>
