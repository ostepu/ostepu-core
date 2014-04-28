/** 
 * @file DeleteChoice.sql
 * deletes a specified choice from %Choice table
 * @author  Till Uhlig
 * @param int \$choiceid a %Choice identifier
 * @result -
 */
 
DELETE FROM `Choice_".Choice::getCourseFromChoiceId($choiceid)."`
WHERE
    CH_id = '".Choice::getIdFromChoiceId($choiceid)."'