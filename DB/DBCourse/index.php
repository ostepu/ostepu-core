<?php


/**
 * @file index.php executes the DBCourse component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/DBCourse.php' );
include_once ( '../../Assistants/CConfig.php' );

new DBCourse();
?>