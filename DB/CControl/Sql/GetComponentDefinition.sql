select CO.CO_id, CO.CO_name, CO.CO_address, CO.CO_option, null as CO_prefix, CL.CL_id, CL.CL_name, CL.CL_relevanz, CO2.CO_address as CL_address, CO2.CO_option as CL_option
from (Component CO left join ComponentLinkage CL on CO.CO_id = CL.CO_id_owner) left join Component CO2 on (CO2.CO_id = CL.CO_id_target)
where CO.CO_id = '$componentid' or CO.CO_name = '$componentid'