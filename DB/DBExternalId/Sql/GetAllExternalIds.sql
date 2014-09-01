<?php
/**
 * @file GetAllExternalIds.sql
 * gets all external id from %ExternalId table
 * @author Till Uhlig
 * @result  C.C_id, C.C_name, C.C_semester, C.C_defaultGroupSize, EX.EX_id
 */
?>
 
select 
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize,
    EX.EX_id
from
    ExternalId EX left join Course C ON (EX.C_id = C.C_id)