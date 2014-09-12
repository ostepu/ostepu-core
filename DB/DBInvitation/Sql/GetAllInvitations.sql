<?php
/**
 * @file GetAllInvitations.sql
 * gets a table for output, where all invitations are listed
 * @author Till Uhlig
 * @param 
 * @result 
 * - U is the groupleader who invites
 * - U2 are the members of the Invitation without the leader
 */
?>

SELECT 
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
