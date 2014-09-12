SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Form`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Form_<?php echo $object->getId(); ?>` (
  `FO_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `FO_type` INT NULL DEFAULT 0,
  `FO_solution` TEXT NULL,
  `FO_task` TEXT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`FO_id`),
  UNIQUE INDEX `E_id_UNIQUE` (`E_id` ASC),
  UNIQUE INDEX `FO_id_UNIQUE` (`FO_id` ASC),
  INDEX `redundanzForm_<?php echo $object->getId(); ?>` (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Form_Exercise1_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Form_ExerciseSheet1_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `redundanzForm_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Form_BINS_<?php echo $object->getId(); ?>`;
CREATE TRIGGER `Form_BINS_<?php echo $object->getId(); ?>` BEFORE INSERT ON `Form_<?php echo $object->getId(); ?>` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from `Exercise` E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;

DROP TRIGGER IF EXISTS `Form_BUPD_<?php echo $object->getId(); ?>`;
CREATE TRIGGER `Form_BUPD_<?php echo $object->getId(); ?>` BEFORE UPDATE ON `Form_<?php echo $object->getId(); ?>` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from `Exercise` E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;