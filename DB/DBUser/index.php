<?php


/**
 * @file index.php executes the DBUser component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/DBUser.php' );

Logger::Log( 
            'begin DBUser',
            LogLevel::DEBUG
            );
            
new DBUser();

Logger::Log( 
            'end DBUser',
            LogLevel::DEBUG
            );
?>