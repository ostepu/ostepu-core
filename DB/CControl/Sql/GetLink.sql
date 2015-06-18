select 
    CL.CL_id,
    CL.CL_name,
    CL.CL_relevanz,
    CL.CL_priority,
    CL.CO_id_target,
    CL.CO_id_owner,
    CO.CO_name as CL_targetName,
    CO.CO_address as CL_address,
    CL.CO_id_target
from
    ComponentLinkage CL
        join
    Component CO ON CO.CO_id = CL.CO_id_owner
where
    CL_id = '<?php echo $linkid; ?>' or CL_name = '<?php echo $linkid; ?>'
ORDER BY CL_priority asc, CL_id asc