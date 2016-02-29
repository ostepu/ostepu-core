<?php
/**
 * @file GetExistsCourseProcesses.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

/**
 * @file GetExistsCourseProcess.sql
 * checks whether table exists
 * @author Till Uhlig
 */
?>

show tables like 'Process<?php echo $pre; ?>_<?php echo $courseid; ?>';