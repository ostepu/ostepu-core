UPDATE ComponentLinkage
SET $values
WHERE CL_id = '$linkid' or CO_id_owner = (select CO_id from Component where CO_name = '$linkid' limit 1);