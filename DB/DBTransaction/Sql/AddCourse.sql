<?php
/**
 * @file AddCourse.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Transaction`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Transaction<?php echo $name; ?>_<?php echo $object->getId(); ?>` (
  `T_id` INT NOT NULL AUTO_INCREMENT,
  `T_durability` BIGINT NOT NULL,
  `T_authentication` varchar(140),
  `T_random` char(32) NOT NULL,
  `T_content` TEXT,
  PRIMARY KEY (`T_id`),
  UNIQUE INDEX `T_id_UNIQUE` USING BTREE (`T_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;