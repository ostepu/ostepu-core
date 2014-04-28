SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Choice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Choice_{$object->getId()}` (
  `CH_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NULL,
  `FO_id` INT NOT NULL,
  `CH_text` VARCHAR(255) NULL DEFAULT '',
  `CH_correct` INT NULL DEFAULT 0,
  PRIMARY KEY (`CH_id`),
  UNIQUE INDEX `CH_id_UNIQUE` (`CH_id` ASC),
  INDEX `redundanz16` (`E_id` ASC, `FO_id` ASC),
  CONSTRAINT `fk_Choice_Form1_{$object->getId()}`
    FOREIGN KEY (`FO_id`)
    REFERENCES `Form_{$object->getId()}` (`FO_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Choice_Exercise1_{$object->getId()}`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz16_{$object->getId()}`
    FOREIGN KEY (`E_id` , `FO_id`)
    REFERENCES `Form_{$object->getId()}` (`E_id` , `FO_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Choice_BUPD_{$object->getId()}`;
CREATE TRIGGER `Choice_BUPD_{$object->getId()}` BEFORE UPDATE ON `Choice_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.E_id = (select FO.E_id from `Form_{$object->getId()}` FO where FO.FO_id = NEW.FO_id limit 1);
if (NEW.E_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding form';
END if;
END;

DROP TRIGGER IF EXISTS `Choice_BINS_{$object->getId()}`;
CREATE TRIGGER `Choice_BINS_{$object->getId()}` BEFORE INSERT ON `Choice_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.E_id = (select FO.E_id from `Form_{$object->getId()}` FO where FO.FO_id = NEW.FO_id limit 1);
if (NEW.E_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding form';
END if;
END;