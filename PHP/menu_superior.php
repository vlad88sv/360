<table>
    <tbody>
        <tr>
            <td id="logotipo">
                <a href="<?php echo PROY_URL ?>">
                    <img src="IMG/portada/logo.jpg" alt="Logotipo Flor360.com"/>
                </a>
		<img id="mariposa"src="IMG/stock/butterfly-mini.gif" />
            </td>
	    <td id="centro">
		<a href="<?php echo opcion('portada_enlace_imagen_superior_central',''); ?>"><img src="IMG/portada/superior_central.jpg" alt="<?php echo PROY_NOMBRE; ?>" title="<?php echo PROY_NOMBRE; ?>"/></a>
	    </td>
            <td id="telefonos">
                    <img src="IMG/stock/bandera_SLV.gif" alt="Bandera de El Salvador" /><br />
                    El Salvador
                    <p class="medio-oculto">
                        <span style="text-decoration: blink;">ESA: (503) 2243-6017</span><br />
                        <?php if (!S_iniciado()) { ?>
                        <a rel="nofollow" href="<?php echo PROY_URL ?>iniciar" title="Iniciar sesión">Iniciar sesión</a> / <a rel="nofollow" href="<?php echo PROY_URL ?>registrar" title="Registrarse">Registrarse</a>
                        <?php } else { ?>
                        <a rel="nofollow" href="<?php echo PROY_URL ?>finalizar" title="Cerrar sesión">Cerrar sesión</a>
                        <?php } ?>
                    </p>
            </td>
    </tbody>
</table>
<?php
echo '<div id="menu_superior">';
// Menues dinamicos

$c = 'SELECT fcat.`codigo_categoria`, fcat.`titulo`, fcat.`descripcion`, fmenu.`codigo_menu`, fmenu.`titulo` AS "menu" FROM `flores_categorias` AS fcat LEFT JOIN `flores_menu` fmenu ON fcat.codigo_menu = fmenu.codigo_menu  WHERE 1 ORDER BY fmenu.`posicion` ASC';
$r = db_consultar($c);

while ($f=mysql_fetch_assoc($r))
{
    $menu[$f['menu']][] = array('menu' => $f['menu'], 'codigo_menu' => $f['codigo_menu'], 'codigo_categoria' => $f['codigo_categoria'], 'titulo' => $f['titulo']);
}

echo '<ul id="nav" class="dropdown dropdown-horizontal">';
foreach($menu as $clave => $componentes)
{
    	echo '<li class="dir"><a href="'.PROY_URL.'categoria-superior-'.SEO($componentes[0]['menu'].'-'.$componentes[0]['codigo_menu']).'" title="'.$clave.'">'.$clave.'</a>';
        echo '<ul>';
	echo '<li><a href="'.PROY_URL.'categoria-superior-'.SEO($componentes[0]['menu'].'-'.$componentes[0]['codigo_menu']).'">Todas</a></li>';
        foreach($componentes as $item)
        {
            echo '<li><a href="'.PROY_URL.'categoria-'.SEO($item['titulo'].'-'.$item['codigo_categoria']).'" title="'.$item['titulo'].'">'.$item['titulo'].'</a></li>';
        }
        echo '</ul></li>';
}

/************* ADMINISTRACION ***************************/
if (in_array(_F_usuario_cache('nivel'),array(_N_administrador,_N_vendedor)))
{
echo '<li class="dir lidestacado"><a href="'.PROY_URL.'ventas" title="Obtener lista de compra-venta en espera">Ventas</a></li>';
}

if (_F_usuario_cache('nivel') == _N_administrador)
{
echo '<li class="dir"><a href="'.PROY_URL.'~administracion" title="administración">Admin</a>
			<ul>
                	<li><a href="'.PROY_URL.'~contenedores?agregar" title="Agregar contenedor">Nuevo contenedor</a></li>
                	<li><a href="'.PROY_URL.'~contenedores" title="Contenedores">Contenedores</a></li>
                	<li><a href="'.PROY_URL.'~variedades" title="Variedades">Variedades</a></li>
                	<li><a href="'.PROY_URL.'~accesorios" title="Accesorios">Accesorios</a></li>
                	<li><a href="'.PROY_URL.'~categorias" title="Categorias">Categorias</a></li>
                	<li><a href="'.PROY_URL.'~productos_categoria" title="Gestionar categorias de productos por contenedor">Cat. por contenedor</a></li>
                	<li><a href="'.PROY_URL.'~filtros" title="Gestionar filtros">Filtros</a></li>
                	<li><a href="'.PROY_URL.'~menu" title="Gestionar menú">Menú</a></li>
                	<li><a href="'.PROY_URL.'~usuarios" title="Gestionar artículos">Usuarios</a></li>
			<li><a href="'.PROY_URL.'~compras" title="Gestionar compras">Compras</a></li>
			<li><a href="'.PROY_URL.'~massmail" title="Envio masivo de correo">Correo masivo</a></li>
			<li><a href="'.PROY_URL.'~estadisticas" title="Estadísticas">Estadísticas</a></li>
			<li><a href="'.PROY_URL.'~administracion" title="Administracion global">Administración</a></li>
			</ul>
		</li>';
}

/************* AYUDA ***************************/
?>
<li class="dir"><a href="<?php echo PROY_URL; ?>ayuda?tema=nosotros" title="Ayuda">Ayuda</a>
<ul>
<li><a href="<?php echo PROY_URL; ?>ayuda?tema=nosotros" title="Quienes somos">Quienes somos</a></li>
<li><a href="<?php echo PROY_URL; ?>ayuda?tema=terminos_y_condiciones" title="Terminos y condiciones">Terminos y condiciones</a></li>
<li><a href="<?php echo PROY_URL; ?>ayuda?tema=PF" title="Preguntas Frecuentes">Preguntas frecuentes</a></li>
<li><a href="<?php echo PROY_URL; ?>ayuda?tema=como_pagar" title="Como pagar">¿Como pagar?</a></li>
</ul></li>

<li class="dir lidestacado"><a href="<?php echo PROY_URL; ?>contactanos" title="Contáctanos">Contáctanos</a>
<ul>
<li><a href="<? echo PROY_URL; ?>contactanos" title="Contáctanos">Contáctanos</a></li>
<li><a href="http://twitter.com/flor360" target="_blank" title="Flor360 en Twitter">...en Twitter!</a></li>
<li><a href="http://facebook.com/flor360" target="_blank" title="Flor360 en Facebook">...en Facebook!</a></li>
<li><a href="http://digg.com/d31IAuT" target="_blank" title="Flor360 en Digg">...en Digg!</a></li>
</ul>
</li>

<li class="dir"><a target="_blank" href="http://blog.flor360.com" title="Blog de Flor360.com">Blog.360</a></li>

<li class="dir busqueda" style="float:right">
<form action="<?php echo PROY_URL; ?>buscar" class="buscar">
<?php echo ui_input('busqueda',@$_GET['busqueda']);?> <input type="submit" value="Búscar" class="btnlnk btnlnk-mini" />
</form>
</li>

</ul>
</div>

<div rel="nofollow" style="position:fixed;bottom:0;right:0;">
    <script type="text/javascript" src="http://widgets.amung.us/colored.js"></script><script type="text/javascript">WAU_colored('75ferr1qqs05', '8cc63f000000')</script>
</div>