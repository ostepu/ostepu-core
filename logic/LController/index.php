<?php
require_once ( dirname( __FILE__ ) . '/LController.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LController::getPrefix());

// create a new instance of LController class with the config data
if (!$com->used())
    new LController($com->loadConfig());
?>