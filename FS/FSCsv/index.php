<?php
/**
 * @file index.php executes the FSCsv component on calling via rest api
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
 
require_once ( dirname( __FILE__ ) . '/FSCsv.php' );

new FSCsv();