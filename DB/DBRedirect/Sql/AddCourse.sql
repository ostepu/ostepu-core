<?php
/**
 * @file AddCourse.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Redirect`
-- -----------------------------------------------------
<?php $tableName = 'Redirect'.$pre.'_'.$object->getId(); ?>
CREATE TABLE IF NOT EXISTS `<?php echo $tableName;?>` (
  `RED_id` INT NOT NULL AUTO_INCREMENT,
  `RED_title` VARCHAR(120) NOT NULL,
  `RED_url` VARCHAR(255) NOT NULL DEFAULT '',
  `RED_location` VARCHAR(20) NOT NULL DEFAULT 'sheet',
  `RED_sublocation` VARCHAR(20) NULL,
  `RED_condition` VARCHAR(20) NULL,
  `RED_style` VARCHAR(50) NULL,
  `RED_authentication` VARCHAR(20) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`RED_id`),
  UNIQUE INDEX `RED_id_UNIQUE` USING BTREE (`RED_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;