<?php
/**
 * @file index.php executes the CSystem component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
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