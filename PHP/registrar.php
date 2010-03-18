<?php
function CONTENIDO_REGISTRAR()
{
    if (S_iniciado())
    {
        header("location: ./");
        return;
    }
    if (isset($_POST['registrar_proceder']))
    {
        $flag_registroExitoso=true;
        if (!empty($_POST['registrar_campo_correo']))
        {
            if (!validcorreo($_POST['registrar_campo_correo']))
            {
                echo mensaje ("Este correo electrónico no es válido, por favor revise que este escrito correctamente o escoja otro e intente de nuevo",_M_ERROR);
                $flag_registroExitoso=false;
            }
            if (_F_usuario_existe($_POST['registrar_campo_correo'],"correo"))
            {
                echo mensaje ("Este correo electrónico ya existe en el sistema, por favor escoja otro e intente de nuevo",_M_ERROR);
                $flag_registroExitoso=false;
            }
                $datos['correo'] = $_POST['registrar_campo_correo'];
        }
        else
        {
            echo mensaje ("Por favor ingrese su correo e intente de nuevo",_M_ERROR);
            $flag_registroExitoso=false;
        }

        if (!empty($_POST['registrar_campo_nombre_completo']))
        {
            $datos['nombre_completo'] = trim($_POST['registrar_campo_nombre_completo']);
        }
        else
        {
            echo mensaje ("Por favor ingrese su nombre completo e intente de nuevo",_M_ERROR);
            $flag_registroExitoso=false;
        }

        if (!empty($_POST['registrar_campo_clave']) && !empty($_POST['registrar_campo_clave_2']))
        {
            //Contraseñas iguales?
            if (trim($_POST['registrar_campo_clave']) == trim($_POST['registrar_campo_clave_2']))
            {
                //Tamaño adecuado?
                if(strlen($_POST['registrar_campo_clave']) >= 6 && strlen($_POST['registrar_campo_clave']) <= 100)
                {
                    $datos['clave'] = sha1(trim($_POST['registrar_campo_clave']));
                }
                else
                {
                    echo mensaje ("La contraseña debe tener mas de 6 caracteres",_M_ERROR);
                    $flag_registroExitoso=false;
                }
            }
            else
            {
                echo mensaje ("Las contraseñas no coinciden, por favor ingrese su contraseña e intente de nuevo",_M_ERROR);
                $flag_registroExitoso=false;
            }
        }
        else
        {
            echo mensaje ("Por favor ingrese su contraseña e intente de nuevo",_M_ERROR);
            $flag_registroExitoso=false;
        }

        if (empty($_POST['registrar_campo_telefono']))
        {
            echo mensaje ("Por favor ingrese su número telefonico e intente de nuevo",_M_ERROR);
            $flag_registroExitoso=false;
        }
        $datos['telefono'] = $_POST['registrar_campo_telefono'];

        if ($flag_registroExitoso)
        {
            $datos["nivel"] = _N_usuario;
            $datos["ultimo_acceso"] = mysql_datetime();
            $datos["registro"]= mysql_datetime();
            db_agregar_datos(db_prefijo.'usuarios',$datos);
            echo Mensaje('¡Su solicitud de registro ha sido procesada!.');
            echo '<p>Puede probar su nueva cuenta ingresando al sistema con el formulario a continuación</p>';
            // Comprobamos que no haya ingresado al sistema
            if (!S_iniciado())
            {
                require_once("PHP/inicio.php");
                CONTENIDO_INICIAR_SESION();
                return;
            }
            correo($datos['correo'],"Su registro en ".PROY_NOMBRE." ha sido exitoso","Su registro de usuario  en ".PROY_NOMBRE." ha sido exitoso<hr><br />\n<h1>Datos registrados</h1><br />\nCorreo electrónico: <strong>".$datos['correo']."</strong><br />\nNombre completo: <strong>".$datos['nombre_completo']."</strong><br />\n<br /><br />Gracias por registarse.<br />".PROY_NOMBRE."<br />".PROY_URL);
            return;
        }
    }

$HEAD_titulo = PROY_NOMBRE . ' - Registrar cuenta';
echo "<p>¡Bienvenido!, ¿desea vivir la mejor experiencia en compra de flores en El Salvador?<br />Si ya posee una cuenta puede ". ui_href("","./iniciar","iniciar sesión").'</p>';
echo __PORQUE_TENER_CUENTA;
echo "<form action=\"registrar\" method=\"POST\">";
echo "<table>";
echo ui_tr(ui_td("<acronym title='Ud. ingresará a nuestro sistema usando esta dirección de correo electronico. Asegurese que la dirección exista, puesto que será necesaria en caso de que desee recuperar su contraseña.'>Correo electronico (e-mail)</acronym>") . ui_td(ui_input("registrar_campo_correo",_F_form_cache("registrar_campo_correo"))) . ui_td('<span id="registrar_respuesta_correo"></span>'));
echo ui_tr(ui_td("<acronym title='Este es el nombre que utilizaremos al contactarlo'>Nombre Completo</acronym>")  . ui_td(ui_input("registrar_campo_nombre_completo",_F_form_cache("registrar_campo_nombre_completo"))) . ui_td('<span id="registrar_respuesta_nombre_completo"></span>'));
echo ui_tr(ui_td("<acronym title='Le permitirá validar su identidad en nuestro sistema. Deberá ser mayor a 6 carácteres'>Contraseña</acronym>")      . ui_td(ui_input("registrar_campo_clave","","password")));
echo ui_tr(ui_td("<acronym title='Por favor ingrese nuevamente su contraseña (verificación)'>Contraseña (verificación)</acronym>")      . ui_td(ui_input("registrar_campo_clave_2","","password")));
echo ui_tr(ui_td("<acronym title='Número de contacto principal. Le llamaremos a este número si es necesario esclarecer datos sobre una venta'>Teléfono de contacto</acronym>")  . ui_td(ui_input("registrar_campo_telefono",_F_form_cache("registrar_campo_telefono"))));
echo "</table>";
echo ui_input("registrar_proceder", "Proceder", "submit")."<br />";
echo "</form>";
echo "<strong>Su correo electrónico, teléfono, dirección u otros datos no serán revelados al público ni vendidos a terceras personas.</strong>";
echo JS_onload('
$("#registrar_campo_correo").blur(function(){$("#registrar_respuesta_correo").load("./registro_correo_existe:"+$("#registrar_campo_correo").val());});
');
}
?>
