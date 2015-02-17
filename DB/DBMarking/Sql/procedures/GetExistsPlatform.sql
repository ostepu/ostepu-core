DROP PROCEDURE IF EXISTS `DBMarkingGetExistsPlatform`;
CREATE PROCEDURE `DBMarkingGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Marking';
end;