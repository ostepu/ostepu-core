SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Attachment_{$pre}_{$object->getId()}` (
  `A_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `F_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`A_id`),
  UNIQUE INDEX `A_id_UNIQUE` USING BTREE (`A_id` ASC),
  INDEX `redundanz3_{$pre}_{$object->getId()}` (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Attachment_File1_{$pre}_{$object->getId()}`
    FOREIGN KEY (`F_id`)
    REFERENCES `File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Attachment_Exercise1_{$pre}_{$object->getId()}`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz3_{$pre}_{$object->getId()}`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Attachment_ExerciseSheet1_{$pre}_{$object->getId()}`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Attachment_BINS_{$pre}_{$object->getId()}`;
CREATE TRIGGER `Attachment_BINS_{$pre}_{$object->getId()}` BEFORE INSERT ON `Attachment_{$pre}_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;

DROP TRIGGER IF EXISTS `Attachment_BUPD_{$pre}_{$object->getId()}`;
CREATE TRIGGER `Attachment_BUPD_{$pre}_{$object->getId()}` BEFORE UPDATE ON `Attachment_{$pre}_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;