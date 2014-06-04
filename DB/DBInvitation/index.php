<?php
require_once ( dirname( __FILE__ ) . '/DBInvitation.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBInvitation::getPrefix( ) );

// runs the DBInvitation
if ( !$com->used( ) )
    new DBInvitation( $com->loadConfig( ) );
?>