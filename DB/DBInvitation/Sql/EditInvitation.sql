/**
 * @file EditInvitation.sql
 * updates a specified entry in %Invitation table
 * @author  Till Uhlig
 * @param int \$esid a %Invitation identifier
 * @param int \$userid a %Invitation identifier
 * @param int \$member a %Invitation identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */

UPDATE Invitation
SET $values
WHERE ES_id = '$esid' and U_id_leader = '$memberid' and U_id_member = '$userid'