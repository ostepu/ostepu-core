<?php
require_once ( dirname( __FILE__ ) . '/LFormProcessor.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LFormProcessor::getPrefix() . ',link,course');

// create a new instance of LFormProcessor class with the config data
if (!$com->used())
    new LFormProcessor($com->loadConfig());
?>