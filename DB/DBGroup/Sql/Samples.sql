INSERT
IGNORE INTO `Group`
SELECT C.U_id,
       C.U_id,
       NULL,
       E.ES_id
FROM CourseStatus C
JOIN ExerciseSheet E ON (C.C_id = E.C_id)
WHERE C.CS_status = '0' ;