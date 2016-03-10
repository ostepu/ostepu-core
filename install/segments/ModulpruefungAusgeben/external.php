<?php
function apache_module_exists($module)
{
    $res = getenv('HTTP_'.strtoupper($module))=='On'?TRUE:
        getenv('REDIRECT_HTTP_'.strtoupper($module))=='On'?true:FALSE;
    return $res;
}
$result = array();

$result['mod_php5'] = apache_module_exists('mod_php5');
$result['mod_rewrite'] = apache_module_exists('mod_rewrite');
$result['mod_deflate'] = apache_module_exists('mod_deflate');
$result['mod_headers(win)'] = apache_module_exists('mod_headers');
$result['mod_filter(win)'] = apache_module_exists('mod_filter');
$result['mod_expires(win)'] = apache_module_exists('mod_expires');
echo json_encode($result);