<?php


/**
 * @file index.php executes the DBQuery component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/DBQuery.php' );

new DBQuery();