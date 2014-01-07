SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `uebungsplattform2` ;
CREATE SCHEMA IF NOT EXISTS `uebungsplattform2` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `uebungsplattform2` ;

-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Course`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Course` (
  `C_id` INT NOT NULL AUTO_INCREMENT,
  `C_name` VARCHAR(120) NULL,
  `C_semester` VARCHAR(60) NULL,
  `C_defaultGroupSize` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`C_id`),
  UNIQUE INDEX `C_id_UNIQUE` (`C_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`File`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`File` (
  `F_id` INT NOT NULL AUTO_INCREMENT,
  `F_displayName` VARCHAR(255) NULL,
  `F_address` CHAR(55) NULL,
  `F_timeStamp` TIMESTAMP NULL,
  `F_fileSize` INT NULL,
  `F_hash` CHAR(40) NULL,
  PRIMARY KEY (`F_id`),
  UNIQUE INDEX `F_id_UNIQUE` (`F_id` ASC),
  UNIQUE INDEX `F_hash_UNIQUE` (`F_hash` ASC),
  UNIQUE INDEX `F_address_UNIQUE` (`F_address` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Backup`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Backup` (
  `B_id` INT NOT NULL AUTO_INCREMENT,
  `B_date` TIMESTAMP NOT NULL,
  `F_id_file` INT NOT NULL,
  PRIMARY KEY (`B_id`),
  UNIQUE INDEX `B_id_UNIQUE` (`B_id` ASC),
  CONSTRAINT `fk_Backup_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform2`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`User` (
  `U_id` INT NOT NULL AUTO_INCREMENT,
  `U_username` VARCHAR(120) NOT NULL,
  `U_email` VARCHAR(120) NULL,
  `U_lastName` VARCHAR(120) NULL,
  `U_firstName` VARCHAR(120) NULL,
  `U_title` CHAR(10) NULL,
  `U_password` CHAR(64) NOT NULL,
  `U_flag` SMALLINT NOT NULL DEFAULT 1,
  `U_oldUsername` VARCHAR(120) NULL,
  PRIMARY KEY (`U_id`),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`ExerciseSheet`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`ExerciseSheet` (
  `C_id` INT NOT NULL,
  `ES_id` INT NOT NULL AUTO_INCREMENT,
  `F_id_sampleSolution` INT NULL,
  `F_id_file` INT NULL,
  `ES_startDate` TIMESTAMP NULL,
  `ES_endDate` TIMESTAMP NULL,
  `ES_groupSize` INT NOT NULL DEFAULT 1,
  `ES_name` VARCHAR(120) NULL,
  PRIMARY KEY (`ES_id`),
  UNIQUE INDEX `ES_id_UNIQUE` (`ES_id` ASC),
  CONSTRAINT `fk_ExerciseSheet_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform2`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ExerciseSheet_File1`
    FOREIGN KEY (`F_id_sampleSolution`)
    REFERENCES `uebungsplattform2`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ExerciseSheet_File2`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform2`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Group` (
  `U_id_leader` INT NOT NULL,
  `U_id_member` INT NOT NULL,
  `C_id` INT NULL,
  `ES_id` INT NOT NULL,
  PRIMARY KEY (`ES_id`, `U_id_leader`),
  INDEX `fk_Group_ExerciseSheet1_idx` (`ES_id` ASC),
  CONSTRAINT `fk_Group_User1`
    FOREIGN KEY (`U_id_member`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_User2`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_ExerciseSheet2`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform2`.`ExerciseSheet` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform2`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Invitation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Invitation` (
  `U_id_leader` INT NOT NULL,
  `U_id_member` INT NOT NULL,
  `ES_id` INT NOT NULL,
  PRIMARY KEY (`U_id_leader`, `ES_id`, `U_id_member`),
  CONSTRAINT `fk_Invitation_User1`
    FOREIGN KEY (`U_id_member`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Invitation_User2`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Invitation_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform2`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
PACK_KEYS = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`CourseStatus`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`CourseStatus` (
  `C_id` INT NOT NULL,
  `U_id` INT NOT NULL,
  `CS_status` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`C_id`, `U_id`),
  CONSTRAINT `fk_CourseStatus_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform2`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CourseStatus_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`ExerciseType`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`ExerciseType` (
  `ET_id` INT NOT NULL AUTO_INCREMENT,
  `ET_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`ET_id`),
  UNIQUE INDEX `ET_id_UNIQUE` (`ET_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Exercise`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Exercise` (
  `C_id` INT NULL,
  `E_id` INT NOT NULL AUTO_INCREMENT,
  `ES_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `E_maxPoints` DECIMAL(3) NULL,
  `E_bonus` TINYINT(1) NULL,
  `E_id_link` INT NULL,
  PRIMARY KEY (`E_id`),
  UNIQUE INDEX `E_id_UNIQUE` (`E_id` ASC),
  CONSTRAINT `fk_Exercise_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform2`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exercise_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `uebungsplattform2`.`ExerciseType` (`ET_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exercise_ExerciseSheet2`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform2`.`ExerciseSheet` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Submission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Submission` (
  `U_id` INT NOT NULL,
  `S_id` INT NOT NULL AUTO_INCREMENT,
  `F_id_file` INT NOT NULL,
  `S_comment` VARCHAR(120) NULL,
  `S_date` TIMESTAMP NULL,
  `S_accepted` TINYINT(1) NOT NULL DEFAULT false,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`S_id`),
  UNIQUE INDEX `S_id_UNIQUE` (`S_id` ASC),
  CONSTRAINT `fk_Submission_Exercise`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform2`.`Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform2`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_Exercise1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform2`.`Exercise` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Marking`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Marking` (
  `M_id` INT NOT NULL AUTO_INCREMENT,
  `U_id_tutor` INT NOT NULL,
  `F_id_file` INT NOT NULL,
  `S_id` INT NOT NULL,
  `M_tutorComment` VARCHAR(120) NULL,
  `M_outstanding` TINYINT(1) NOT NULL DEFAULT false,
  `M_status` INT NOT NULL DEFAULT 0,
  `M_points` INT NOT NULL,
  `M_date` TIMESTAMP NOT NULL,
  `E_id` INT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`M_id`),
  UNIQUE INDEX `M_id_UNIQUE` (`M_id` ASC),
  CONSTRAINT `fk_Marking_Submission1`
    FOREIGN KEY (`S_id`)
    REFERENCES `uebungsplattform2`.`Submission` (`S_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_User1`
    FOREIGN KEY (`U_id_tutor`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_File1`
    FOREIGN KEY (`F_id_file`)
    REFERENCES `uebungsplattform2`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_Submission2`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform2`.`Submission` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Marking_Submission3`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform2`.`Submission` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Attachment` (
  `A_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `F_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`A_id`),
  UNIQUE INDEX `A_id_UNIQUE` (`A_id` ASC),
  CONSTRAINT `fk_Attachment_File1`
    FOREIGN KEY (`F_id`)
    REFERENCES `uebungsplattform2`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Attachment_Exercise1`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform2`.`Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Attachment_Exercise2`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform2`.`Exercise` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`ApprovalCondition`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`ApprovalCondition` (
  `AC_id` INT NOT NULL AUTO_INCREMENT,
  `C_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `AC_percentage` FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (`AC_id`),
  UNIQUE INDEX `AC_id_UNIQUE` USING BTREE (`AC_id` ASC),
  CONSTRAINT `fk_ApprovalConditions_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `uebungsplattform2`.`ExerciseType` (`ET_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ApprovalConditions_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform2`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`ZIP`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`ZIP` (
  `Z_id` INT NOT NULL AUTO_INCREMENT,
  `Z_requestHash` VARCHAR(45) NOT NULL,
  `F_id` INT NOT NULL,
  PRIMARY KEY (`Z_id`),
  UNIQUE INDEX `Z_id_UNIQUE` (`Z_id` ASC),
  CONSTRAINT `fk_ZIP_File1`
    FOREIGN KEY (`F_id`)
    REFERENCES `uebungsplattform2`.`File` (`F_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`SelectedSubmission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`SelectedSubmission` (
  `U_id_leader` INT NOT NULL,
  `S_id_selected` INT NOT NULL,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`U_id_leader`, `E_id`),
  CONSTRAINT `fk_SelectedSubmission_User1`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SelectedSubmission_Submission1`
    FOREIGN KEY (`S_id_selected`)
    REFERENCES `uebungsplattform2`.`Submission` (`S_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SelectedSubmission_Exercise1`
    FOREIGN KEY (`E_id`)
    REFERENCES `uebungsplattform2`.`Exercise` (`E_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_SelectedSubmission_Exercise2`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform2`.`Exercise` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Component`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Component` (
  `CO_id` INT NOT NULL AUTO_INCREMENT,
  `CO_name` VARCHAR(45) NOT NULL,
  `CO_address` VARCHAR(255) NOT NULL,
  `CO_option` VARCHAR(255) NULL,
  PRIMARY KEY (`CO_id`),
  UNIQUE INDEX `CO_id_UNIQUE` (`CO_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`ComponentLinkage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`ComponentLinkage` (
  `CL_id` INT NOT NULL AUTO_INCREMENT,
  `CO_id_owner` INT NOT NULL,
  `CL_name` VARCHAR(120) NULL,
  `CL_relevanz` VARCHAR(255) NULL,
  `CO_id_target` INT NOT NULL,
  PRIMARY KEY (`CL_id`),
  UNIQUE INDEX `CL_id_UNIQUE` (`CL_id` ASC),
  CONSTRAINT `fk_ComponentLinkage_Component1`
    FOREIGN KEY (`CO_id_owner`)
    REFERENCES `uebungsplattform2`.`Component` (`CO_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ComponentLinkage_Component2`
    FOREIGN KEY (`CO_id_target`)
    REFERENCES `uebungsplattform2`.`Component` (`CO_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`ExternalId`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`ExternalId` (
  `EX_id` VARCHAR(255) NOT NULL,
  `C_id` INT NOT NULL,
  PRIMARY KEY (`EX_id`),
  UNIQUE INDEX `EX_id_UNIQUE` (`EX_id` ASC),
  CONSTRAINT `fk_ExternalId_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform2`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`RemovableFiles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`RemovableFiles` (
  `F_address` CHAR(55) NULL,
  UNIQUE INDEX `F_address_UNIQUE` (`F_address` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform2`.`Session`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform2`.`Session` (
  `U_id` INT NOT NULL,
  `SE_sessionID` CHAR(32) NOT NULL,
  PRIMARY KEY (`U_id`, `SE_sessionID`),
  UNIQUE INDEX `SE_sessionID_UNIQUE` (`SE_sessionID` ASC),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC),
  CONSTRAINT `fk_Session_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `uebungsplattform2`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
USE `uebungsplattform2`;

DELIMITER $$
USE `uebungsplattform2`$$
CREATE TRIGGER `Course_BDEL` BEFORE DELETE ON `Course` FOR EACH ROW
BEGIN
DELETE FROM `CourseStatus` WHERE C_id = OLD.C_id;
DELETE FROM `ExternalId` WHERE C_id = OLD.C_id;
DELETE FROM `ExerciseSheet` WHERE C_id = OLD.C_id;
DELETE FROM `ApprovalCondition` WHERE C_id = OLD.C_id;
END;
$$

USE `uebungsplattform2`$$
CREATE TRIGGER `File_ADEL` AFTER DELETE ON `File` FOR EACH ROW
begin
insert IGNORE into RemovableFiles 
set F_address = OLD.F_address;
end;
$$

USE `uebungsplattform2`$$
CREATE TRIGGER `File_AINS` AFTER INSERT ON `File` FOR EACH ROW
Delete From RemovableFiles where F_address = NEW.F_address
$$

USE `uebungsplattform2`$$
CREATE TRIGGER `User_BUPD` BEFORE UPDATE ON `User` FOR EACH ROW
begin
IF NEW.U_flag = 0 and OLD.U_flag = 1 THEN
SET NEW.U_oldUsername = OLD.U_username;
SET NEW.U_username = '';
SET NEW.U_email = '';
SET NEW.U_lastName = '';
SET NEW.U_firstName = '';
SET NEW.U_title = '';
SET NEW.U_password = '';
END IF;
end;
$$

USE `uebungsplattform2`$$
CREATE TRIGGER `ExerciseSheet_BDEL` BEFORE DELETE ON `ExerciseSheet` FOR EACH ROW
BEGIN
DELETE IGNORE FROM `File` WHERE F_id = OLD.F_id_file or F_id = OLD.F_id_sampleSolution;
DELETE FROM `Invitation` WHERE ES_id = OLD.ES_id;
DELETE FROM `Group` WHERE ES_id = OLD.ES_id;
DELETE FROM `Exercise` WHERE ES_id = OLD.ES_id;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Group_BUPD` BEFORE UPDATE ON `Group` FOR EACH ROW
begin
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;

if not exists(select * from Invitation where ES_id = NEW.ES_id and U_id_member = NEW.U_id_member and U_id_leader = NEW.U_id_leader) then 
SIGNAL sqlstate '45001' set message_text = "no invitation";
ELSE 
delete from Invitation where ES_id = NEW.ES_id and U_id_member = NEW.U_id_member and U_id_leader = NEW.U_id_leader;
END if;
end;


$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Group_BINS` BEFORE INSERT ON `Group` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `ExerciseType_BDEL` BEFORE DELETE ON `ExerciseType` FOR EACH ROW
BEGIN
DELETE FROM `Exercise` WHERE ET_id = OLD.ET_id;
DELETE FROM `ApprovalConditions` WHERE ET_id = OLD.ET_id;
END;
$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Exercise_BDEL` BEFORE DELETE ON `Exercise` FOR EACH ROW
BEGIN
DELETE FROM `Attachment` WHERE E_id = OLD.E_id;
DELETE FROM `SelectedSubmission` WHERE E_id = OLD.E_id;
DELETE FROM `Submission` WHERE E_id = OLD.E_id;

END;
$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Exercise_BINS` BEFORE INSERT ON `Exercise` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Exercise_BUPD` BEFORE UPDATE ON `Exercise` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform2`$$
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

USE `uebungsplattform2`$$
CREATE TRIGGER `Submission_BINS` BEFORE INSERT ON `Submission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Submission_BUPD` BEFORE UPDATE ON `Submission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

USE `uebungsplattform2`$$
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

USE `uebungsplattform2`$$
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

USE `uebungsplattform2`$$
CREATE TRIGGER `Attachment_ADEL` AFTER DELETE ON `Attachment` FOR EACH ROW
Delete IGNORE From File where F_id = OLD.F_id;
$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Attachment_BINS` BEFORE INSERT ON `Attachment` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `Attachment_BUPD` BEFORE UPDATE ON `Attachment` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `SelectedSubmission_BINS` BEFORE INSERT ON `SelectedSubmission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `SelectedSubmission_BUPD` BEFORE UPDATE ON `SelectedSubmission` FOR EACH ROW
BEGIN
SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercise";
END if;
END;$$

USE `uebungsplattform2`$$
CREATE TRIGGER `deleteComponentLinks` BEFORE DELETE ON `Component` FOR EACH ROW
DELETE FROM ComponentLinkage WHERE CO_id_owner = OLD.CO_id or CO_id_target = OLD.CO_id;
$$


DELIMITER ;
