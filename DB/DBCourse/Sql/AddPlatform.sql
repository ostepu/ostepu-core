SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `Course` (
  `C_id` INT NOT NULL AUTO_INCREMENT,
  `C_name` VARCHAR(120) NULL,
  `C_semester` VARCHAR(60) NULL,
  `C_defaultGroupSize` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`C_id`),
  UNIQUE INDEX `C_id_UNIQUE` (`C_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
<?php include $sqlPath.'/procedures/GetCourse.sql'; ?>
<?php include $sqlPath.'/procedures/GetAllCourses.sql'; ?>
<?php include $sqlPath.'/procedures/GetUserCourses.sql'; ?>
<?php include $sqlPath.'/procedures/GetExistsPlatform.sql'; ?>