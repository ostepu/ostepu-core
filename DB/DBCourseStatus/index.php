<?php


/**
 * @file index.php executes the DBCourseStatus component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */

require_once ( dirname( __FILE__ ) . '/DBCourseStatus.php' );

new DBCourseStatus();