<?php
/**
 * @file index.php executes the LExtension component on calling via rest api
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once ( dirname( __FILE__ ) . '/LExtension.php' );

new LExtension();