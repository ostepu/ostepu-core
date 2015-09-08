<?php


/**
 * @file index.php executes the CGate component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/CGate.php' );

Logger::Log( 
            'begin CGate',
            LogLevel::DEBUG
            );
            
new CGate();

Logger::Log( 
            'end CGate',
            LogLevel::DEBUG
            );