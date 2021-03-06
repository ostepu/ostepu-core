<?php
/**
 * @file AddPlatform.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `User<?php echo $profile;?>` (
  `U_id` INT NOT NULL AUTO_INCREMENT,
  `U_username` VARCHAR(120) NOT NULL,
  `U_email` VARCHAR(120) NULL,
  `U_lastName` VARCHAR(120) NULL,
  `U_firstName` VARCHAR(120) NULL,
  `U_title` CHAR(10) NULL,
  `U_password` CHAR(64) NOT NULL,
  `U_flag` SMALLINT NULL DEFAULT 1,
  `U_salt` CHAR(40) NULL,
  `U_failed_logins` INT NULL DEFAULT 0,
  `U_externalId` VARCHAR(255) NULL,
  `U_studentNumber` VARCHAR(120) NULL,
  `U_isSuperAdmin` INT NULL DEFAULT 0,
  `U_comment` VARCHAR(255) NULL,
  PRIMARY KEY (`U_id`),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC),
  UNIQUE INDEX `U_username_UNIQUE` (`U_username` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

ALTER TABLE `User<?php echo $profile;?>` MODIFY COLUMN U_flag TINYINT NULL DEFAULT 1;
ALTER TABLE `User<?php echo $profile;?>` MODIFY COLUMN U_isSuperAdmin TINYINT NULL DEFAULT 0;
call execute_if_column_not_exists('User<?php echo $profile;?>','U_lang','ALTER TABLE `User<?php echo $profile;?>` ADD COLUMN U_lang CHAR(2) NOT NULL DEFAULT \'de\';');

DROP TRIGGER IF EXISTS `User<?php echo $profile;?>_BUPD`;
CREATE TRIGGER `User<?php echo $profile;?>_BUPD` BEFORE UPDATE ON `User<?php echo $profile;?>` FOR EACH ROW
<?php
/*delete from user
@just keep id, username and flag
@author Till*/
?>
begin
IF NEW.U_flag = 0 and OLD.U_flag = 1 THEN
SET NEW.U_email = '';
SET NEW.U_lastName = '';
SET NEW.U_firstName = '';
SET NEW.U_title = '';
SET NEW.U_password = '';
SET NEW.U_failed_logins = ' ';
END IF;
end;

DROP TRIGGER IF EXISTS `User<?php echo $profile;?>_AUPD`;
CREATE TRIGGER `User<?php echo $profile;?>_AUPD` AFTER UPDATE ON `User<?php echo $profile;?>` FOR EACH ROW
<?php
/*if user is inactiv or deleted delete session
@author Lisa Dietrich */
?>
begin
If NEW.U_flag != 1
then delete from `Session` where NEW.U_id = U_id;
end if;
end;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>