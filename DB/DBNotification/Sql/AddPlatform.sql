<?php
/**
 * @file AddPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Notification`
-- -----------------------------------------------------
<?php $tableName = 'Notification'.$pre; ?>
CREATE TABLE IF NOT EXISTS `<?php echo $tableName;?>` (
  `NOT_id` INT NOT NULL AUTO_INCREMENT,
  `NOT_begin` INT NOT NULL DEFAULT 0,
  `NOT_end` INT NOT NULL DEFAULT 0,
  `NOT_requiredStatus` char(10) NOT NULL DEFAULT '0',
  `NOT_text` VARCHAR(1000) NULL,
  PRIMARY KEY (`NOT_id`),
  UNIQUE INDEX `NOT_id_UNIQUE` USING BTREE (`NOT_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;