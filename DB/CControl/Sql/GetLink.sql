select CL_id, CL_name, CO_id_owner, CO_id_target, CL_relevanz
from componentlinkage
where CL_id = '$linkid' or CL_name = '$linkid'