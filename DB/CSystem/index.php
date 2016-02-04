<?php


/**
 * @file index.php executes the CSystem component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/CSystem.php' );

Logger::Log( 
            'begin CSystem',
            LogLevel::DEBUG
            );
            
new CSystem();

Logger::Log( 
            'end CSystem',
            LogLevel::DEBUG
            );