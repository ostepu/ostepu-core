<?php
/**
 * @file Samples.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
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