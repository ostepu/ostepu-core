<?php
/**
 * @file GetAllCourses.sql
 * gets all courses from %Course table
 * @author  Till Uhlig
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
    ES.ES_id
from
    Course C
        left join
    ExerciseSheet ES ON C.C_id = ES.C_id