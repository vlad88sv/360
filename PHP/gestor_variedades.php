<?php
protegerme();
$GLOBAL_MOSTRAR_PIE = false;
set_time_limit(0);
ini_set('memory_limit', '128M');
ini_set('max_input_time', '6000');
ini_set('max_execution_time', '6000');
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
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
$opts['tb'] = db_prefijo.'producto_variedad';

// Name of field which is the unique key
$opts['key'] = 'codigo_variedad';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('codigo_variedad');

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

/* Get the user's default language and use it if possible or you can
   specify particular one you want to use. Refer to official documentation
   for list of available languages. */
$opts['language'] = 'ES-AR-UTF8';

$opts['fdd']['codigo_variedad'] = array(
  'name'     => 'Codigo variedad',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['codigo_producto'] = array(
  'name'     => 'Codigo contenedor',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);

if (isset($_POST['f360_contenedor']))
	$opts['fdd']['codigo_producto']['values'] = array($_POST['f360_contenedor']);

$opts['fdd']['descripcion'] = array(
  'name'     => 'Descripcion',
  'select'   => 'T',
  'maxlen'   => 250,
  'sort'     => true
);
$opts['fdd']['precio'] = array(
  'name'     => 'Precio',
  'select'   => 'T',
  'maxlen'   => 12,
  'sort'     => true
);
$opts['fdd']['receta'] = array(
  'name'     => 'Preparación',
  'select'   => 'T',
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['foto'] = array(
  'name'     => 'Fotografía',
  'select'   => 'T',
  'maxlen'   => -1,
  'sort'     => true,
  'input'    =>  'F'
);

$opts['extra'] = ui_input('referencia','variedades','hidden');

// Now important call to phpMyEdit
new phpMyEdit($opts);

?>