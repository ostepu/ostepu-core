/**
 * @file GetCourseAttachments.sql
 * gets all course attachments from %Attachment table
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @result 
 * - A, the attachment data
 * - F, the attachment file
 */
 
select 
    concat('{$courseid}','_',A.A_id) as A_id,
    A.E_id,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash
from
    `Attachment{$pre}_{$courseid}` A
    left join `File` F ON F.F_id = A.F_id