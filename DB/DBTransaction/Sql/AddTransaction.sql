/**
 * @file AddTransaction.sql
 * inserts an transaction into %Transaction table
 * @author  Till Uhlig
 * @result -
 */

INSERT INTO `Transaction{$name}_{$courseid}` SET {$object->getInsertData()},T_random = '{$random}';
select '{$courseid}' as 'C_id';