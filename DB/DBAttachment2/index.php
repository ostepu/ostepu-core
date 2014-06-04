<?php
require_once ( dirname( __FILE__ ) . '/DBAttachment2.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBAttachment2::getPrefix( ) . ',course,link' );

// runs the DBAttachment2
if ( !$com->used( ) )
    new DBAttachment2( $com );
?>