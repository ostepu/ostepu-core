<?php
require_once ( dirname( __FILE__ ) . '/DBSession.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBSession::getPrefix( ) );

// runs the DBSession
if ( !$com->used( ) )
    new DBSession( $com->loadConfig( ) );
?>