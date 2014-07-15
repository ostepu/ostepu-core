/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `ExerciseSheet` A limit 1;