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

CREATE TABLE IF NOT EXISTS `Session<?php echo $profile;?>` (
  `U_id` INT NOT NULL,
  `SE_sessionID` CHAR(32) NOT NULL,
  PRIMARY KEY (`U_id`, `SE_sessionID`),
  UNIQUE INDEX `SE_sessionID_UNIQUE` (`SE_sessionID` ASC),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC),
  CONSTRAINT `fk_Session<?php echo $profile;?>_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `User<?php echo $userProfile;?>` (`U_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Session<?php echo $profile;?>_BINS`;
CREATE TRIGGER `Session<?php echo $profile;?>_BINS` BEFORE INSERT ON `Session<?php echo $profile;?>` FOR EACH ROW
<?php
/*check if user is inactive or deleted
@author Lisa*/
?>
begin
If (select U_flag from `User<?php echo $userProfile;?>` where U_id = NEW.U_id limit 1) != 1
then SIGNAL sqlstate '45001' set message_text = 'user is inactive or deleted';
end if;
end;

DROP TRIGGER IF EXISTS `Session<?php echo $profile;?>_AINS`;
CREATE TRIGGER `Session<?php echo $profile;?>_AINS` AFTER INSERT ON `Session<?php echo $profile;?>` FOR EACH ROW
<?php
/* set U_failedLogins = 0
@author Lisa*/
?>
begin
update `User<?php echo $userProfile;?>`
set U_failed_logins = 0
where U_id = NEW.U_id;
end;

DROP TRIGGER IF EXISTS `Session<?php echo $profile;?>_AUPD`;
CREATE TRIGGER `Session<?php echo $profile;?>_AUPD` AFTER UPDATE ON `Session<?php echo $profile;?>` FOR EACH ROW
<?php
/* set U_failedLogins = 0
@author Till*/
?>
begin
update `User<?php echo $userProfile;?>`
set U_failed_logins = 0
where U_id = NEW.U_id;
end;

<?php if (is_dir($sqlPath.'/procedures')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/procedures/'.$inp);}},scandir($sqlPath.'/procedures'),array_pad(array(),count(scandir($sqlPath.'/procedures')),$sqlPath));?>
<?php if (is_dir($sqlPath.'/migrations')) array_map(function ($inp,$sqlPath){if ($inp!='.' && $inp!='..'){include($sqlPath.'/migrations/'.$inp);}},scandir($sqlPath.'/migrations'),array_pad(array(),count(scandir($sqlPath.'/migrations')),$sqlPath));?>