<?php
/**
 * @file AddPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `Group` (
  `U_id_leader` INT NOT NULL,
  `U_id_member` INT NOT NULL,
  `C_id` INT NULL,
  `ES_id` INT NOT NULL,
  PRIMARY KEY (`ES_id`, `U_id_leader`),
  INDEX `fk_Group_ExerciseSheet1_idx` USING BTREE (`ES_id` ASC),
  INDEX `redundanz` (`C_id` ASC, `ES_id` ASC),
  CONSTRAINT `fk_Group_User1`
    FOREIGN KEY (`U_id_member`)
    REFERENCES `User` (`U_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Group_User2`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `User` (`U_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Group_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `redundanz`
    FOREIGN KEY (`C_id` , `ES_id`)
    REFERENCES `ExerciseSheet` (`C_id` , `ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Group_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `Course` (`C_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Group_BINS`;
CREATE TRIGGER `Group_BINS` BEFORE INSERT ON `Group` FOR EACH ROW
<?php
/*check if corresponding exerciseSheet exists
@if not send error message
@author Lisa*/
?>
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercisesheet';
END if;
END;

DROP TRIGGER IF EXISTS `Group_BUPD`;
CREATE TRIGGER `Group_BUPD` BEFORE UPDATE ON `Group` FOR EACH ROW
<?php
/*check if corresponding exerciseSheet exists
*and if invitation exists
*if not send error message
@author Lisa Dietrich*/
?>
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercisesheet';
END if;
if NEW.U_id_member <> NEW.U_id_leader and not exists (Select * from Invitation where U_id_member = NEW.U_id_member and U_id_leader = NEW.U_id_leader and ES_id = NEW.ES_id limit 1)
then SIGNAL sqlstate '45001' set message_text = 'corresponding ivitation does not exist';
end if;
END;

DROP TRIGGER IF EXISTS `Group_BDEL`;
CREATE TRIGGER `Group_BDEL` BEFORE DELETE ON `Group` FOR EACH ROW
<?php
/* check if all users in this group are deleted
@author Lisa Dietrich*/
?>
begin
<?php
/*if exists (Select U_id from user where U_id = OLD.U_id_leader AND U_flag = 1 limit 1)
then signal  sqlstate '45001' set message_text = 'active users in group';
else delete from Submission
where U_id = OLD.U_id_leader;
end if;*/
?>
end;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>