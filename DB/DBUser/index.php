<?php
/**
 * @file index.php executes the DBUser component on calling via rest api
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once ( dirname( __FILE__ ) . '/DBUser.php' );

Logger::Log(
            'begin DBUser',
            LogLevel::DEBUG
            );

new DBUser();

Logger::Log(
            'end DBUser',
            LogLevel::DEBUG
            );