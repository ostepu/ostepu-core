-- update an entry from Group-table
UPDATE `Group`
SET $values
WHERE ES_id = $esid and U_id_leader = $userid