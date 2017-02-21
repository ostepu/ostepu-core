<?php
/**
 * @file GetUser.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBUserGetUser`;
CREATE PROCEDURE `DBUserGetUser` (IN profile varchar(30), IN userid varchar(120))
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
    U.U_password,
    U.U_salt,
    U.U_failed_logins,
    U.U_externalId,
    U.U_studentNumber,
    U.U_isSuperAdmin,
    U.U_comment,
    U.U_lang,
    CS.CS_status,
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize
FROM
    `User",profile,"` U
        left join
    `CourseStatus` CS ON (U.U_id = CS.U_id)
        left join
    `Course` C ON (CS.C_id = C.C_id)
WHERE
    U.U_id = '",userid,"' or U.U_username = '",userid,"' or U_externalId = '",userid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;