/**
 * @file SetSelectedSubmission.sql
 * inserts a selected submission row into %SelectedSubmission table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
INSERT INTO SelectedSubmission SET $values