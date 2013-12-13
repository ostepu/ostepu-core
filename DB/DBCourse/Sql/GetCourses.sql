select C_id, C_name, C_semester, C_defaultGroupSize, NULL as C_exerciseSheets
from course NATURAL JOIN coursestatus
where U_id = '$userid'