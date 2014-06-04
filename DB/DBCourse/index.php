<?php
require_once ( dirname( __FILE__ ) . '/DBCourse.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBCourse::getPrefix( ) );

// runs the DBCourse
if ( !$com->used( ) )
    new DBCourse( $com->loadConfig( ) );
?>