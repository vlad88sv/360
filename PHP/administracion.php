<?php
ADMINISTRACION_PROCESAR_PORCENTAJE_PRECIOS();
ADMINISTRACION_RESTABLECER_SISTEMA();
echo <<< HTML
<h1>Opciones globales de administracion</h1>
<p>En esta seccion se encuentran opciones generales de adminsitracion y modificacion de datos del sistema.</p>
HTML;

/* Opcion para modificacion global de precios */
echo '<hr />
<form action="'.PROY_URL_ACTUAL.'" method="POST" >
<p>Modificar <strong>todos los precios</strong> en base al siguiente porcentaje '.ui_input('txt_porcentaje_precio').'</p>
<p>Ejemplo: ingresar 100 sinfica incrementar todos los precios en en 100%.</p><p>Note que puede disminuir los precios poniendo valores negativos.</p>
<p style="color:#F00"><strong>¡Advertencia!</strong> esta operación no puede ser revertida!</p>'.
ui_input('btn_porcentaje_precio','Realizar cambio','submit','btnlnk').
'</form>';

/* Opcion para restablecer todo el sistema */
echo '<hr />
<form action="'.PROY_URL_ACTUAL.'" method="POST" >
<p>Borrar <strong>todos los datos del sistema</strong>  '.ui_input('chk_confirmar_restablecer_sistema','confirmado','checkbox').'</p>
<p style="color:#F00"><strong>¡Advertencia!</strong> esta operación no puede ser revertida!, los usuarios serán eliminados y se creará un nuevo usuario "administrador@localhost.com" con clave "administrador"</p>'.
ui_input('btn_restablecer_sistema','Realizar cambio','submit','btnlnk').
'</form>';

function ADMINISTRACION_PROCESAR_PORCENTAJE_PRECIOS()
{
    if (!(isset($_POST['btn_porcentaje_precio']) && isset($_POST['txt_porcentaje_precio']) && is_numeric($_POST['txt_porcentaje_precio'])))
        return;

    $c = sprintf('UPDATE %s SET precio=precio+(precio*(%s/100))',db_prefijo.'producto_variedad',$_POST['txt_porcentaje_precio']);
    db_consultar($c);
}

function ADMINISTRACION_RESTABLECER_SISTEMA()
{
    if (isset($_POST['chk_confirmar_restablecer_sistema']) && isset($_POST['btn_restablecer_sistema']))
    {
        $tablas = array('productos_categoria', 'producto_variedad', 'producto_contenedor','usuarios');
        foreach($tablas as $tabla)
            db_consultar(sprintf('TRUNCATE TABLE `%s`',db_prefijo.$tabla));

        unlinkRecursive('IMG/i/',false);
        mkdir('IMG/i/m/');
    }
}
?>
