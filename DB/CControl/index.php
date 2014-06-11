<?php


/**
 * @file index.php executes the CControl component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/CControl.php' );

new CControl();
?>