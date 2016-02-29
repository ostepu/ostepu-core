<?php
/**
 * @file Samples.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

CREATE OR REPLACE VIEW stat AS
SELECT 0 n
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 0
UNION ALL
SELECT 1
UNION ALL
SELECT 1
UNION ALL
SELECT 2
UNION ALL
SELECT 3;


SET @c =
    (SELECT count(*)
     FROM Course);


SET @row = 0;


INSERT
IGNORE INTO `CourseStatus`
SELECT mod(@row:=@row+1,@c)+1,
       U.U_id,
       S.*
FROM USER U,
          stat S
ORDER BY RAND() LIMIT <?php echo (10*$userAmount); ?>;


DROP VIEW stat;