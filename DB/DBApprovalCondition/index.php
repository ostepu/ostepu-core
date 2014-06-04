<?php
require_once ( dirname( __FILE__ ) . '/DBApprovalCondition.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBApprovalCondition::getPrefix( ) );

// runs the DBExerciseSheet
if ( !$com->used( ) )
    new DBApprovalCondition( $com->loadConfig( ) );
?>