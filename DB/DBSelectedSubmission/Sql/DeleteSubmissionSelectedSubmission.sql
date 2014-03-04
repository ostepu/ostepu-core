/** 
 * @file DeleteSubmissionSelectedSubmission.sql
 * deletes a specified selected submission row from %SelectedSubmission table
 * @author  Till Uhlig
 * @param int \$suid a %Submission identifier
 * @result -
 */
 
DELETE FROM SelectedSubmission 
WHERE
    S_id_selected = '$suid'