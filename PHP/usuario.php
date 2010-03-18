<?php
$tablausuarios = db_prefijo.'usuarios';

function _F_usuario_existe($correo,$campo="correo"){
    global $tablausuarios;
    $nombre_completo = db_codex($correo);
    $resultado = db_consultar ("SELECT correo FROM $tablausuarios where $campo='$correo'");
    if ($resultado) {
        if ( mysql_num_rows($resultado) == 1 )
        {
            return true;
        }
    }
    return false;
}

function _F_usuario_datos($correo,$campo="correo"){
    global $tablausuarios;
    $c = "SELECT * FROM $tablausuarios WHERE $campo='correo'";
    $resultado = db_consultar ($c);
    return (mysql_num_rows($resultado) > 0) ? db_fila_a_array($resultado) : false;
}

function _F_usuario_agregar($datos){
    global $tablausuarios;
    if ( !_F_usuario_existe($datos['nombre_completo']) ){
        return db_agregar_datos ($tablausuarios, $datos);
    } else {
        return false;
    }
}

function _F_usuario_acceder($correo, $clave,$enlazar=true){
    global $tablausuarios;
    $correo = db_codex (trim($correo));
    $clave =db_codex (trim($clave));

    $c = "SELECT * FROM $tablausuarios WHERE LOWER(correo)=LOWER('$correo') AND clave=SHA1('$clave')";
    DEPURAR($c,0);
    $resultado = db_consultar ($c);
    if ($resultado) {
    $n_filas = mysql_num_rows ($resultado);
    if ( $n_filas == 1 ) {
        $_SESSION['autenticado'] = true;
        $_SESSION['cache_datos_nombre_completo'] = db_fila_a_array($resultado);
        $c = "UPDATE $tablausuarios SET ultimo_acceso=NOW() WHERE codigo_nombre_completo="._F_usuario_cache('codigo_nombre_completo');
        $resultado = db_consultar ($c);
        return 1;
    }
    } else {
        unset ($_SESSION['autenticado']);
        unset ($_SESSION['codigo_nombre_completo']);
        echo "Error general al autenticar!"."<br />";
        return 0;
    }

}

function _F_usuario_cache($campo){
    if ( isset($_SESSION) && array_key_exists('cache_datos_nombre_completo', $_SESSION) ) {
        if ( array_key_exists ($campo, $_SESSION['cache_datos_nombre_completo']) ) {
            return $_SESSION['cache_datos_nombre_completo'][$campo];
        }else{
            return NULL;
        }
    }else{
        return NULL;
    }
}

function _autenticado()
{
    return (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] = true);
}
?>
