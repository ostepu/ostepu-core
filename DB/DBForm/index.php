<?php
require_once ( dirname( __FILE__ ) . '/DBForm.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBForm::getPrefix( ) . ',course,link' );

// runs the DBForm
if ( !$com->used( ) )
    new DBForm( $com->loadConfig( ) );
?>