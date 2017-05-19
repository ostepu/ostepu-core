<?php
/**
 * @file index.php executes the DBRedirect component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

require_once ( dirname( __FILE__ ) . '/DBRedirect.php' );

Logger::Log(
            'begin DBRedirect',
            LogLevel::DEBUG
            );
            
 new DBRedirect();

Logger::Log(
            'end DBRedirect',
            LogLevel::DEBUG
            );