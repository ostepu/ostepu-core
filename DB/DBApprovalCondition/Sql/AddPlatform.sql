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

CREATE TABLE IF NOT EXISTS `ApprovalCondition` (
  `AC_id` INT NOT NULL AUTO_INCREMENT,
  `C_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `AC_percentage` FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (`AC_id`),
  UNIQUE INDEX `AC_id_UNIQUE` USING BTREE (`AC_id` ASC),
  CONSTRAINT `fk_ApprovalConditions_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `ExerciseType` (`ET_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ApprovalConditions_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `Course` (`C_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>