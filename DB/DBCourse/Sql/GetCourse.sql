<?php
/**
 * @file GetCourse.sql
 * gets an specified course from %Course table
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @result 
 * - C, the course data
 * - ES, the exercise sheet data
 */
?>
 
 select 
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize,
    ES.ES_id,
    S.*
from
    Course C
        left join
    ExerciseSheet ES ON C.C_id = ES.C_id
        left join 
    Setting_<?php echo $courseid; ?> S ON (1)
where
    C.C_id = '<?php echo $courseid; ?>'