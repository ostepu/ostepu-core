<?php
/**
 * @file DeleteCourse.sql
 * deletes a specified course from %Course table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$courseid a %Course identifier
 * @result -
 */
?>

DELETE FROM Course
WHERE
    C_id = '<?php echo $courseid; ?>'