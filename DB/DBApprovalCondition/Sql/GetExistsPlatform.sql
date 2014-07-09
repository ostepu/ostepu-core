/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `ApprovalCondition` A limit 1;