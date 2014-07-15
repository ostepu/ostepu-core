/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `File` A limit 1;