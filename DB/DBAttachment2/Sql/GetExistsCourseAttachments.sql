/**
 * @file GetExistsCourseAttachments.sql
 * checks whether table exists
 */

select 
    count(1)
from
    `Attachment_{$pre}_{$courseid}` A limit 1;