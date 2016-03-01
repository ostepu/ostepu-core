<?php
/**
 * @file EditMemberRight.sql
 * updates an specified course status from %CourseStatus table
 * @author  Till Uhlig
 * @param int \$courseid a %Course identifier
 * @param int \$userid an %User identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE CourseStatus
SET <?php echo $values; ?>
WHERE C_id = '<?php echo $courseid; ?>' and U_id = '<?php echo $userid; ?>'