<?php
//$arrCSS[] = 'CSS/dropdown/themes/vimeo.com/helper';
$arrCSS[] = 'CSS/dropdown/dropdown';
//$arrCSS[] = 'CSS/dropdown/themes/vimeo.com/default.advanced';

if (_F_usuario_cache('nivel') == _N_administrador)
{
    echo ui_input('js_admin','Mostrar/Ocultar opciones de administración',"button");
    echo JS_onload('$(".admin360").hide();$("#js_admin").click(function () {$(".admin360").toggle();});');
}

if (!isset($_GET['peticion']))
{
    $arrJS[] = 'jquery.fav-1.0';

    $arrJS[] = 'jquery.bookmark.pack';
    $arrCSS[] = 'JS/jquery.bookmark';
    $arrHEAD[] = JS_onload("$.bookmark.setDefaults({icons: 'bookmarks.png'}); $('#bookmark').jFav(); $('#bookmarks').bookmark({title: 'YoMachete.com - Ventas en línea en El Salvador',url: 'http://yomachete.com',sites: ['delicious', 'twitter','digg', 'facebook', 'stumbleupon','google','yahoo','windows']});");
    require_once ('PHP/portada.php');
    return;
}

$arrJS[] = 'fancybox/jquery.easing.1.3';
//$arrJS[] = 'fancybox/jquery.fancybox-1.2.1.pack';
//$arrCSS[] = 'JS/fancybox/jquery.fancybox';
//$arrHEAD[] = JS_onload('$("a.fancybox").fancybox();');



switch ($_GET['peticion'])
{
    case 'iniciar':
        require_once("PHP/inicio.php");
        CONTENIDO_INICIAR_SESION();
    break;
    case 'finalizar':
        _F_sesion_cerrar();
    break;
    case 'buscar':
        require_once("PHP/buscar.php");
    break;
    case 'registrar':
        require_once("PHP/registrar.php");
        CONTENIDO_REGISTRAR();
        break;
    case 'categoria':
        require_once("PHP/categoria.php");
        break;
    case '~usuarios':
        require_once("PHP/gestor_usuarios.php");
        break;
    case '~contenedores':
        require_once("PHP/gestor_contenedores.php");
        break;
    case '~variedades':
        require_once("PHP/gestor_variedades.php");
        break;
    case '~categorias':
        require_once("PHP/gestor_categorias.php");
        break;
    case '~accesorios':
        require_once("PHP/gestor_accesorios.php");
        break;
    case '~productos_categoria':
        require_once("PHP/gestor_productos_categoria.php");
        break;
    case '~filtros':
        require_once("PHP/gestor_filtros.php");
        break;
    case '~menu':
        require_once("PHP/gestor_menu.php");
        break;
    case '~administracion':
        require_once("PHP/administracion.php");
        break;
    case '~compras':
        require_once("PHP/ssl.gestor_compras.php");
        break;
    case 'comprar':
        require_once("PHP/ssl.compras.php");
        break;
    case 'ventas':
        require_once("PHP/ssl.ventas.php");
        break;
    case 'vitrina':
        require_once('PHP/vitrina.php');
        break;
    case 'ayuda':
        require_once('PHP/ayuda.php');
        break;
    case 'editar':
        require_once('PHP/carchivo.php');
        break;
    case 'verificar':
        require_once('PHP/verificar.php');
        break;
    case 'contactanos':
        require_once('PHP/contactanos.php');
        break;
    case '~estadisticas':
        require_once('PHP/estadisticas.php');
        break;
    case '~massmail':
        require_once('PHP/massmail.php');
        break;
    case 'informacion':
        require_once('PHP/ssl.compras.php');
        break;
    case 'registro_usuarios_correo':
        if (isset($_GET['op']))
        {
            $correo=db_codex(trim($_GET['op']));
            if ($correo)
            {
                echo _F_usuario_existe($correo,'correo') ? 'este correo ya esta registrado' : '';
            }
        }
        break;
    default:
        echo 'Petición erronea: '. $_GET['peticion'] .'. Abortando';
}
?>