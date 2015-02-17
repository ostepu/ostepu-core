DROP PROCEDURE IF EXISTS `DBCourseGetExistsPlatform`;
CREATE PROCEDURE `DBCourseGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Course';
end;