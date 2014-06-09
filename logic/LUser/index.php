<?php


/**
 * @file index.php executes the LUser component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/LUser.php' );

new LUser();
?>