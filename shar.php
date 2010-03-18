<?php
$base = 'IMG/i/';
if(!$dh = @opendir($base))
{
    return;
}
while (false !== ($obj = readdir($dh)))
{
    if(!is_file($base.$obj) || $obj == '.' || $obj == '..')
    {
        continue;
    }

    $destino = $base.sha1(preg_replace('/([0-9]+)\.jpg/','$1',$obj));
    echo $base.$obj.' -> '.$destino.'<br>';
    rename ($base.$obj,$destino);
}

closedir($dh);
?>
