DELETE FROM ComponentLinkage
WHERE
    CL_id = '<?php echo $linkid; ?>' or CO_id_owner = (select CO_id from Component where CO_name = '<?php echo $linkid; ?>' limit 1);