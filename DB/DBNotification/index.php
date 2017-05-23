<?php
/**
 * @file index.php executes the DBNotification component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

require_once ( dirname( __FILE__ ) . '/DBNotification.php' );

Logger::Log(
            'begin DBNotification',
            LogLevel::DEBUG
            );

 new DBNotification();

Logger::Log(
            'end DBNotification',
            LogLevel::DEBUG
            );