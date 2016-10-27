<?php
/**
 * @file AddCourse.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
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
<?php $tableName = 'Notification'.$pre.(isset($object) ? '_'.$object->getId() : ''); ?>
CREATE TABLE IF NOT EXISTS `<?php echo $tableName;?>` (
  `NOT_id` INT NOT NULL AUTO_INCREMENT,
  `NOT_begin` INT NOT NULL DEFAULT 0,
  `NOT_end` INT NOT NULL DEFAULT 0,
  `NOT_requiredStatus` char(10) NOT NULL DEFAULT '0',
  `NOT_text` VARCHAR(2500) NULL,
  PRIMARY KEY (`NOT_id`),
  UNIQUE INDEX `NOT_id_UNIQUE` USING BTREE (`NOT_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

ALTER TABLE `<?php echo $tableName; ?>` MODIFY COLUMN NOT_text VARCHAR(2500) NULL;