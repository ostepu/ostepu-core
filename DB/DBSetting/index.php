<?php


/**
 * @file index.php executes the DBSettings component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/DBSetting.php' );

 new DBSetting();