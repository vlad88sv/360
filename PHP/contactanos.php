<?php
if (isset($_POST['enviar']))
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
?>
<div style="text-align:center;width:100%;margin:auto;">
<h1>Contacto</h1>
<p>
Si desea conocer mas sobre nuestros precios, variedad de flores, precios de entrega a domicilio, entoces por favor llene el siguiente formulario con su nombre, email, número de teléfono y su consulta y con gusto le responderemos su consulta en la mayor brevedad posible.
</p>
<p>
Recuerde que <?php echo PROY_NOMBRE; ?> [Telefono: 2243-6017] ofrece venta de arreglos florales personales y para eventos (bodas, quince años, confirmaciones, primeras comuniones, bautizos, fiestas y mas).
</p>
<p><strong>Recuerde que nuestros productos ya incluyen costos de envío - ¡algo que nadie mas te ofrece!</strong></p>
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
