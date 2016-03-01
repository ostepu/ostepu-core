<?php
/**
 * @file GetExistsCourseTransactions.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 */

/**
 * @file GetExistsCourseTransactions.sql
 * checks whether table exists
 * @author Till Uhlig
 */
?>

show tables like 'Transaction<?php echo $name; ?>_<?php echo $courseid; ?>';