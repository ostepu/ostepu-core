<?php
/**
 * @file index.php executes the DBFile component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once ( dirname( __FILE__ ) . '/DBFile.php' );

Logger::Log(
            'begin DBFile',
            LogLevel::DEBUG
            );

new DBFile();

Logger::Log(
            'end DBFile',
            LogLevel::DEBUG
            );