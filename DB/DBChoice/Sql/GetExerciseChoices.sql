/**
 * @file GetExerciseChoices.sql
 * gets choices from %Choice table
 * @author Till Uhlig
 * @param int \$eid an %Sheet identifier
 * @result 
 * - CH, the choice data
 */
 
SET @course = (select E.C_id from `Exercise` E where E.E_id = {$eid} limit 1);
SET @statement = 
concat(
\"select 
    concat('\", @course ,\"','_',CH.CH_id) as CH_id,
    CH.FO_id,
    CH.E_id,
    CH.CH_text,
    CH.CH_correct
from
    `Choice_\", @course, \"` CH
where
    CH.E_id = '{$eid}'\");
    
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
 