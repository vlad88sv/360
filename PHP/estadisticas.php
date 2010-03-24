<?php
protegerme();
$c = 'SELECT var.foto AS foto, con.`titulo`,con.`codigo_producto`, (SELECT count(*) FROM `flores_visita` AS vis WHERE vis.codigo_producto=con.codigo_producto) AS vistas FROM `flores_producto_contenedor` AS con LEFT JOIN `flores_producto_variedad` AS var USING(codigo_producto) WHERE 1 GROUP BY var.codigo_producto ORDER BY vistas DESC';
$pmv = db_consultar($c);

$c = 'SELECT MIN(precio) AS min, FORMAT(AVG(precio),2) avg, MAX(precio) max FROM flores_producto_variedad WHERE precio > 0';
$epre = db_consultar($c);
$epreassoc = mysql_fetch_assoc($epre);

$c = 'SELECT flores_producto_contenedor.codigo_producto, titulo, flores_producto_contenedor.descripcion, foto, precio FROM flores_producto_contenedor LEFT JOIN flores_producto_variedad USING(codigo_producto) WHERE precio > 0 ORDER BY precio ASC LIMIT 1';
$emin = mysql_fetch_assoc(db_consultar($c));

$c = 'SELECT flores_producto_contenedor.codigo_producto, titulo, flores_producto_contenedor.descripcion, foto, precio FROM flores_producto_contenedor LEFT JOIN flores_producto_variedad USING(codigo_producto) WHERE precio > 0 ORDER BY precio DESC LIMIT 1';
$emax = mysql_fetch_assoc(db_consultar($c));

echo '<h1>Precios</h1>';
echo sprintf('El precio mas bajo en el sistema es: $%s, el precio promedio es: $%s, el mas alto es: $%s',$epreassoc['min'],$epreassoc['avg'],$epreassoc['max']);
echo '<hr />';
echo '<table style="text-align:center;width:100%;table-layout:fixed;">';
echo '<td>Arreglo mas barato:<br /><a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($emin['titulo'].'-'.$emin['codigo_producto']).'"><img src="'.PROY_URL.'imagen_133_200_'.$emin['foto'].'.jpg" /></a></td>';
echo '<td>Arreglo mas caro:<br /><a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($emax['titulo'].'-'.$emax['codigo_producto']).'"><img src="'.PROY_URL.'imagen_133_200_'.$emax['foto'].'.jpg" /></a></td>';
echo '</table>';
echo '<h1>Los productos mas vistos</h1>';
echo '<table class="tabla-estandar ancha">';
echo '<tr><th>Fotografia</th><th>Titulo del producto</th><th>No. Visitas</th></tr>';
while ($f = mysql_fetch_assoc($pmv))
	echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', '<img src="'.PROY_URL.'imagen_133_200_'.$f['foto'].'.jpg" />', '#'.$f['codigo_producto'].' ~ <a href="'.PROY_URL.'arreglos-florales-floristerias-en-el-salvador-'.SEO($f['titulo'].'-'.$f['codigo_producto']).'">'.$f['titulo'].'</a>', $f['vistas']);
echo '</table>';
?>
