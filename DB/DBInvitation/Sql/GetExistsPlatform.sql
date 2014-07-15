/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Invitation` A limit 1;