/** 
 * @file DeleteInvitation.sql
 * deletes a specified group entry from %Invitation table
 * @author  Till Uhlig
 * @param int \$esid a %ExerciceSheet identifier
 * @param int \$userid a %User identifier
 * @param int \$memberid a %Invitation identifier
 * @result -
 */

DELETE FROM Invitation 
WHERE
    ES_id = '$esid' and U_id_leader = '$memberid' and U_id_member = '$userid'