SET @row = 0;
SET @a = (SELECT count(*) FROM File)-1;

INSERT
IGNORE INTO `Submission`
SELECT C.U_id as A, @row := @row + 1,
                       FLOOR(1 + (RAND() * @a)) as B,
                       NULL as C,
                       UNIX_TIMESTAMP(now())-FLOOR(0 + (RAND() * 60*60*24*60)) as D,
                       1 as E,
                       E.E_id as F,
                       E.ES_id as G,
                       NULL as H,
                       NULL as I,
                       NULL as J
FROM Exercise E
JOIN CourseStatus C ON (E.C_id=C.C_id
                        AND C.CS_status=0);