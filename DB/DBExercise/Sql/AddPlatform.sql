SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Exercise`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Exercise` (
  `C_id` INT NULL,
  `E_id` INT NOT NULL AUTO_INCREMENT,
  `ES_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `E_maxPoints` DECIMAL(3) NULL,
  `E_bonus` TINYINT(1) NULL,
  `E_id_link` INT NULL,
  `E_linkName` VARCHAR(45) NULL,
  PRIMARY KEY (`E_id`),
  UNIQUE INDEX `E_id_UNIQUE` USING BTREE (`E_id` ASC),
  INDEX `redundanz2` USING BTREE (`C_id` ASC, `ES_id` ASC),
  CONSTRAINT `fk_Exercise_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exercise_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `ExerciseType` (`ET_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz2`
    FOREIGN KEY (`C_id` , `ES_id`)
    REFERENCES `ExerciseSheet` (`C_id` , `ES_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Exercise_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Exercise_BDEL`;
CREATE TRIGGER `Exercise_BDEL` BEFORE DELETE ON `Exercise` FOR EACH ROW
/*delete corresponding data
author Till*/
BEGIN
DELETE FROM `Attachment` WHERE E_id = OLD.E_id;
#DELETE FROM `SelectedSubmission` WHERE E_id = OLD.E_id;
DELETE FROM `Submission` WHERE E_id = OLD.E_id;
DELETE FROM `ExerciseFileType` WHERE E_id = OLD.E_id;
END;

DROP TRIGGER IF EXISTS `Exercise_BINS`;
CREATE TRIGGER `Exercise_BINS` BEFORE INSERT ON `Exercise` FOR EACH ROW
/*check if corresponding exerciseSheet exists
@if not send error message_text
@author Lisa*/
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercisesheet';
END if;
END;

DROP TRIGGER IF EXISTS `Exercise_BUPD`;
CREATE TRIGGER `Exercise_BUPD` BEFORE UPDATE ON `Exercise` FOR EACH ROW
/* check if corresponding exerciseSheet exists
@if not send error message
@author Lisa*/
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercisesheet';
END if;
END;