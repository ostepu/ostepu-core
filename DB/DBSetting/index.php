<?php
/**
 * @file index.php executes the DBSettings component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

require_once ( dirname( __FILE__ ) . '/DBSetting.php' );

Logger::Log(
            'begin DBSetting',
            LogLevel::DEBUG
            );

 new DBSetting();

Logger::Log(
            'end DBSetting',
            LogLevel::DEBUG
            );