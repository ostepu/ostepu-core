<?php
/**
 * @file EditCourse.sql
 * updates a specified course from %Course table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 * @param int $courseid a %Course identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE Course
SET <?php echo $values; ?>
WHERE C_id = '<?php echo $courseid; ?>';