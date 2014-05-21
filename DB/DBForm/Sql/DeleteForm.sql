/** 
 * @file DeleteForm.sql
 * deletes a specified form from %Form table
 * @author  Till Uhlig
 * @param int \$formid a %Form identifier
 * @result -
 */

DELETE FROM `Form_".Form::getCourseFromFormId($formid)."`
WHERE
    FO_id = '".Form::getIdFromFormId($formid)."'