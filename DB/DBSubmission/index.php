<?php
require_once ( dirname( __FILE__ ) . '/DBSubmission.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBSubmission::getPrefix( ) );

// runs the DBSubmission
if ( !$com->used( ) )
    new DBSubmission( $com->loadConfig( ) );
?>