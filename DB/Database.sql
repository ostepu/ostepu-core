SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `uebungsplattform` ;
CREATE SCHEMA IF NOT EXISTS `uebungsplattform` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `uebungsplattform` ;

-- -----------------------------------------------------
-- Table `uebungsplattform`.`Course`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Course` (
  `C_id` INT NOT NULL AUTO_INCREMENT,
  `C_name` VARCHAR(120) NULL,
  `C_semester` VARCHAR(60) NULL,
  `C_defaultGroupSize` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`C_id`),
  UNIQUE INDEX `C_id_UNIQUE` (`C_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`File`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`File` (
  `F_id` INT NOT NULL AUTO_INCREMENT,
  `F_displayName` VARCHAR(255) NULL,
  `F_address` CHAR(55) NULL,
  `F_timeStamp` BIGINT NULL DEFAULT 0,
  `F_fileSize` INT NULL,
  `F_hash` CHAR(40) NULL,
  PRIMARY KEY (`F_id`),
  UNIQUE INDEX `F_id_UNIQUE` (`F_id` ASC),
  UNIQUE INDEX `F_hash_UNIQUE` (`F_hash` ASC),
  UNIQUE INDEX `F_address_UNIQUE` (`F_address` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Backup`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Backup` (
  `B_id` INT NOT NULL AUTO_INCREMENT,
  `B_date` TIMESTAMP NOT NULL,
  `F_id_file` INT NOT NULL,
  PRIMARY KEY (`B_id`),
  UNIQUE INDEX `B_id_UNIQUE` (`B_id` ASC),
  CONSTRAINT `fk_Backup_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`User` (
  `U_id` INT NOT NULL AUTO_INCREMENT,
  `U_username` VARCHAR(120) NOT NULL,
  `U_email` VARCHAR(120) NULL,
  `U_lastName` VARCHAR(120) NULL,
  `U_firstName` VARCHAR(120) NULL,
  `U_title` CHAR(10) NULL,
  `U_password` CHAR(64) NOT NULL,
  `U_flag` SMALLINT NULL DEFAULT 1,
  `U_oldUsername` VARCHAR(120) NULL,
  `U_salt` CHAR(40) NULL,
  `U_failed_logins` INT NULL DEFAULT 0,
  PRIMARY KEY (`U_id`),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC),
  UNIQUE INDEX `U_username_UNIQUE` (`U_username` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ExerciseSheet`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ExerciseSheet` (
  `C_id` INT NOT NULL,
  `ES_id` INT NOT NULL AUTO_INCREMENT,
  `F_id_sampleSolution` INT NULL,
  `F_id_file` INT NULL,
  `ES_startDate` BIGINT NULL DEFAULT 0,
  `ES_endDate` BIGINT NULL DEFAULT 0,
  `ES_groupSize` INT NULL DEFAULT 1,
  `ES_name` VARCHAR(120) NULL,
  PRIMARY KEY (`ES_id`),
  UNIQUE INDEX `ES_id_UNIQUE` (`ES_id` ASC),
  CONSTRAINT `fk_ExerciseSheet_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ExerciseSheet_File1`
    FOREIGN KEY (`F_id_sampleSolution`)
    REFERENCES `uebungsplattform`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ExerciseSheet_File2`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Group` (
  `U_id_leader` INT NOT NULL,
  `U_id_member` INT NOT NULL,
  `C_id` INT NULL,
  `ES_id` INT NOT NULL,
  PRIMARY KEY (`ES_id`, `U_id_leader`),
  INDEX `fk_Group_ExerciseSheet1_idx` USING BTREE (`ES_id` ASC),
  INDEX `redundanz` (`C_id` ASC, `ES_id` ASC),
  CONSTRAINT `fk_Group_User1`
    FOREIGN KEY (`U_id_member`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_User2`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz`
    FOREIGN KEY (`C_id` , `ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`C_id` , `ES_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Invitation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Invitation` (
  `U_id_leader` INT NOT NULL,
  `U_id_member` INT NOT NULL,
  `ES_id` INT NOT NULL,
  PRIMARY KEY (`U_id_leader`, `ES_id`, `U_id_member`),
  CONSTRAINT `fk_Invitation_User1`
    FOREIGN KEY (`U_id_member`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Invitation_User2`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Invitation_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
PACK_KEYS = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`CourseStatus`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`CourseStatus` (
  `C_id` INT NOT NULL,
  `U_id` INT NOT NULL,
  `CS_status` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`C_id`, `U_id`),
  CONSTRAINT `fk_CourseStatus_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CourseStatus_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ExerciseType`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ExerciseType` (
  `ET_id` INT NOT NULL AUTO_INCREMENT,
  `ET_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`ET_id`),
  UNIQUE INDEX `ET_id_UNIQUE` (`ET_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Exercise`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Exercise` (
  `C_id` INT NULL,
  `E_id` INT NOT NULL AUTO_INCREMENT,
  `ES_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `E_maxPoints` DECIMAL(3) NULL,
  `E_bonus` TINYINT(1) NULL,
  `E_id_link` INT NULL,
  PRIMARY KEY (`E_id`),
  UNIQUE INDEX `E_id_UNIQUE` USING BTREE (`E_id` ASC),
  INDEX `redundanz2` USING BTREE (`C_id` ASC, `ES_id` ASC),
  CONSTRAINT `fk_Exercise_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Exercise_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `uebungsplattform`.`ExerciseType` (`ET_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exercise_ExerciseSheet2`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz2`
    FOREIGN KEY (`C_id` , `ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`C_id` , `ES_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Submission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Submission` (
  `U_id` INT NOT NULL,
  `S_id` INT NOT NULL AUTO_INCREMENT,
  `F_id_file` INT NOT NULL,
  `S_comment` VARCHAR(120) NULL,
  `S_date` BIGINT NULL DEFAULT 0,
  `S_accepted` TINYINT(1) NOT NULL DEFAULT false,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  `S_status` INT NULL DEFAULT 1,
  PRIMARY KEY (`S_id`),
  UNIQUE INDEX `S_id_UNIQUE` USING BTREE (`S_id` ASC),
  INDEX `redundanz5` USING BTREE (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Submission_Exercise`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_Exercise1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz5`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`ES_id` , `E_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Marking`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Marking` (
  `M_id` INT NOT NULL AUTO_INCREMENT,
  `U_id_tutor` INT NOT NULL,
  `F_id_file` INT NULL,
  `S_id` INT NOT NULL,
  `M_tutorComment` VARCHAR(120) NULL,
  `M_outstanding` TINYINT(1) NULL DEFAULT false,
  `M_status` INT NULL DEFAULT 0,
  `M_points` INT NULL DEFAULT 0,
  `M_date` BIGINT NULL DEFAULT 0,
  `E_id` INT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`M_id`),
  UNIQUE INDEX `M_id_UNIQUE` USING BTREE (`M_id` ASC),
  INDEX `redundanz6` USING BTREE (`ES_id` ASC, `E_id` ASC, `S_id` ASC),
  CONSTRAINT `fk_Marking_Submission1`
    FOREIGN KEY (`S_id`)
    REFERENCES `uebungsplattform`.`Submission` (`S_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_User1`
    FOREIGN KEY (`U_id_tutor`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_Submission2`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform`.`Submission` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_Submission3`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`Submission` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz6`
    FOREIGN KEY (`ES_id` , `E_id` , `S_id`)
    REFERENCES `uebungsplattform`.`Submission` (`ES_id` , `E_id` , `S_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Attachment` (
  `A_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `F_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`A_id`),
  UNIQUE INDEX `A_id_UNIQUE` USING BTREE (`A_id` ASC),
  INDEX `redundanz3` (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_Attachment_File1`
    FOREIGN KEY (`F_id`)
    REFERENCES `uebungsplattform`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Attachment_Exercise1`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Attachment_Exercise2`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz3`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`ES_id` , `E_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ApprovalCondition`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ApprovalCondition` (
  `AC_id` INT NOT NULL AUTO_INCREMENT,
  `C_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `AC_percentage` FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (`AC_id`),
  UNIQUE INDEX `AC_id_UNIQUE` USING BTREE (`AC_id` ASC),
  CONSTRAINT `fk_ApprovalConditions_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `uebungsplattform`.`ExerciseType` (`ET_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ApprovalConditions_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ZIP`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ZIP` (
  `Z_id` INT NOT NULL AUTO_INCREMENT,
  `Z_requestHash` VARCHAR(45) NOT NULL,
  `F_id` INT NOT NULL,
  PRIMARY KEY (`Z_id`),
  UNIQUE INDEX `Z_id_UNIQUE` (`Z_id` ASC),
  CONSTRAINT `fk_ZIP_File1`
    FOREIGN KEY (`F_id`)
    REFERENCES `uebungsplattform`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`SelectedSubmission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`SelectedSubmission` (
  `U_id_leader` INT NOT NULL,
  `S_id_selected` INT NOT NULL,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`U_id_leader`, `E_id`),
  INDEX `redundanz7` USING BTREE (`ES_id` ASC, `E_id` ASC),
  CONSTRAINT `fk_SelectedSubmission_User1`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SelectedSubmission_Submission1`
    FOREIGN KEY (`S_id_selected`)
    REFERENCES `uebungsplattform`.`Submission` (`S_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SelectedSubmission_Exercise1`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SelectedSubmission_Exercise2`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `redundanz7`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `uebungsplattform`.`Exercise` (`ES_id` , `E_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Component`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Component` (
  `CO_id` INT NOT NULL AUTO_INCREMENT,
  `CO_name` VARCHAR(45) NOT NULL,
  `CO_address` VARCHAR(255) NOT NULL,
  `CO_option` VARCHAR(255) NULL,
  PRIMARY KEY (`CO_id`),
  UNIQUE INDEX `CO_id_UNIQUE` (`CO_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ComponentLinkage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ComponentLinkage` (
  `CL_id` INT NOT NULL AUTO_INCREMENT,
  `CO_id_owner` INT NOT NULL,
  `CL_name` VARCHAR(120) NULL,
  `CL_relevanz` VARCHAR(255) NULL,
  `CO_id_target` INT NOT NULL,
  PRIMARY KEY (`CL_id`),
  UNIQUE INDEX `CL_id_UNIQUE` (`CL_id` ASC),
  CONSTRAINT `fk_ComponentLinkage_Component1`
    FOREIGN KEY (`CO_id_owner`)
    REFERENCES `uebungsplattform`.`Component` (`CO_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ComponentLinkage_Component2`
    FOREIGN KEY (`CO_id_target`)
    REFERENCES `uebungsplattform`.`Component` (`CO_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ExternalId`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ExternalId` (
  `EX_id` VARCHAR(255) NOT NULL,
  `C_id` INT NOT NULL,
  PRIMARY KEY (`EX_id`),
  UNIQUE INDEX `EX_id_UNIQUE` (`EX_id` ASC),
  CONSTRAINT `fk_ExternalId_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`RemovableFiles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`RemovableFiles` (
  `F_address` CHAR(55) NULL,
  UNIQUE INDEX `F_address_UNIQUE` (`F_address` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Session`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Session` (
  `U_id` INT NOT NULL,
  `SE_sessionID` CHAR(32) NOT NULL,
  PRIMARY KEY (`U_id`, `SE_sessionID`),
  UNIQUE INDEX `SE_sessionID_UNIQUE` (`SE_sessionID` ASC),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC),
  CONSTRAINT `fk_Session_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `uebungsplattform` ;

-- -----------------------------------------------------
-- procedure IncFailedLogins
-- -----------------------------------------------------

DELIMITER $$
USE `uebungsplattform`$$
CREATE PROCEDURE `IncFailedLogins` (IN userid varchar(120))
BEGIN
DECLARE count int(11);
select U_failed_logins into count
from User where U_id = userid or U_username = userid;

set count = count +1;

if count>=10 then
UPDATE User
SET U_flag = 2
where U_id = userid or U_username = userid;
end if;

UPDATE User
SET U_failed_logins = count
where U_id = userid or U_username = userid;

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
    U.U_id = userid or U_username = userid;
END;$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure deleteFile
-- -----------------------------------------------------

DELIMITER $$
USE `uebungsplattform`$$
CREATE PROCEDURE `deleteFile` (IN fileid int(11))
BEGIN
DECLARE count char(55);
select F_address into count
from File where F_id = fileid;

Delete from File
where F_id = fileid;

SELECT 
    count as F_address;
END;$$

DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
USE `uebungsplattform`;

DELIMITER $$
USE `uebungsplattform`$$
CREATE TRIGGER `Course_BDEL` BEFORE DELETE ON `Course` FOR EACH ROW
BEGIN
DELETE FROM `CourseStatus` WHERE C_id = OLD.C_id;
DELETE FROM `ExternalId` WHERE C_id = OLD.C_id;
DELETE FROM `ExerciseSheet` WHERE C_id = OLD.C_id;
DELETE FROM `ApprovalCondition` WHERE C_id = OLD.C_id;
END;
$$

USE `uebungsplattform`$$
CREATE TRIGGER `File_ADEL` AFTER DELETE ON `File` FOR EACH ROW
begin
insert IGNORE into RemovableFiles 
set F_address = OLD.F_address;
end;
$$

USE `uebungsplattform`$$
CREATE TRIGGER `File_AINS` AFTER INSERT ON `File` FOR EACH ROW
Delete From RemovableFiles where F_address = NEW.F_address
$$

USE `uebungsplattform`$$
CREATE TRIGGER `User_BUPD` BEFORE UPDATE ON `User` FOR EACH ROW
begin
/*if (not New.U_flag is null and New.U_flag = OLD.U_flag) then
SIGNAL sqlstate '45001' set message_text = "no flag change";
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
$$

USE `uebungsplattform`$$
CREATE TRIGGER `User_AUPD` AFTER UPDATE ON `User` FOR EACH ROW
begin
If NEW.U_flag != 1
then delete from `session` where NEW.U_id = U_id;
end if;
end;$$

USE `uebungsplattform`$$
CREATE TRIGGER `ExerciseSheet_BDEL` BEFORE DELETE ON `ExerciseSheet` FOR EACH ROW
BEGIN
DELETE IGNORE FROM `File` WHERE F_id = OLD.F_id_file or F_id = OLD.F_id_sampleSolution;
DELETE FROM `Invitation` WHERE ES_id = OLD.ES_id;
DELETE FROM `Group` WHERE ES_id = OLD.ES_id;
DELETE FROM `Exercise` WHERE ES_id = OLD.ES_id;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `ExerciseSheet_AINS` AFTER INSERT ON `ExerciseSheet` FOR EACH ROW
begin
INSERT INTO `Group` 
SELECT C.U_id , C.U_id , null , NEW.ES_id 
FROM CourseStatus C
WHERE C.C_id = NEW.C_id; 
#AND C.CS_status = 1 ;
end;
$$

USE `uebungsplattform`$$
CREATE TRIGGER `ExerciseSheet_BINS` BEFORE INSERT ON `ExerciseSheet` FOR EACH ROW
begin
IF NEW.ES_groupSize is null 
then Set NEW.ES_groupSize = (SELECT C_defaultGroupSize FROM Course WHERE C_id = NEW.C_id limit 1);
end if;
end;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Group_BINS` BEFORE INSERT ON `Group` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Group_BUPD` BEFORE UPDATE ON `Group` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Invitation_BINS` BEFORE INSERT ON `Invitation` FOR EACH ROW
begin
if ((SELECT COUNT(G.U_id_leader) FROM `Group` G WHERE G.U_id_member = NEW.U_id_member AND G.ES_id = NEW.ES_id)+(SELECT COUNT(U_id_member) FROM Invitation WHERE U_id_member = NEW.U_id_member AND ES_id = NEW.ES_id))>=(SELECT E.ES_groupSize FROM ExerciseSheet E WHERE E.ES_id = NEW.ES_id) 
then SIGNAL sqlstate '45001' set message_text = "maximal groupsize reached";
end if;
end;$$

USE `uebungsplattform`$$
CREATE TRIGGER `ExerciseType_BDEL` BEFORE DELETE ON `ExerciseType` FOR EACH ROW
BEGIN
DELETE FROM `Exercise` WHERE ET_id = OLD.ET_id;
DELETE FROM `ApprovalConditions` WHERE ET_id = OLD.ET_id;
END;
$$

USE `uebungsplattform`$$
CREATE TRIGGER `Exercise_BDEL` BEFORE DELETE ON `Exercise` FOR EACH ROW
BEGIN
DELETE FROM `Attachment` WHERE E_id = OLD.E_id;
DELETE FROM `SelectedSubmission` WHERE E_id = OLD.E_id;
DELETE FROM `Submission` WHERE E_id = OLD.E_id;

END;
$$

USE `uebungsplattform`$$
CREATE TRIGGER `Exercise_BINS` BEFORE INSERT ON `Exercise` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Exercise_BUPD` BEFORE UPDATE ON `Exercise` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Submission_BDEL` BEFORE DELETE ON `Submission` FOR EACH ROW
begin
Delete From `Marking` where S_id = OLD.S_id;
Delete ignore from `File` where OLD.F_id_file = F_id;
/*
if exists(select * from SelectedSubmission where S_id_selected = OLD.S_id) 
and 
exists(
select 
    S2.S_id
from
    (Submission S
    join Exercise E ON E.E_id = S.E_id)
        join
    `Group` G ON G.ES_id = E.ES_id
        join
    `Group` G2 ON G2.U_id_leader = G.U_id_member
        and G.U_id_member = S.U_id
        join
    Submission S2 ON S2.E_id = S.E_id and S2.S_id <> OLD.S_id
group by S2.S_id
order by S2.S_date desc
limit 1
)
then 

update SelectedSubmission
set S_id_selected = 

(
select 
    S2.S_id
from
    (Submission S
    join Exercise E ON E.E_id = S.E_id)
        join
    `Group` G ON G.ES_id = E.ES_id
        join
    `Group` G2 ON G2.U_id_leader = G.U_id_member
        and G.U_id_member = S.U_id
        join
    Submission S2 ON S2.E_id = S.E_id and S2.S_id <> OLD.S_id
group by S2.S_id
order by S2.S_date desc
limit 1
)
where S_id_selected = OLD.S_id and E_id = OLD.E_id;

else*/
delete from `SelectedSubmission` where S_id_selected = OLD.S_id;


#end if;

end;
$$

USE `uebungsplattform`$$
CREATE TRIGGER `Submission_BINS` BEFORE INSERT ON `Submission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Submission_BUPD` BEFORE UPDATE ON `Submission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Marking_BINS` BEFORE INSERT ON `Marking` FOR EACH ROW
BEGIN
SET NEW.E_id = (select S.E_id from Submission S where S.S_id = NEW.S_id limit 1);
if (NEW.E_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding submission";
END if;

SET NEW.ES_id = (select S.ES_id from Submission S where S.S_id = NEW.S_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding submission";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Marking_BUPD` BEFORE UPDATE ON `Marking` FOR EACH ROW
BEGIN
SET NEW.E_id = (select S.E_id from Submission S where S.S_id = NEW.S_id limit 1);
if (NEW.E_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding submission";
END if;

SET NEW.ES_id = (select S.ES_id from Submission S where S.S_id = NEW.S_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding submission";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Attachment_ADEL` AFTER DELETE ON `Attachment` FOR EACH ROW
Delete IGNORE From File where F_id = OLD.F_id;
$$

USE `uebungsplattform`$$
CREATE TRIGGER `Attachment_BINS` BEFORE INSERT ON `Attachment` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Attachment_BUPD` BEFORE UPDATE ON `Attachment` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `SelectedSubmission_BINS` BEFORE INSERT ON `SelectedSubmission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `SelectedSubmission_BUPD` BEFORE UPDATE ON `SelectedSubmission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `deleteComponentLinks` BEFORE DELETE ON `Component` FOR EACH ROW
DELETE FROM ComponentLinkage WHERE CO_id_owner = OLD.CO_id or CO_id_target = OLD.CO_id;
$$


DELIMITER ;
