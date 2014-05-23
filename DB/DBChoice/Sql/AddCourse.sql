SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Choice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Choice{$preChoice}_{$object->getId()}` (
  `CH_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NULL,
  `FO_id` INT NOT NULL,
  `CH_text` VARCHAR(255) NULL DEFAULT '',
  `CH_correct` INT NULL DEFAULT 0,
  `S_id` INT NULL,
  PRIMARY KEY (`CH_id`),
  UNIQUE INDEX `CH_id_UNIQUE` (`CH_id` ASC),
  INDEX `redundanz16{$preChoice}_{$object->getId()}` (`E_id` ASC, `FO_id` ASC),
  CONSTRAINT `fk_Choice_Form1{$preChoice}{$preForm}_{$object->getId()}`
    FOREIGN KEY (`FO_id`)
    REFERENCES `Form{$preForm}_{$object->getId()}` (`FO_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Choice_Exercise1{$preChoice}{$preExercise}_{$object->getId()}`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise{$preExercise}` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz16{$preChoice}{$preForm}_{$object->getId()}`
    FOREIGN KEY (`E_id` , `FO_id`)
    REFERENCES `Form{$preForm}_{$object->getId()}` (`E_id` , `FO_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Choice_Submission1`
    FOREIGN KEY (`S_id`)
    REFERENCES `uebungsplattform`.`Submission` (`S_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Choice{$preChoice}_BUPD_{$object->getId()}`;
CREATE TRIGGER `Choice{$preChoice}_BUPD_{$object->getId()}` BEFORE UPDATE ON `Choice{$preChoice}_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.E_id = (select FO.E_id from `Form{$preForm}_{$object->getId()}` FO where FO.FO_id = NEW.FO_id limit 1);
if (NEW.E_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding form';
END if;
END;

DROP TRIGGER IF EXISTS `Choice{$preChoice}_BINS_{$object->getId()}`;
CREATE TRIGGER `Choice{$preChoice}_BINS_{$object->getId()}` BEFORE INSERT ON `Choice{$preChoice}_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.E_id = (select FO.E_id from `Form{$preForm}_{$object->getId()}` FO where FO.FO_id = NEW.FO_id limit 1);
if (NEW.E_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding form';
END if;
END;