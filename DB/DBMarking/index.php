<?php
require_once ( dirname( __FILE__ ) . '/DBMarking.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBMarking::getPrefix( ) );

// runs the DBMarking
if ( !$com->used( ) )
    new DBMarking( $com->loadConfig( ) );
?>