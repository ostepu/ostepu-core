select 
    CO_id, CO_name, CO_address, CO_option
from
    Component
where
    CO_id = '$componentid'
        or CO_name = '$componentid'