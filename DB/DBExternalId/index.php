<?php
require_once ( dirname( __FILE__ ) . '/DBExternalId.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBExternalId::getPrefix( ) );

// runs the DBExternalId
if ( !$com->used( ) )
    new DBExternalId( $com->loadConfig( ) );
?>