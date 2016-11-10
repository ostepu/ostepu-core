DROP PROCEDURE IF EXISTS `DBGateGetAllGateProfiles`;
CREATE PROCEDURE `DBGateGetAllGateProfiles` (IN profile varchar(30), IN authProfile varchar(30), IN ruleProfile varchar(30))
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    GP.*,
    GR.*,
    GA.*
FROM
    `GateProfile",profile,"` GP
        left join
    `GateAuth",authProfile,"` GA ON (GP.GP_id = GA.GP_id)
        left join
    `GateRule",ruleProfile,"` GR ON (GP.GP_id = GR.GP_id);");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;