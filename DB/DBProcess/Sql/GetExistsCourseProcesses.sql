/**
 * @file GetExistsCourseProcess.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Process_{$courseid}` PRO limit 1;