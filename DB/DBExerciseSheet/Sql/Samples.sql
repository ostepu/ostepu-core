<?php
/**
 * @file Samples.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

CREATE OR REPLACE VIEW generator AS
SELECT 0 n
UNION ALL
SELECT 1
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
SELECT 15;


SET @row = 0;


INSERT
IGNORE INTO `ExerciseSheet`
SELECT C.C_id, @row := @row + 1,
                       NULL,
                       FLOOR(1 + (RAND() * (
                                                (SELECT count(*)
                                                 FROM File)-1))),
                       UNIX_TIMESTAMP(now())-(60*60*24*30)+FLOOR(0 + (RAND() * 60*60*24*30)),
                       UNIX_TIMESTAMP(now())+FLOOR(0 + (RAND() * 60*60*24*30)),
                       FLOOR(1 + (RAND() * 4)),
                       NULL
FROM Course C,
    (SELECT 1
     FROM generator LIMIT 10) AS Q;


DROP VIEW generator;