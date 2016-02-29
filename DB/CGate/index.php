<?php
/**
 * @file index.php executes the CGate component on calling via rest api
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

require_once ( dirname( __FILE__ ) . '/CGate.php' );

Logger::Log(
            'begin CGate',
            LogLevel::DEBUG
            );

new CGate();

Logger::Log(
            'end CGate',
            LogLevel::DEBUG
            );