select C_id as _Id, C_name as _name, C_semester as _semester, C_defaultGroupSize as _defaultGroupSize, null as _exerciseSheets 
from course NATURAL JOIN coursestatus
where U_id = $userid