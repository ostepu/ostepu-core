<?php
/**
 * @file GetSheetInvitations.sql
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

DROP PROCEDURE IF EXISTS `DBInvitationGetSheetInvitations`;
CREATE PROCEDURE `DBInvitationGetSheetInvitations` (IN esid INT)
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
    U2.U_id as U_id2,
    U2.U_username as U_username2,
    U2.U_firstName as U_firstName2,
    U2.U_lastName as U_lastName2,
    U2.U_email as U_email2,
    U2.U_title as U_title2,
    U2.U_flag as U_flag2,
    U2.U_studentNumber as U_studentNumber2,
    U2.U_isSuperAdmin as U_isSuperAdmin2,
    U2.U_comment as U_comment2,
    I.ES_id
from
    Invitation I
        join
    User U ON (I.U_id_member = U.U_id)
        join
    User U2 ON (I.U_id_leader = U2.U_id)
where
    I.ES_id = '",esid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;