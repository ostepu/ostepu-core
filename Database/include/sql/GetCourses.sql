select C_id as Id, C_name as name, C_semester as semester, C_defaultGroupSize as defaultGroupSize, null as exerciseSheets 
from course NATURAL JOIN coursestatus
where U_id = $userid