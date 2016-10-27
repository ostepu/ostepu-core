<?php
/**
 * @file AddPlatform.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

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
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Exercise_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `ExerciseType` (`ET_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `redundanz2`
    FOREIGN KEY (`C_id` , `ES_id`)
    REFERENCES `ExerciseSheet` (`C_id` , `ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Exercise_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `Course` (`C_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

ALTER TABLE `Exercise` MODIFY COLUMN E_maxPoints SMALLINT UNSIGNED NULL DEFAULT 0;
call execute_if_column_not_exists('Exercise','E_submittable','ALTER TABLE `Exercise` ADD COLUMN E_submittable TINYINT(1) NOT NULL DEFAULT 1;');


DROP TRIGGER IF EXISTS `Exercise_BINS`;
CREATE TRIGGER `Exercise_BINS` BEFORE INSERT ON `Exercise` FOR EACH ROW
<?php
/*check if corresponding exerciseSheet exists
@if not send error message_text
@author Lisa*/
?>
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercisesheet';
END if;
END;

DROP TRIGGER IF EXISTS `Exercise_BUPD`;
CREATE TRIGGER `Exercise_BUPD` BEFORE UPDATE ON `Exercise` FOR EACH ROW
<?php
/* check if corresponding exerciseSheet exists
@if not send error message
@author Lisa*/
?>
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercisesheet';
END if;
END;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>