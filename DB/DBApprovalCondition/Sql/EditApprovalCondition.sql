/**
 * @file EditApprovalCondition.sql
 * updates an specified approval condition from %ApprovalCondition table
 * @author  Till Uhlig
 * @param int $apid a %ApprovalCondition identifier
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */

UPDATE ApprovalCondition
SET $values
WHERE AC_id = '$apid'