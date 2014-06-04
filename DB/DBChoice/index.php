<?php
require_once ( dirname( __FILE__ ) . '/DBChoice.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBChoice::getPrefix( ) . ',course,link' );

// runs the DBChoice
if ( !$com->used( ) )
    new DBChoice( $com );
?>