<?php
/**
 * @file AddCourseMember.sql
 * inserts an course status into %CourseStatus table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>
 
INSERT INTO CourseStatus SET <?php echo $values; ?>