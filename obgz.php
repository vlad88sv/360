<?php
ob_start("ob_gzhandler");
if (preg_match('/^.*\.css$/', $_GET['archivo']))
    $mime = 'text/css';
else
    $mime = 'text/javascript';
header("Content-type: $mime");
readfile($_GET['archivo']);
?>