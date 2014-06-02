/**
 * @file EditChoice.sql
 * updates a specified choice from %Choice table
 * @author  Till Uhlig
 * @param int \$choiceid a %Choice identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
UPDATE `Choice{$preChoice}_".Choice::getCourseFromChoiceId($choiceid)."`
SET {$object->getInsertData()}
WHERE CH_id = '".Choice::getIdFromChoiceId($choiceid)."'