<?php
require_once ( dirname( __FILE__ ) . '/LFile.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( LFile::getBaseDir( ) );

// runs the LFile
if ( !$com->used( ) )
    new LFile( $com->loadConfig( ) );
?>