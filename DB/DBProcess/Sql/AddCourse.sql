<?php
/**
 * @file AddCourse.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2015
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Process`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Process<?php echo $pre; ?>_<?php echo $object->getId(); ?>` (
  `PRO_id` INT NOT NULL AUTO_INCREMENT,
  `ES_id` INT NULL,
  `E_id` INT NULL,
  `PRO_parameter` TEXT NULL,
  `CO_id_target` INT NOT NULL,
  PRIMARY KEY (`PRO_id`),
  UNIQUE INDEX `EPRO_id_UNIQUE` (`PRO_id` ASC),
  INDEX `redundanzProcess<?php echo $pre; ?>_<?php echo $object->getId(); ?>` (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Process_Exercise1<?php echo $pre; ?>_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Process_ExerciseSheet1<?php echo $pre; ?>_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `redundanzProcess<?php echo $pre; ?>_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Process_Component1<?php echo $pre; ?>_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`CO_id_target`)
    REFERENCES `Component` (`CO_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

ALTER IGNORE TABLE `Process<?php echo $pre; ?>_<?php echo $object->getId(); ?>` MODIFY COLUMN PRO_parameter TEXT NULL;

DROP TRIGGER IF EXISTS `Process_BUPD<?php echo $pre; ?>_<?php echo $object->getId(); ?>`;
CREATE TRIGGER `Process_BUPD<?php echo $pre; ?>_<?php echo $object->getId(); ?>` BEFORE UPDATE ON `Process<?php echo $pre; ?>_<?php echo $object->getId(); ?>` FOR EACH ROW
BEGIN
if (NEW.E_id is not null) then
SET NEW.ES_id = (select E.ES_id from `Exercise` E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;END if;
END;

DROP TRIGGER IF EXISTS `Process_BINS<?php echo $pre; ?>_<?php echo $object->getId(); ?>`;
CREATE TRIGGER `Process_BINS<?php echo $pre; ?>_<?php echo $object->getId(); ?>` BEFORE INSERT ON `Process<?php echo $pre; ?>_<?php echo $object->getId(); ?>` FOR EACH ROW
BEGIN
if (NEW.E_id is not null) then
SET NEW.ES_id = (select E.ES_id from `Exercise` E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;END if;
END;