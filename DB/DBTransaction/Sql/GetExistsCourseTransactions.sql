/**
 * @file GetExistsCourseTransactions.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Transaction{$name}_{$courseid}` T limit 1;