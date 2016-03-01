DELETE FROM Component
WHERE
    CO_id = '<?php echo $componentid; ?>'
    or CO_name = '<?php echo $componentid; ?>'