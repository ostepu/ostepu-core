/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */

select 
    count(CO_id)
from 
    `Component` A, 
    `ComponentLinkage` B 
limit 1