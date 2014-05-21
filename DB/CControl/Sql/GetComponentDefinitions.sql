select 
    CO.CO_id,
    CO.CO_name,
    CO.CO_address,
    CO.CO_option,
    null as CO_prefix,
    CL.CL_id,
    CL.CL_name,
    CL.CO_id_target,
    CL.CL_relevanz,
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