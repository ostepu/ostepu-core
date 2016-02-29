<?php
/**
 * @file GetAllCourses.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBCourseGetAllCourses`;
CREATE PROCEDURE `DBCourseGetAllCourses` ()
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize,
    ES.ES_id
from
    Course C
        left join
    ExerciseSheet ES ON C.C_id = ES.C_id;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;