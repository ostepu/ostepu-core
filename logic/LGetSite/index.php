<?php


/**
 * @file index.php executes the LGetSite component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/LGetSite.php' );

new LGetSite();