select 
    CO_id, CO_name, CO_address, CO_option
from
    Component
where
    CO_id = '<?php echo $componentid; ?>'
        or CO_name = '<?php echo $componentid; ?>'