SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Submission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Submission` (
  `U_id` INT NOT NULL,
  `S_id` INT NOT NULL AUTO_INCREMENT,
  `F_id_file` INT NOT NULL,
  `S_comment` VARCHAR(120) NULL,
  `S_date` BIGINT NULL DEFAULT 0,
  `S_accepted` TINYINT(1) NOT NULL DEFAULT false,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  `S_flag` SMALLINT NULL DEFAULT 1,
  `S_leaderId` INT NULL,
  `S_hideFile` SMALLINT NULL DEFAULT 0,
  PRIMARY KEY (`S_id`),
  UNIQUE INDEX `S_id_UNIQUE` USING BTREE (`S_id` ASC),
  INDEX `redundanz5` USING BTREE (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Submission_Exercise`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz5`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Submission_BDEL`;
CREATE TRIGGER `Submission_BDEL` BEFORE DELETE ON `Submission` FOR EACH ROW
/*check if this submission is selected and replace 
@author Till, edited by Lisa Dietrich*/
begin
Delete From `Marking` where S_id = OLD.S_id;
delete from `SelectedSubmission` where S_id_selected = OLD.S_id;
end;

DROP TRIGGER IF EXISTS `Submission_BINS`;
CREATE TRIGGER `Submission_BINS` BEFORE INSERT ON `Submission` FOR EACH ROW
/*check if corresponding exerciseSheet exists
@if not send error message
@author Lisa*/
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding exercisesheet';
END if;

SET NEW.S_leaderId = (SELECT G.U_id_member FROM `Group` G WHERE G.U_id_leader = NEW.U_id and G.ES_id = NEW.ES_id limit 1);
if (NEW.S_leaderId is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding group leader';
END if;
END;

DROP TRIGGER IF EXISTS `Submission_BUPD`;
CREATE TRIGGER `Submission_BUPD` BEFORE UPDATE ON `Submission` FOR EACH ROW
/*check if corresponding exerciseSheet exists
@if not send error message
@author Lisa*/
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding exercisesheet';
END if;

SET NEW.S_leaderId = (SELECT G.U_id_member FROM `Group` G WHERE G.U_id_leader = NEW.U_id and G.ES_id = NEW.ES_id limit 1);
if (NEW.S_leaderId is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding group leader';
END if;
END;

DROP TRIGGER IF EXISTS `Submission_ADEL`;
CREATE TRIGGER `Submission_ADEL` AFTER DELETE ON `Submission` FOR EACH ROW
BEGIN
##Delete ignore from `File` where OLD.F_id_file = F_id;
END;