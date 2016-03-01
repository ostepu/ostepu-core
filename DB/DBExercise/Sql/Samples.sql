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

CREATE OR REPLACE VIEW generator AS
SELECT 1 n
UNION ALL
SELECT 2
UNION ALL
SELECT 3
UNION ALL
SELECT 4
UNION ALL
SELECT 5
UNION ALL
SELECT 6
UNION ALL
SELECT 7
UNION ALL
SELECT 8
UNION ALL
SELECT 9
UNION ALL
SELECT 10
UNION ALL
SELECT 11
UNION ALL
SELECT 12
UNION ALL
SELECT 13
UNION ALL
SELECT 14
UNION ALL
SELECT 15
UNION ALL
SELECT 16;


SET @c =
    (SELECT count(*)
     FROM Course);


SET @u =
    (SELECT count(*)
     FROM USER);


SET @e =
    (SELECT count(*)
     FROM ExerciseType);


SET @row = 0;


INSERT
IGNORE INTO `Exercise`
SELECT NULL, @row:=@row+1,
                   E.ES_id,
                   mod(@row:=@row+1,@e)+1,
                   FLOOR(1 + (RAND() * 8)),
                   0,
                   Q.*,
                   1
FROM ExerciseSheet E,

    (SELECT A.*
     FROM generator A LIMIT 3) AS Q;


DROP VIEW generator;