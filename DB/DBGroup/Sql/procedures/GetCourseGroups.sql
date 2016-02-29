<?php
/**
 * @file GetCourseGroups.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBGroupGetCourseGroups`;
CREATE PROCEDURE `DBGroupGetCourseGroups` (IN courseid INT)
READS SQL DATA
begin
SET @s = concat("
SELECT SQL_CACHE
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
    G.ES_id
FROM
    ((`Group` G
    join `Group` G2 ON (G.ES_id = G2.ES_id
        and G.U_id_member = G2.U_id_member))
    join User U ON U.U_id = G.U_id_member
        and (G2.U_id_leader <> U.U_id
        or G2.U_id_leader = G.U_id_leader))
        left join
    User U2 ON U2.U_id = G2.U_id_leader
        and U2.U_id <> G.U_id_member
WHERE
    G.U_id_leader = G2.U_id_leader
        and G.C_id = '",courseid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;