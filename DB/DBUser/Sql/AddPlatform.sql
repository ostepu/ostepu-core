SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `User` (
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

-- -----------------------------------------------------
-- procedure IncFailedLogins
-- -----------------------------------------------------

CREATE PROCEDURE `IncFailedLogins` (IN userid varchar(120))
BEGIN
DECLARE count int(11);
select U_failed_logins into count
from User where U_id = userid or U_username = userid;

set count = count +1;

if count>=10 then
UPDATE User
SET U_flag = 2
where U_id = userid or U_username = userid or U_externalId = userid;
end if;

UPDATE User
SET U_failed_logins = count
where U_id = userid or U_username = userid or U_externalId = userid;

SELECT 
    U.U_id,
    U.U_username,
    U.U_firstName,
    U.U_lastName,
    U.U_email,
    U.U_title,
    U.U_flag,
    U.U_password,
    U.U_salt,
    U.U_failed_logins,
    U.U_externalId,
    U.U_studentNumber,
    CS.CS_status,
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize
FROM
    User U
        left join
    CourseStatus CS ON (U.U_id = CS.U_id)
        left join
    Course C ON (CS.C_id = C.C_id)
WHERE
    U.U_id like userid or U_username = userid or U_externalId = userid;
END;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `User_BUPD`;
CREATE TRIGGER `User_BUPD` BEFORE UPDATE ON `User` FOR EACH ROW
/*delete from user
@just keep id, username and flag
@author Till*/
begin
/*if (not New.U_flag is null and New.U_flag = OLD.U_flag) then
SIGNAL sqlstate '45001' set message_text = 'no flag change';
end if;*/

IF NEW.U_flag = 0 and OLD.U_flag = 1 THEN
SET NEW.U_email = '';
SET NEW.U_lastName = '';
SET NEW.U_firstName = '';
SET NEW.U_title = '';
SET NEW.U_password = '';
SET NEW.U_failed_logins = ' ';
END IF;
end;

DROP TRIGGER IF EXISTS `User_AUPD`;
CREATE TRIGGER `User_AUPD` AFTER UPDATE ON `User` FOR EACH ROW
/*if user is inactiv or deleted delete session
@author Lisa Dietrich */
begin
If NEW.U_flag != 1
then delete from `Session` where NEW.U_id = U_id;
end if;
end;