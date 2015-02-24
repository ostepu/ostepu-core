CREATE OR REPLACE VIEW tutors AS
SELECT *
FROM CourseStatus C
WHERE C.CS_status>0
    OR (C.CS_status=0
        AND NOT exists
            (SELECT 1
             FROM CourseStatus C2
             WHERE C2.C_id = C.C_id
                 AND C.CS_status>0 LIMIT 1));


SET @row = 0;


INSERT
IGNORE INTO `Marking`
SELECT @row:=@row+1,
    (SELECT t.U_id
     FROM tutors t
     WHERE t.C_id=E.C_id
     ORDER BY rand() LIMIT 1),FLOOR(1+(RAND()*(
                                                   (SELECT count(*)
                                                    FROM File)-1))),
                              S.S_id_selected,
                              NULL,
                              NULL,
                              FLOOR(1+(RAND()*3)),
                              FLOOR(0+(RAND()*E.E_maxPoints+1)),
                              UNIX_TIMESTAMP(now())-FLOOR(0+(RAND()*60*60*24*60)),
                              NULL,
                              NULL,
                              NULL
FROM SelectedSubmission S
JOIN Exercise E ON (S.E_id = E.E_id);


DROP VIEW tutors;