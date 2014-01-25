/**
 * @file GetSheetMemberInvitations.sql
 * gets a table for output, where all invitations are listed where the user is Groupmember for an specific Exercisesheet
 * @author Till Uhlig
 * @param int \%userid a User identifier
 * @param int \%esid a ExerciseSheet identifier
 * @result 
 * - U is the groupleader who invites
 * - U2 are the members of the Invitation without the leader
 */

SELECT 
    U.U_id,
    U.U_username,
    U.U_firstName,
    U.U_lastName,
    U.U_email,
    U.U_title,
    U.U_flag,
    U2.U_id as U_id2,
    U2.U_username as U_username2,
    U2.U_firstName as U_firstName2,
    U2.U_lastName as U_lastName2,
    U2.U_email as U_email2,
    U2.U_title as U_title2,
    U2.U_flag as U_flag2,
    I.ES_id
from
    Invitation I
        join
    User U ON (I.U_id_member = U.U_id)
        join
    User U2 ON (I.U_id_leader = U2.U_id)
where
    I.ES_id = $esid
        and I.U_id_leader = $userid