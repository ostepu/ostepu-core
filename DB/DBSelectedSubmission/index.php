<?php
require_once ( dirname( __FILE__ ) . '/DBSelectedSubmission.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBSelectedSubmission::getPrefix( ) );

// runs the DBSelectedSubmission
if ( !$com->used( ) )
    new DBSelectedSubmission( $com->loadConfig( ) );
?>