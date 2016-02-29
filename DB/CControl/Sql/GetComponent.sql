<?php
/**
 * @file GetComponent.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */
?>

select
    CO_id, CO_name, CO_address, CO_option, CO.CO_def
from
    Component
where
    CO_id = '<?php echo $componentid; ?>'
        or CO_name = '<?php echo $componentid; ?>'