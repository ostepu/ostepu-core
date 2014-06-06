<?php
require_once ( dirname( __FILE__ ) . '/LGroup.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LGroup::getPrefix());

// create a new instance of LUser class with the config data
if (!$com->used())
    new LGroup($com->loadConfig());
?>