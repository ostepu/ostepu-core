<?php
require_once ( dirname( __FILE__ ) . '/DBGroup.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBGroup::getPrefix( ) );

// runs the DBUser
if ( !$com->used( ) )
    new DBGroup( $com->loadConfig( ) );
?>