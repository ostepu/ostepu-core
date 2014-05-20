/**
 * @file GetCourseChoices.sql
 * gets all choices of a course from %Choice table
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @result 
 * - CH, the choice data
 */
 
select 
    concat('\", {$courseid} ,\"','_',CH.CH_id) as CH_id,
    CH.FO_id,
    CH.E_id,
    CH.CH_text,
    CH.CH_correct
from
    `Choice{$preChoice}_{$courseid}` CH