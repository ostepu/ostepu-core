/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Course` A limit 1;