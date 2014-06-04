<?php
require_once ( dirname( __FILE__ ) . '/DBFile.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBFile::getPrefix( ) );

// runs the DBFile
if ( !$com->used( ) )
    new DBFile( $com->loadConfig( ) );
?>