<?php
require_once ( dirname( __FILE__ ) . '/LCondition.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LCondition::getPrefix());

// create a new instance of LCondition class with the config data
if (!$com->used())
    new LCondition($com->loadConfig());
?>