<?php
require_once ( dirname( __FILE__ ) . '/DBExerciseFileType.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBExerciseFileType::getPrefix( ) );

// runs the DBExerciseSheet
if ( !$com->used( ) )
    new DBExerciseFileType( $com->loadConfig( ) );
?>