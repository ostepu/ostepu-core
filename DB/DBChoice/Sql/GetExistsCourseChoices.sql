/**
 * @file GetExistsCourseChoices.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Choice{$preChoice}_{$courseid}` FO limit 1;