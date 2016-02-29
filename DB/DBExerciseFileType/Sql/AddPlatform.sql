<?php
/**
 * @file AddPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `ExerciseFileType` (
  `EFT_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `EFT_text` VARCHAR(255) NULL,
  PRIMARY KEY (`EFT_id`),
  UNIQUE INDEX `EFT_id_UNIQUE` (`EFT_id` ASC),
  CONSTRAINT `fk_ExerciseFileType_Exercise1`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>