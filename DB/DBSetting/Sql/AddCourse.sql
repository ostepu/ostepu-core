<?php
/**
 * @file AddCourse.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `Setting`
-- -----------------------------------------------------
<?php $tableName = 'Setting'.$profile.'_'.$object->getId(); ?>
CREATE TABLE IF NOT EXISTS `<?php echo $tableName;?>` (
  `SET_id` INT NOT NULL AUTO_INCREMENT,
  `SET_name` VARCHAR(255) NOT NULL,
  `SET_state` VARCHAR(255) NOT NULL DEFAULT '',
  `SET_type` VARCHAR(255) NOT NULL DEFAULT 'TEXT',
  `SET_category` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`SET_id`),
  UNIQUE INDEX `SET_id_UNIQUE` USING BTREE (`SET_id` ASC),
  UNIQUE INDEX `SET_name_UNIQUE` USING BTREE (`SET_name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

call execute_if_column_not_exists('<?php echo $tableName;?>','SET_category','ALTER TABLE `<?php echo $tableName;?>` ADD COLUMN SET_category VARCHAR(255) NOT NULL DEFAULT \'\';');

ALTER TABLE `<?php echo $tableName;?>` MODIFY COLUMN SET_state VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `<?php echo $tableName;?>` MODIFY COLUMN SET_type VARCHAR(255) NOT NULL DEFAULT 'TEXT';

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>