SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `Submission` (
  `U_id` INT NOT NULL,
  `S_id` INT NOT NULL AUTO_INCREMENT,
  `F_id_file` INT NULL,
  `S_comment` VARCHAR(255) NULL,
  `S_date` INT UNSIGNED NOT NULL DEFAULT 0,
  `S_accepted` TINYINT(1) NOT NULL DEFAULT false,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  `S_flag` TINYINT NOT NULL DEFAULT 1,
  `S_leaderId` INT NULL,
  `S_hideFile` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`S_id`),
  UNIQUE INDEX `S_id_UNIQUE` USING BTREE (`S_id` ASC),
  INDEX `redundanz5` USING BTREE (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Submission_Exercise`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `User` (`U_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `File` (`F_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `redundanz5`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

ALTER IGNORE TABLE `Submission` MODIFY COLUMN S_comment VARCHAR(255) NULL;
ALTER IGNORE TABLE `Submission` MODIFY COLUMN F_id_file INT NULL;
ALTER IGNORE TABLE `Submission` MODIFY COLUMN S_date INT UNSIGNED NOT NULL DEFAULT 0;
ALTER IGNORE TABLE `Submission` MODIFY COLUMN S_flag TINYINT NOT NULL DEFAULT 1;
ALTER IGNORE TABLE `Submission` MODIFY COLUMN S_hideFile TINYINT NOT NULL DEFAULT 0;

DROP TRIGGER IF EXISTS `Submission_BINS`;
CREATE TRIGGER `Submission_BINS` BEFORE INSERT ON `Submission` FOR EACH ROW
<?php
/*check if corresponding exerciseSheet exists
@if not send error message
@author Lisa*/
?>
BEGIN
if (NEW.ES_id is NULL) then
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding exercisesheet';
END if;
END if;

SET NEW.S_leaderId = (SELECT G.U_id_member FROM `Group` G WHERE G.U_id_leader = NEW.U_id and G.ES_id = NEW.ES_id limit 1);
if (NEW.S_leaderId is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding group leader';
END if;
END;

DROP TRIGGER IF EXISTS `Submission_BUPD`;
CREATE TRIGGER `Submission_BUPD` BEFORE UPDATE ON `Submission` FOR EACH ROW
<?php
/*check if corresponding exerciseSheet exists
@if not send error message
@author Lisa*/
?>
BEGIN
if (NEW.ES_id is NULL) then
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding exercisesheet';
END if;
END if;

SET NEW.S_leaderId = (SELECT G.U_id_member FROM `Group` G WHERE G.U_id_leader = NEW.U_id and G.ES_id = NEW.ES_id limit 1);
if (NEW.S_leaderId is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding group leader';
END if;
END;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>