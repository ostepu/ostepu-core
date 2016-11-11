<?php
/**
 * @file AddPlatform.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.6.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `GateProfile<?php echo $profile;?>` (
  `GP_id` INT NOT NULL AUTO_INCREMENT,
  `GP_name` VARCHAR(120),
  PRIMARY KEY (`GP_id`),
  UNIQUE INDEX `GP_id_UNIQUE` (`GP_id` ASC),
  UNIQUE INDEX `GP_name_UNIQUE` (`GP_name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `GateRule<?php echo $ruleProfile;?>` (
  `GR_id` INT NOT NULL AUTO_INCREMENT,
  `GP_id`  INT NOT NULL,
  `GR_type`  VARCHAR(120) NOT NULL DEFAULT 'httpCall',
  `GR_component` VARCHAR(120) NOT NULL,
  `GR_content` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`GR_id`),
  UNIQUE INDEX `GR_id_UNIQUE` (`GR_id` ASC),
  CONSTRAINT `fk_GateRule_GateProfile`
    FOREIGN KEY (`GP_id`)
    REFERENCES `GateProfile<?php echo $profile;?>` (`GP_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `GateAuth<?php echo $authProfile;?>` (
  `GA_id` INT NOT NULL AUTO_INCREMENT,
  `GP_id`  INT NOT NULL,
  `GA_type`  VARCHAR(120) NOT NULL DEFAULT 'noAuth',
  `GA_params`  VARCHAR(255),
  `GA_login`  VARCHAR(120),
  `GA_passwd`  VARCHAR(120),
  PRIMARY KEY (`GA_id`),
  UNIQUE INDEX `GA_id_UNIQUE` (`GA_id` ASC),
  CONSTRAINT `fk_GateAuth_GateProfile`
    FOREIGN KEY (`GP_id`)
    REFERENCES `GateProfile<?php echo $profile;?>` (`GP_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>