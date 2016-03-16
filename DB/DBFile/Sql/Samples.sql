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
IGNORE INTO `File`
SELECT @row := @row + 1,
               SUBSTRING(concat(md5(@row),md5(@row),md5(@row),'.txt'),-FLOOR(5 + (RAND() * 60))),
               concat('file/',md5(@row)),
               UNIX_TIMESTAMP(now())-FLOOR(0 + (RAND() * 60*60*24*60)),
               FLOOR(100 + (RAND() * 60*60*24*60)),
               md5(@row),
               NULL,
               NULL
FROM
    (SELECT 1
     FROM generator A,
                    generator B,
                              generator C,
                                        generator D,
                                                  generator E LIMIT <?php echo ($userAmount*10*30); ?>) AS Q;


DROP VIEW generator;