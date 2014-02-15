/**
 * @file EditSubmissionSelectedSubmission.sql
 * updates a specified selected submission row from %SelectedSubmission table
 * @author  Till Uhlig
 * @param int \$suid a %Submission identifier
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
UPDATE SelectedSubmission
SET $values
WHERE S_id_selected = '$suid'