SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Attachment{$pre}_{$object->getId()}` (
  `A_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `F_id` INT NOT NULL,
  `ES_id` INT NULL,
  `PRO_id` INT NULL,
  PRIMARY KEY (`A_id`),
  UNIQUE INDEX `A_id_UNIQUE` USING BTREE (`A_id` ASC),
  INDEX `redundanz3{$pre}_{$object->getId()}` (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Attachment{$pre}_File1_{$object->getId()}`
    FOREIGN KEY (`F_id`)
    REFERENCES `File` (`F_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Attachment{$pre}_Process1_{$object->getId()}`
    FOREIGN KEY (`PRO_id`)
    REFERENCES `Process_{$object->getId()}` (`PRO_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Attachment{$pre}_Exercise1_{$object->getId()}`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `redundanz3{$pre}_{$object->getId()}`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Attachment{$pre}_ExerciseSheet1_{$object->getId()}`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Attachment_BINS{$pre}_{$object->getId()}`;
CREATE TRIGGER `Attachment_BINS{$pre}_{$object->getId()}` BEFORE INSERT ON `Attachment{$pre}_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;

DROP TRIGGER IF EXISTS `Attachment_BUPD{$pre}_{$object->getId()}`;
CREATE TRIGGER `Attachment_BUPD{$pre}_{$object->getId()}` BEFORE UPDATE ON `Attachment{$pre}_{$object->getId()}` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;