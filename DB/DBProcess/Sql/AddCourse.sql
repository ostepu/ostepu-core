SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Process`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Process_{$object->getId()}` (
  `PRO_id` INT NOT NULL AUTO_INCREMENT,
  `ES_id` INT NULL,
  `E_id` INT NOT NULL,
  `PRO_parameter` VARCHAR(255) NULL,
  `CO_id_target` INT NOT NULL,
  PRIMARY KEY (`PRO_id`),
  UNIQUE INDEX `EPRO_id_UNIQUE` (`PRO_id` ASC),
  INDEX `redundanzProcess_{$object->getId()}` (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Process_Exercise1_{$object->getId()}`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Process_ExerciseSheet1_{$object->getId()}`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanzProcess_{$object->getId()}`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Process_Component1_{$object->getId()}`
    FOREIGN KEY (`CO_id_target`)
    REFERENCES `Component` (`CO_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Process_BUPD_{$object->getId()}`;
CREATE TRIGGER `Process_BUPD_{$object->getId()}` BEFORE UPDATE ON `Process_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from `Exercise` E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;

DROP TRIGGER IF EXISTS `Process_BINS_{$object->getId()}`;
CREATE TRIGGER `Process_BINS_{$object->getId()}` BEFORE INSERT ON `Process_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from `Exercise` E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;