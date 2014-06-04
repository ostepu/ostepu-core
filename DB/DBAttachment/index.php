<?php
require_once ( dirname( __FILE__ ) . '/DBAttachment.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBAttachment::getPrefix( ) );

// runs the DBAttachment
if ( !$com->used( ) )
    new DBAttachment( $com->loadConfig( ) );
?>