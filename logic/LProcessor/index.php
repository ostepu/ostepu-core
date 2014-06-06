<?php
require_once ( dirname( __FILE__ ) . '/LProcessor.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LProcessor::getPrefix() . ',submission,course,link');

// create a new instance of LProcessor class with the config data
if (!$com->used())
    new LProcessor($com->loadConfig());
?>