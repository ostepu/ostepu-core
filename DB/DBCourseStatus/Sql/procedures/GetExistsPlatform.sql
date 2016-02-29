<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBCourseStatusGetExistsPlatform`;
CREATE PROCEDURE `DBCourseStatusGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'CourseStatus';
end;