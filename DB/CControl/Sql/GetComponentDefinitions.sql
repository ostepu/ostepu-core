<?php
/**
 * @file GetComponentDefinitions.sql
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
    CL.CL_relevanz,
    CL.CL_priority,
    CL.CL_path,
    CL.CO_id_target,
    CL.CO_id_owner,
    CO2.CO_name as CL_targetName,
    CO2.CO_address as CL_address,
    CO2.CO_option as CL_option
from
    Component CO
        left join
    ComponentLinkage CL ON CO.CO_id = CL.CO_id_owner
        left join
    Component CO2 ON CO2.CO_id = CL.CO_id_target
where CO.CO_status = '1'
ORDER BY CL_priority asc, CL_id asc