-- delete an entry from Group-table
DELETE FROM `Group` 
WHERE
    ES_id = $esid and U_id_leader = $userid