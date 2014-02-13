/**
 * @file GetSheetGroups.sql
 * gets a table for output, where all members of the groups for a specific user are listed
 * @author Till Uhlig
 * @param int \$userid a %Group identifier
 * @result 
 * - U is the groupleader
 * - U2 are the members of the group without the leader
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
    G.U_id_leader = '$userid'