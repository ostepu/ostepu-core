<?php
require_once ( dirname( __FILE__ ) . '/DBProcess.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBProcess::getPrefix( ) . ',course,link' );

// runs the DBProcess
if ( !$com->used( ) )
    new DBProcess( $com );
?>