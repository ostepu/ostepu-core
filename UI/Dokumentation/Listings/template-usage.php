<?php
include_once 'Template.php';

$data = file_get_contents('Listings/template-json.json');
$data = json_decode($data);

$t = Template::WithTemplateFile('Listings/template-html.html');
$t->bind($data);

print $t;

?>
