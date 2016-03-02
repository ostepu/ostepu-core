<?php
/**
 * @file index.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
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