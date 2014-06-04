<?php
require_once ( dirname( __FILE__ ) . '/DBExerciseSheet.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBExerciseSheet::getPrefix( ) );

// runs the DBExerciseSheet
if ( !$com->used( ) )
    new DBExerciseSheet( $com->loadConfig( ) );
?>