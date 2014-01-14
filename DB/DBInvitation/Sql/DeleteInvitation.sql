DELETE FROM Invitation 
WHERE
    ES_id = '$esid' and U_id_leader = '$memberid' and U_id_member = '$userid'