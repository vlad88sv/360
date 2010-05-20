<?php
require_once ("PHP/vital.php");
$c = sprintf('SELECT correo FROM %s WHERE enviado=0 LIMIT 1',db_prefijo.'bulk');
$r = db_consultar($c);
if (mysql_num_rows($r) > 0)
{
    $f = mysql_fetch_assoc($r);
    $c = sprintf('UPDATE %s SET enviado=1 WHERE correo="%s" LIMIT 1',db_prefijo.'bulk',$f['correo']);
    $r = db_consultar($c);
}

?>
