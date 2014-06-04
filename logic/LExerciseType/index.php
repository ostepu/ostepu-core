<?php
require_once ( dirname( __FILE__ ) . '/LExerciseType.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LExerciseType::getPrefix());

// create a new instance of LExerciseType class with the config data
if (!$com->used())
    new LExerciseType($com->loadConfig());
?>