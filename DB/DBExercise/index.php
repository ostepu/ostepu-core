<?php
require_once ( dirname( __FILE__ ) . '/DBExercise.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBExercise::getPrefix( ) );

// runs the DBExercise
if ( !$com->used( ) )
    new DBExercise( $com->loadConfig( ) );
?>