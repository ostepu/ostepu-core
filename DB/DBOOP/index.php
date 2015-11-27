<?php


/**
 * @file index.php
 *
 * @author Till Uhlig
 * @date 2015
 */

require_once ( dirname( __FILE__ ) . '/DBOOP.php' );

Logger::Log( 
            'begin DBOOP',
            LogLevel::DEBUG
            );

// ruft die Beispielkomponente auf            
new DBOOP();

Logger::Log( 
            'end DBOOP',
            LogLevel::DEBUG
            );