<?php
/**
 * @file GetComponentDefinition.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */
?>

select
    CO.CO_id,
    CO.CO_name,
    CO.CO_address,
    CO.CO_option,
    CO.CO_def,
    null as CO_prefix,
    CL.CL_id,
    CL.CL_name,
    CL.CO_id_target,
    CL.CO_id_owner,
    CL.CL_relevanz,
    CL.CL_priority,
    CL.CL_path,
    CO2.CO_name as CL_targetName,
    CO2.CO_address as CL_address,
    CO2.CO_option as CL_option
from
    Component CO
        left join
    ComponentLinkage CL ON CO.CO_id = CL.CO_id_owner
        left join
    Component CO2 ON CO2.CO_id = CL.CO_id_target
where
    (CO.CO_id = '<?php echo $componentid; ?>'
        or CO.CO_name = '<?php echo $componentid; ?>')
        and CO.CO_status = '1'
ORDER BY CL_priority asc, CL_id asc