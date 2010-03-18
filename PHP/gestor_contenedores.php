<?php
protegerme();
$GLOBAL_MOSTRAR_PIE = false;
set_time_limit(0);
/*
 * Si esta presente la opcion de editar variedad entonces
 * tenemos que mostrar los datos del contenedor + sus variedades actuales
 * ademas de la posibilidad de permitirle agregar nuevas variedades
 */


/*
 * IMPORTANT NOTE: This generated file contains only a subset of huge amount
 * of options that can be used with phpMyEdit. To get information about all
 * features offered by phpMyEdit, check official documentation. It is available
 * online and also for download on phpMyEdit project management page:
 *
 * http://platon.sk/projects/main_page.php?project_id=5
 *
 * This file was generated by:
 *
 *                    phpMyEdit version: 5.7.1
 *       phpMyEdit.class.php core class: 1.204
 *            phpMyEditSetup.php script: 1.50
 *              generating setup script: 1.50
 */

// MySQL host name, user name, password, database, and table
$opts['dbh'] = $db_link;
$opts['page_name'] = PROY_URL_ACTUAL;
$opts['tb'] = db_prefijo.'producto_contenedor';

// Name of field which is the unique key
$opts['key'] = 'codigo_producto';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('codigo_producto');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'ACPVDF';

// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

// Navigation style: B - buttons (default), T - text links, G - graphic links
// Buttons position: U - up, D - down (default)
$opts['navigation'] = 'DB';

// Display special page elements
$opts['display'] = array(
	'form'  => true,
	'query' => true,
	'sort'  => true,
	'time'  => false,
	'tabs'  => true
);

// Set default prefixes for variables
$opts['js']['prefix']               = 'PME_js_';
$opts['dhtml']['prefix']            = 'PME_dhtml_';
$opts['cgi']['prefix']['operation'] = 'PME_op_';
$opts['cgi']['prefix']['sys']       = 'PME_sys_';
$opts['cgi']['prefix']['data']      = 'PME_data_';

$opts['language'] = 'ES-AR-UTF8';

$opts['fdd']['codigo_producto'] = array(
  'name'     => 'Codigo producto',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['codigo_producto']['URL'] = PROY_URL.'vitrina--$value';

$opts['fdd']['titulo'] = array(
  'name'     => 'Titulo',
  'select'   => 'T',
  'maxlen'   => 250,
  'sort'     => true
);
$opts['fdd']['descripcion'] = array(
  'name'     => 'Descripcion',
  'select'   => 'T',
  'maxlen'   => -1,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['color'] = array(
  'name'     => 'Color',
  'select'   => 'T',
  'maxlen'   => 50,
  'values'   => unserialize(COLORES),
  'sort'     => true
);

$opts['extra'] = ui_input('referencia','contenedor','hidden');

if (isset($_GET['agregar']))
{
    $_POST['con_referencia']=true;
    $_POST['PME_sys_sfn[0]']='0';
    $_POST['PME_sys_fl']='0';
    $_POST['PME_sys_qfn']='';
    $_POST['PME_sys_fm']='0';
    $_POST['PME_sys_rec']='1';
    $_POST['PME_sys_operation']='Agregar';
}

// Now important call to phpMyEdit
new phpMyEdit($opts);

// si fue adicion lo mandamos a la nueva vitrina

if (isset($_POST['PME_sys_saveadd']))
{
    global $_LOCATION;
    $c = sprintf('SELECT MAX(codigo_producto) AS MAX_ID FROM %s',db_prefijo.'producto_contenedor');
    $r = db_consultar($c);
    $f = mysql_fetch_assoc($r);
    $_LOCATION = PROY_URL.'vitrina--'.$f['MAX_ID'];
}
?>