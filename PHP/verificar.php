<?php
//cv = codigo confirmación
//ce = correo electronico
if (!empty($_GET['cc']) && !empty($_GET['ce']) && strlen($_GET['cc']) == 40 && validcorreo($_GET['ce']))
{
    $c = sprintf('UPDATE %s SET confirmado=1 WHERE correo="%s" AND codigo_confirmacion="%s"',db_prefijo.'correo_oferta',db_codex($_GET['ce']),db_codex($_GET['cc']));
    db_consultar($c);
    if (db_afectados())
    {
        echo '<p>Su correo electrónico ha sido confirmado y activado.</p>';
        echo '<p>De ahora en adelante Ud. recibirá nuestras promociones especiales en su correo, Ud. podra remover su correo de esta lista de envío automática en el momento que desee</p>';
    }
    else
    {
        echo '<p>Error general al verificar su correo</p>';
    }
    echo '<br /><p><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
    return;
}
if (empty($_POST['ce']) || !isset($_POST['inscribir']))
    header('Location: '.PROY_URL);

if (!validcorreo($_POST['ce']))
{
    echo '<p>El correo ingresado es invalido.<br /><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
    return;
}

if (db_contar(db_prefijo.'correo_oferta','correo="'.$_POST['ce'].'"') > 0)
{
    echo '<p>El correo ingresado ya fue registrado.<br /><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
    return;
}

$CodigoConfirmacion = sha1(microtime());
$mensaje = '
<p>
Estimado usuario,

Segun nuestros registros Ud. solicito inscribirse voluntariamente y de forma totalmente gratuita a nuestra lista de correos, mediante la cual recibira informacion de promociones especiales unicamente relacionadas con <a href="http://flor360.com">'.PROY_NOMBRE.'</a> ['.PROY_NOMBRE.'].<br />
<br />
Recuerde que podra anular su suscripcion en todo momento sin ningun costo.<br />
<br />
Por favor haga clic en el enlace a continuacion si Ud. solicito la inscripcion:<br />
<a href="'.PROY_URL.'verificar?ce='.$_POST['ce'].'&cc='.$CodigoConfirmacion.'">'.PROY_URL.'verificar?ce='.$_POST['ce'].'&cc='.$CodigoConfirmacion.'</a><br />
<br />
Si tu cliente de correo no soporta enlaces, por favor copie y pegue el enlace en su barra de direcciones.<br />
<br />
Favor hacer caso omiso de este mensaje si Ud. nunca intento suscribirse a la lista de promociones especiales de Flor360.com<br />
<br />
Este mensaje fue enviado por el sistema de notificaciones de Flor360.com<br />
<center><img src="'.PROY_URL.'estatico/firma_correo.jpg"></center>
</p>
';

$c = sprintf('REPLACE INTO %s (correo,codigo_confirmacion,confirmado,fecha) VALUES("%s","%s",0,NOW())', db_prefijo.'correo_oferta', $_POST['ce'], $CodigoConfirmacion);
db_consultar($c);

if (db_afectados())
{
    correo($_POST['ce'],'Activación de Promociones especiales en Flor360.com - '.dechex(crc32(microtime())),$mensaje);
    echo '<p>Estimado usuario, se le ha enviado un correo especial con el motivo de asegurar la correcta recepción de nuestros correos y que de esta forma Ud. pueda recibir nuestras promociones especiales en el futuro . Por favor permitanos un tiempo de 1 a 5 minutos y revise su buzón de correo, siga las instrucciones del correo electrónico titulado "Activación de Promociones especiales en Flor360.com"</p>';
}
else
{
    echo '<p>Eror general</p>';
}
echo '<br /><p><a href="'.PROY_URL.'">Regresar a página principal</a></p>';
?>
