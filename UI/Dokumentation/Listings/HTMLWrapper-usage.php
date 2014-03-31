<?php
include_once 'HTMLWrapper.php';
include_once 'Template.php';

// fill template
$t1 = Template::WithTemplateFile('Listings/HTMLWrapper-template1.html');
$t2 = Template::WithTemplateFile('Listings/HTMLWrapper-template2.html');

// embed template in default HTML framework
$w = new HTMLWrapper($t1, $t2);
$w->set_config_file('Listings/HTMLWrapper-config.json');
$w->defineForm(basename(__FILE__), false, $t2);
$w->show();

?>
