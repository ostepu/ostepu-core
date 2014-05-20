/**
 * @file GetExistsCourseProcess.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Process{$pre}_{$courseid}` PRO limit 1;