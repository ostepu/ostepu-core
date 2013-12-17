select CL.CL_id, CL.CL_name, CL.CL_relevanz, CO.CO_address as CL_address
from componentlinkage CL join component CO on CO.CO_id = CL.CO_id_owner
where CL_id = '$linkid' or CL_name = '$linkid'