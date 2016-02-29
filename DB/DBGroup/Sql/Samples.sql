<?php
/**
 * @file Samples.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

INSERT
IGNORE INTO `Group`
SELECT C.U_id,
       C.U_id,
       NULL,
       E.ES_id
FROM CourseStatus C
JOIN ExerciseSheet E ON (C.C_id = E.C_id)
WHERE C.CS_status = '0' ;