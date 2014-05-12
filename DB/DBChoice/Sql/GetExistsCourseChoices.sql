/**
 * @file GetExistsCourseChoices.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Choice_{$courseid}` FO limit 1;