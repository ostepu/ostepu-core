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

CREATE TABLE IF NOT EXISTS `Attachment` (
  `A_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `F_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`A_id`),
  UNIQUE INDEX `A_id_UNIQUE` USING BTREE (`A_id` ASC),
  INDEX `redundanz3` (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Attachment_File1`
    FOREIGN KEY (`F_id`)
    REFERENCES `File` (`F_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Attachment_Exercise1`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `redundanz3`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Attachment_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Attachment_BINS`;
CREATE TRIGGER `Attachment_BINS` BEFORE INSERT ON `Attachment` FOR EACH ROW
<?php
/*check if corresponding exercise exists
if not send error message
author Lisa*/
?>
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;

DROP TRIGGER IF EXISTS `Attachment_BUPD`;
CREATE TRIGGER `Attachment_BUPD` BEFORE UPDATE ON `Attachment` FOR EACH ROW
<?php
/*check if corresponding exercise exists
if not send error message
author Lisa*/
?>
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>