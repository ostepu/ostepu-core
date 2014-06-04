<?php
require_once ( dirname( __FILE__ ) . '/LExerciseSheet.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LExerciseSheet::getPrefix());

// create a new instance of LExercisesheet class with the config data
if (!$com->used())
    new LExerciseSheet($com->loadConfig());
?>