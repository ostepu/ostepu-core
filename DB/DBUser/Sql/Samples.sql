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
IGNORE INTO `User`
SELECT @row := @row + 1,
               SUBSTRING(md5(@row),-8),
               SUBSTRING(md5(@row),-FLOOR(5 + (RAND() * 15))),
               SUBSTRING(md5(@row),-FLOOR(5 + (RAND() * 5))),
               SUBSTRING(md5(@row),-FLOOR(5 + (RAND() * 5))),
               NULL,
               md5(@row),
               1,
               md5(@row),
               0,
               NULL,
               NULL,
               0,
               NULL
FROM
    (SELECT 1
     FROM generator A,
                    generator B,
                              generator C,
                                        generator D LIMIT <?php echo $userAmount; ?>) AS Q;


DROP VIEW generator;