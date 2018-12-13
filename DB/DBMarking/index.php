<?php
/**
 * @file index.php executes the DBMarking component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once ( dirname( __FILE__ ) . '/DBMarking.php' );

Logger::Log(
            'begin DBMarking',
            LogLevel::DEBUG
            );

new DBMarking();

Logger::Log(
            'end DBMarking',
            LogLevel::DEBUG
            );