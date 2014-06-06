<?php
require_once ( dirname( __FILE__ ) . '/LFormPredecessor.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LFormPredecessor::getPrefix() . ',link,course');

// create a new instance of LFormPredecessor class with the config data
if (!$com->used())
    new LFormPredecessor($com->loadConfig());
?>