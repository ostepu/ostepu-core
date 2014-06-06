<?php
require_once ( dirname( __FILE__ ) . '/LOOP.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LOOP::getPrefix() . ',link,course');

// create a new instance of LOOP class with the config data
if (!$com->used())
    new LOOP($com->loadConfig());
?>