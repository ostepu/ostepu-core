<?php


/**
 * @file index.php executes the LSamples component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2015
 */
 
require_once ( dirname( __FILE__ ) . '/LSamples.php' );
include_once ( '../../Assistants/CConfig.php' );

new LSamples();