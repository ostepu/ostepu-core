<?php
/**
 * @file index.php executes the CHelp component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

require_once ( dirname( __FILE__ ) . '/CHelp.php' );

Logger::Log(
            'begin CHelp',
            LogLevel::DEBUG
            );

new CHelp();

Logger::Log(
            'end CHelp',
            LogLevel::DEBUG
            );