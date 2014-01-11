/**
 * @file SetApprovalCondition.sql
 * inserts an approval condition into %ApprovalCondition table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */

INSERT INTO ApprovalCondition SET $values