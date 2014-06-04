<?php
require_once ( dirname( __FILE__ ) . '/LUser.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LUser::getPrefix());

// create a new instance of LUser class with the config data
if (!$com->used())
    new LUser($com->loadConfig());
?>