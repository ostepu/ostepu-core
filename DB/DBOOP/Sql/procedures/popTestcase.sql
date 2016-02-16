DROP PROCEDURE IF EXISTS `DBpopTestcase`;

CREATE PROCEDURE `DBpopTestcase` ()

BEGIN

DECLARE exit handler for sqlexception
  BEGIN
    -- ERROR
  ROLLBACK;
END;

DECLARE exit handler for sqlwarning
 BEGIN
    -- WARNING
 ROLLBACK;
END;

START TRANSACTION;
SET @groupAll = (SELECT concat("SELECT OOP_id, TableName INTO @SID, @Table FROM \(",group_concat(concat("SELECT OOP_id,OOP_status,'", table_name,"' as TableName FROM `", table_name,"` WHERE OOP_status = 0 FOR UPDATE") separator ' UNION ALL ')," LIMIT 1) A WHERE A.OOP_status = 0") 
FROM information_schema.tables
WHERE table_name LIKE 'Testcase_%');

PREPARE stmt1 FROM @groupAll;
EXECUTE stmt1;

IF(@Table IS NOT NULL) THEN

SET @getProcesstable = concat("select referenced_table_name, REPLACE(referenced_table_name, 'Process_', '') INTO @ProTable, @Course from information_schema.key_column_usage where referenced_table_name is not null and table_name = '", @Table,"' and referenced_column_name = 'PRO_id'");

SET @UpdateStat = concat("UPDATE ", @Table, " as T SET T.OOP_status = 1 WHERE T.OOP_id=",@SID);

PREPARE stmt4 FROM @UpdateStat;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

PREPARE stmt2 FROM @getProcesstable;
EXECUTE stmt2;

SET @statement = concat("
select DISTINCT T.OOP_id,
T.OOP_type,
T.OOP_input,
T.OOP_output,
T.OOP_status,
concat('", @course ,"','_',T.PRO_id) as PRO_id,
T.OOP_runOutput,
T.OOP_workDir,
T.OOP_submission,
concat('", @course ,"','_',PRO.PRO_id) as PRO_id2,
PRO.E_id as E_exercise2,
PRO.ES_id as ES_id2,
PRO.PRO_parameter as PRO_parameter2,
PRO.CO_id_target as CO_target2
from ", @Table, " as T, ", @ProTable," as PRO where T.PRO_id = PRO.PRO_id and T.OOP_id=",@SID);

PREPARE stmt3 FROM @statement;
EXECUTE stmt3;


END IF;

COMMIT;
END;