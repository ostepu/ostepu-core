<?php
require_once ( dirname( __FILE__ ) . '/LForm.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LForm::getPrefix() . ',course,link');

// create a new instance of LForm class with the config data
if (!$com->used())
    new LForm($com->loadConfig());
?>