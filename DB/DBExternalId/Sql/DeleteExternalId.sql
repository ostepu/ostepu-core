/** 
 * @file DeleteExternalId.sql
 * deletes an specified external id from %ExternalId table
 * @author  Till Uhlig
 * @param int \$aid a %ExternalId identifier
 * @result -
 */
 
DELETE FROM ExternalId 
WHERE
    EX_id = '$exid'