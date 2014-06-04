<?php
require_once ( dirname( __FILE__ ) . '/DBExerciseType.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBExerciseType::getPrefix( ) );

// runs the DBExerciseSheet
if ( !$com->used( ) )
    new DBExerciseType( $com->loadConfig( ) );
?>