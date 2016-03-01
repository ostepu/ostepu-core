<?php
/**
 * @file index.php executes the CAbstract component on calling via rest api
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

require_once ( dirname( __FILE__ ) . '/CAbstract.php' );

new CAbstract();