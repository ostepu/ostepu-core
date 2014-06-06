<?php
require_once ( dirname( __FILE__ ) . '/LExercise.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LExercise::getPrefix());

// create a new instance of LExercise class with the config data
if (!$com->used())
    new LExercise($com->loadConfig());
?>