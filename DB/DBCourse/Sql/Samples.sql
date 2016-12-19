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

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

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


SET @t = 0;


INSERT
IGNORE INTO `Course<?php echo $profile;?>`
SELECT @row := @row + 1,
               SUBSTRING(concat(md5(@row),md5(@row),md5(@row)),-FLOOR(10 + (RAND() * 50))),
               concat('SS ',FLOOR(2014 + (RAND() * 10))),
               FLOOR(1 + (RAND() * 4))
FROM
    (SELECT 1
     FROM generator A,
                    generator B,
                              generator C LIMIT <?php echo ($courseAmount/2); ?>) AS Q
UNION
SELECT @row := @row + 1,
               SUBSTRING(concat(md5(@row),md5(@row),md5(@row)),-FLOOR(10 + (RAND() * 50))),
               concat('WS ',@t:=FLOOR(2014 + (RAND() * 10)),'/',@t+1),
               FLOOR(1 + (RAND() * 4))
FROM
    (SELECT 1
     FROM generator A,
                    generator B,
                              generator C LIMIT <?php echo ($courseAmount/2); ?>) AS Q;


DROP VIEW generator;