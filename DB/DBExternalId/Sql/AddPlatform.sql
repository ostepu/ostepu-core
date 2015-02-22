SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `ExternalId` (
  `EX_id` VARCHAR(255) NOT NULL,
  `C_id` INT NOT NULL,
  PRIMARY KEY (`EX_id`),
  UNIQUE INDEX `EX_id_UNIQUE` (`EX_id` ASC),
  CONSTRAINT `fk_ExternalId_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `Course` (`C_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
<?php include $sqlPath.'/procedures/GetExternalId.sql'; ?>
<?php include $sqlPath.'/procedures/GetAllExternalIds.sql'; ?>
<?php include $sqlPath.'/procedures/GetCourseExternalIds.sql'; ?>
<?php include $sqlPath.'/procedures/GetExistsPlatform.sql'; ?>