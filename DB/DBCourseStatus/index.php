<?php
require_once ( dirname( __FILE__ ) . '/DBCourseStatus.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBCourseStatus::getPrefix( ) );

// runs the DBUser
if ( !$com->used( ) )
    new DBCourseStatus( $com->loadConfig( ) );
?>