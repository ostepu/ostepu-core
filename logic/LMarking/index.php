<?php
require_once ( dirname( __FILE__ ) . '/LMarking.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LMarking::getPrefix());

// create a new instance of LMarking class with the config data
if (!$com->used())
    new LMarking($com->loadConfig());
?>