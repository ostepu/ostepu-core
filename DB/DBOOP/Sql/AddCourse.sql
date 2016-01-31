SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Choice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Testcase<?php echo $pre; ?>_<?php echo $object->getId(); ?>` (
  `OOP_id` INT NOT NULL AUTO_INCREMENT,
  `OOP_type` VARCHAR(255) NULL,
  `OOP_input` TEXT NULL,
  `OOP_output` TEXT NULL,
  `OOP_status` SMALLINT NULL DEFAULT 0,
  `PRO_id` INT NOT NULL,
  `OOP_runOutput` TEXT NULL,
  `OOP_workDir` TEXT NULL,
  `OOP_submission` INT NOT NULL,
  PRIMARY KEY (`OOP_id`),
  UNIQUE INDEX `OOP_id_UNIQUE` (`OOP_id` ASC),
  INDEX `redundanzProcess<?php echo $pre; ?>_<?php echo $object->getId(); ?>` (`OOP_status` ASC, `OOP_submission` ASC),
  CONSTRAINT `fk_Testcase_Submission1<?php echo $pre; ?>_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`OOP_submission`)
    REFERENCES `Submission` (`S_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Testcase_Process1<?php echo $pre; ?>_<?php echo $object->getId(); ?>`
    FOREIGN KEY (`PRO_id`)
    REFERENCES `Process<?php echo $pre; ?>_<?php echo $object->getId(); ?>` (`PRO_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
    )
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Testcase<?php echo $pre; ?>_BUPD_<?php echo $object->getId(); ?>`;
CREATE TRIGGER `Testcase<?php echo $pre; ?>_BUPD_<?php echo $object->getId(); ?>` BEFORE UPDATE ON `Testcase<?php echo $pre; ?>_<?php echo $object->getId(); ?>` FOR EACH ROW
BEGIN
SET NEW.PRO_id = (select PRO.PRO_id from `Process<?php echo $pre; ?>_<?php echo $object->getId(); ?>` PRO where PRO.PRO_id = NEW.PRO_id limit 1);
if (NEW.PRO_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding process';
END if;

SET NEW.OOP_submission = (select S.S_id from `Submission` S where S.S_id = NEW.OOP_submission limit 1);
if (NEW.OOP_submission is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding submission';
END if;
END;

DROP TRIGGER IF EXISTS `Testcase<?php echo $pre; ?>_BINS_<?php echo $object->getId(); ?>`;
CREATE TRIGGER `Testcase<?php echo $pre; ?>_BINS_<?php echo $object->getId(); ?>` BEFORE INSERT ON `Testcase<?php echo $pre; ?>_<?php echo $object->getId(); ?>` FOR EACH ROW
BEGIN
SET NEW.PRO_id = (select PRO.PRO_id from `Process<?php echo $pre; ?>_<?php echo $object->getId(); ?>` PRO where PRO.PRO_id = NEW.PRO_id limit 1);
if (NEW.PRO_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding process';
END if;

SET NEW.OOP_submission = (select S.S_id from `Submission` S where S.S_id = NEW.OOP_submission limit 1);
if (NEW.OOP_submission is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding submission';
END if;
END;