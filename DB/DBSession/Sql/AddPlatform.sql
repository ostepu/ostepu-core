SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `Session` (
  `U_id` INT NOT NULL,
  `SE_sessionID` CHAR(32) NOT NULL,
  PRIMARY KEY (`U_id`, `SE_sessionID`),
  UNIQUE INDEX `SE_sessionID_UNIQUE` (`SE_sessionID` ASC),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC),
  CONSTRAINT `fk_Session_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `User` (`U_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Session_BINS`;
CREATE TRIGGER `Session_BINS` BEFORE INSERT ON `Session` FOR EACH ROW
<?php
/*check if user is inactive or deleted
@author Lisa*/
?>
begin
If (select U_flag from User where U_id = NEW.U_id limit 1) != 1
then SIGNAL sqlstate '45001' set message_text = 'user is inactive or deleted';
end if;
end;

DROP TRIGGER IF EXISTS `Session_AINS`;
CREATE TRIGGER `Session_AINS` AFTER INSERT ON `Session` FOR EACH ROW
<?php
/* set U_failedLogins = 0
@author Lisa*/
?>
begin
update User
set U_failed_logins = 0
where U_id = NEW.U_id;
end;

DROP TRIGGER IF EXISTS `Session_AUPD`;
CREATE TRIGGER `Session_AUPD` AFTER UPDATE ON `Session` FOR EACH ROW
<?php
/* set U_failedLogins = 0
@author Till*/
?>
begin
update User
set U_failed_logins = 0
where U_id = NEW.U_id;
end;
<?php include $sqlPath.'/procedures/GetUserSession.sql'; ?>
<?php include $sqlPath.'/procedures/GetSessionUser.sql'; ?>
<?php include $sqlPath.'/procedures/GetAllSessions.sql'; ?>
<?php include $sqlPath.'/procedures/GetExistsPlatform.sql'; ?>