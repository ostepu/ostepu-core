<?php
require_once ( dirname( __FILE__ ) . '/DBUser.php' );
include_once ( '../../Assistants/CConfig.php' );

Logger::Log( 
            'begin DBUser',
            LogLevel::DEBUG
            );

// runs the CConfig
$com = new CConfig( DBUser::getPrefix( ) );

// runs the DBUser
if ( !$com->used( ) )
    new DBUser( $com->loadConfig( ) );

Logger::Log( 
            'end DBUser',
            LogLevel::DEBUG
            );
?>