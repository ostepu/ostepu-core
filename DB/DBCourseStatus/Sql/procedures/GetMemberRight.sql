DROP PROCEDURE IF EXISTS `DBCourseStatusGetMemberRight`;
CREATE PROCEDURE `DBCourseStatusGetMemberRight` (IN courseid INT, IN userid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    U.U_id,
    U.U_username,
    U.U_firstName,
    U.U_lastName,
    U.U_email,
    U.U_title,
    U.U_flag,
    U.U_studentNumber,
    U.U_isSuperAdmin,
    U.U_comment,
    CS.CS_status,
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize,
    concat(",courseid,",'_',S.SET_id) as SET_id,
    S.SET_name,
    S.SET_state,
    S.SET_type
from

    CourseStatus CS 
        join
    Course C ON (CS.C_id = C.C_id)
 join
    User U
       ON (U.U_id = CS.U_id)
        left join 
    Setting_",courseid," S ON (1)
WHERE
    CS.U_id = '",userid,"' and CS.C_id = '",courseid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;
