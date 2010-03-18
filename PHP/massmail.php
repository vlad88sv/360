<?php
if(isset($_POST['enviar']))
{
    correo_x_interes($_POST['asunto'],$_POST['mensaje']);
}
?>
<form action="~massmail" method="post">
    <input type="text" name="mensaje" value="" />
    <textarea cols="50" rows="50" name="asunto"/></textarea>
    <input type="submit" name="enviar" value="Enviar" />
</form>