<?php
/**
 * @file EditMemberRight.sql
 * updates an specified course status from %CourseStatus table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$courseid a %Course identifier
 * @param int \$userid an %User identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE CourseStatus
SET <?php echo $values; ?>
WHERE C_id = '<?php echo $courseid; ?>' and U_id = '<?php echo $userid; ?>'