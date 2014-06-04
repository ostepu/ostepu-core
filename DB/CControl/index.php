<?php
require_once ( dirname( __FILE__ ) . '/CControl.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( CControl::getPrefix( ) . ',link,definition' );

// runs the CControl
if ( !$com->used( ) )
    new CControl( $com->loadConfig( ) );
?>