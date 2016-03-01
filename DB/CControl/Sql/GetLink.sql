<?php
/**
 * @file GetLink.sql
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
    CL.CL_id,
    CL.CL_name,
    CL.CL_relevanz,
    CL.CL_priority,
    CL.CL_path,
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