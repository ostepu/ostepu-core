SET @row = 0;


INSERT
IGNORE INTO `Submission`
SELECT C.U_id, @row := @row + 1,
                       FLOOR(1 + (RAND() * (
                                                (SELECT count(*)
                                                 FROM File)-1))),
                       NULL,
                       UNIX_TIMESTAMP(now())-FLOOR(0 + (RAND() * 60*60*24*60)),
                       1,
                       E.E_id,
                       NULL,
                       NULL,
                       NULL,
                       NULL
FROM Exercise E
JOIN CourseStatus C ON (E.C_id=C.C_id
                        AND C.CS_status=0)
ORDER BY RAND() LIMIT <?php echo (26*$courseAmount*5*3);/**4*/ ?>;