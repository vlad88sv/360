<div style="margin:auto;width:790px;text-align:justify;">
<?php
if (isset($_POST['enviar']))
{
  
  if (strlen($_POST['mensaje']) < 5)
  {
    $error[] = 'Su consulta no parece válida.';
  }
  
  if (strlen($_POST['nombre'])  < 3 || preg_match('/^.*[^\w].*$/',$_POST['nombre']))
  {
    $error[] = 'El nombre ingresado no parece válido o es muy corto.';
  }
  
  if(!validcorreo($_POST['email']))
  {
    $error[] = 'Su correo electrónico no parece válido.';
  }
  
  if (isset($error) && count($error))
  {
    echo '<h1>Su consulta no pudo ser enviada porque se encontraron los siguientes errores</h1>';
    echo '<p style="color:#F00">'.implode ('</p><p style="color:#F00">',$error).'</p>';
  }
  else
  {
    $to      = 'Contactos Flor360.com <contacto@flor360.com>, Alejando Molina <a.molina@flor360.com>, Laura Cañas <l.canas@flor360.com>';
    $subject = 'Nueva consulta a '.PROY_NOMBRE.' - ' . dechex(crc32(microtime()));
    $message =
		  "La siguiente consulta ha sido recibida a travez de ".PROY_URL_ACTUAL."\n" .
		  "@La consulta es sobre: " .
		  $_POST['mensaje'] .
		  " \n@Telefono: " .
		  $_POST['tel'] .
		  " \n@EMAIL: " .
		  $_POST['email'] .
		  " \n@Nombre: " .
		  $_POST['nombre'] .
		  " \n\nNo responda a contacto@flor360.com.\n".
		  "En su lugar responda a " . $_POST['email'] . "\n";
    $headers = 'From: Flor360.com <contacto@flor360.com>' . "\r\n" .
    'Reply-To: '.$_POST['nombre'].' <'.$_POST['email'].'>' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    @mail($to, $subject, $message, $headers);
  
    $c = sprintf('INSERT INTO %s (id_consulta, nombre, telefono, correo, interes, fecha) VALUES (NULL, "%s", "%s", "%s", "%s", NOW())',db_prefijo.'consultas',db_codex($_POST['nombre']),db_codex($_POST['tel']),db_codex($_POST['email']),db_codex($_POST['mensaje']));
    @db_consultar($c);
    echo '<p>';
    echo '¡Muchas gracias por su consulta!<br />';
    echo 'Lo invitamos a seguir navegando en nuestro sitio web. <a href="'.PROY_URL.'">Ir a la página principal</a>.<br />';
    echo 'Recuerde que nuestro número telefonico es: 2243-6017<br />';
    echo '</p>';
    return;
  }
}
?>
<h1>Contacto</h1>
<p>
Conoce mas sobre nuestros precios, flores, entrega a domicilio, utilizando el formulario de contácto (mostrado a continuación) y su consulta con gusto será amablemente contestada en el menor tiempo posible.
</p>
<p>
Teléfonos <?php echo PROY_TELEFONO; ?>.
</p>
<p>
Recuerde que <strong><?php echo PROY_NOMBRE; ?></strong> ofrece venta de arreglos florales personales y para eventos (bodas, quince años, confirmaciones, primeras comuniones, bautizos, fiestas y mas).
</p>
<p><strong>Nuestros productos ya incluyen costos de envío - ¡algo que ninguna otra floristería le ofrece!</strong></p>
<hr />
<form action="<?php echo PROY_URL_ACTUAL; ?>" method="post">
<table style="margin:auto">
<tr><td><p>Su teléfono</p></td><td><input name="tel" value="" /></td></tr>
<tr><td><p>Su email</p></td><td><input name="email" value="" /></td></tr>
<tr><td><p>Su nombre</p></td><td><input name="nombre" value="" /></td></tr>
</table>
<p>Comentario o pregunta</p>
<textarea cols="100" rows="10" name="mensaje"></textarea><br />
<input type="submit" name="enviar" value="Enviar consulta" />
</form>
</div>