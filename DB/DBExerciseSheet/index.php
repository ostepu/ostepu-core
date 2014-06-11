<?php


/**
 * @file index.php executes the DBExerciseSheet component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/DBExerciseSheet.php' );

new DBExerciseSheet();
?>