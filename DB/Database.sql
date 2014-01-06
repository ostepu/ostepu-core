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
  `U_flag` SMALLINT NOT NULL DEFAULT 1,
  `U_oldUsername` VARCHAR(120) NULL,
  PRIMARY KEY (`U_id`),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC))
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
  `ES_startDate` TIMESTAMP NULL,
  `ES_endDate` TIMESTAMP NULL,
  `ES_groupSize` INT NOT NULL DEFAULT 1,
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
  INDEX `fk_Group_ExerciseSheet1_idx` (`ES_id` ASC),
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
  CONSTRAINT `fk_Group_ExerciseSheet2`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
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
  UNIQUE INDEX `E_id_UNIQUE` (`E_id` ASC),
  CONSTRAINT `fk_Exercise_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exercise_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `uebungsplattform`.`ExerciseType` (`ET_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Exercise_ExerciseSheet2`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
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
  `S_date` TIMESTAMP NULL,
  `S_accepted` TINYINT(1) NOT NULL DEFAULT false,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  PRIMARY KEY (`S_id`),
  UNIQUE INDEX `S_id_UNIQUE` (`S_id` ASC),
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
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Marking`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Marking` (
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
    ON UPDATE NO ACTION)
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
  UNIQUE INDEX `A_id_UNIQUE` (`A_id` ASC),
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
    ON UPDATE NO ACTION)
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
    ON UPDATE NO ACTION)
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


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Course`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (1, '﻿Bengalisch II', 'SS 11', 1);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (2, 'Fachschaftsseminar fuer Mathematik', 'WS 11/12', 2);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (3, 'Kulturgeschichte durch den Magen: Essen in Italien', 'SS 12', 3);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (4, 'Maerkte im vor- und nachgelagerten Bereich der Landwirtschaft', 'WS 12/13', 4);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (5, 'Portugiesische Literatur nach 2000', 'SS 13', 5);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (6, 'Umwelt- und Planungsrecht', 'WS 13/14', 6);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`File`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (1, 'a.pdf', 'file/abcdef', '2013-12-08 00:00:01', 100, 'abcdef');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (2, 'b.pdf', 'file/abcde', '2013-12-07 00:00:01', 200, 'abcde');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (3, 'c.pdf', 'file/abcd', '2013-12-06 00:00:01', 300, 'abcd');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (4, 'd.pdf', 'file/abc', '2013-12-05 00:00:01', 400, 'abc');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (5, 'e.pdf', 'file/ab', '2013-12-04 00:00:01', 500, 'ab');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (6, 'f.pdf', 'file/a', '2013-12-03 00:00:01', 600, 'a');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (7, 'g.pdf', 'file/abcdefg', '2013-12-02 00:00:01', 700, 'abcdefg');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (8, 'h.pdf', 'file/abcdefgh', '2013-12-01 00:00:01', 800, 'abcdefgh');
INSERT INTO `uebungsplattform`.`File` (`F_id`, `F_displayName`, `F_address`, `F_timeStamp`, `F_fileSize`, `F_hash`) VALUES (9, 'i.pdf', 'file/delete', '2013-12-01 00:00:01', 900, 'delete');

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`User`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_oldUsername`) VALUES (4, 'till', 'till@email.de', 'Uhlig', 'Till', NULL, 'test', 1, NULL);
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_oldUsername`) VALUES (2, 'lisa', 'lisa@email.de', 'Dietrich', 'Lisa', NULL, 'test', 1, NULL);
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_oldUsername`) VALUES (3, 'joerg', 'joerg@email.de', 'Baumgarten', 'Joerg', NULL, 'test', 1, NULL);
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_oldUsername`) VALUES (1, 'super-admin', NULL, NULL, NULL, NULL, 'test', 1, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ExerciseSheet`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (1, 1, 1, 8, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (1, 2, 2, 1, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, 'Bonus');
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (2, 3, 3, 2, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (2, 4, 4, 3, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, 'Zusatz');
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (4, 5, 5, 4, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (5, 6, 6, 5, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, 'Weihnachtsaufgabe');
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (5, 7, 7, 6, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Group`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `C_id`, `ES_id`) VALUES (1, 1, NULL, 1);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `C_id`, `ES_id`) VALUES (2, 1, NULL, 1);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `C_id`, `ES_id`) VALUES (3, 1, NULL, 1);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `C_id`, `ES_id`) VALUES (1, 1, NULL, 2);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `C_id`, `ES_id`) VALUES (2, 2, NULL, 2);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `C_id`, `ES_id`) VALUES (3, 3, NULL, 2);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Invitation`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Invitation` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (1, 2, 1);
INSERT INTO `uebungsplattform`.`Invitation` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (1, 3, 1);
INSERT INTO `uebungsplattform`.`Invitation` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (2, 1, 2);
INSERT INTO `uebungsplattform`.`Invitation` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (3, 2, 2);
INSERT INTO `uebungsplattform`.`Invitation` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (4, 2, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`CourseStatus`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (1, 2, 0);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (1, 3, 1);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (1, 4, 3);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (2, 2, 3);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (2, 3, 0);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (2, 4, 1);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (3, 2, 0);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (3, 3, 3);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (4, 2, 3);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (4, 3, 1);
INSERT INTO `uebungsplattform`.`CourseStatus` (`C_id`, `U_id`, `CS_status`) VALUES (4, 4, 0);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ExerciseType`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ExerciseType` (`ET_id`, `ET_name`) VALUES (1, 'Theorie');
INSERT INTO `uebungsplattform`.`ExerciseType` (`ET_id`, `ET_name`) VALUES (2, 'Praxis');
INSERT INTO `uebungsplattform`.`ExerciseType` (`ET_id`, `ET_name`) VALUES (3, 'Klausur');

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Exercise`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Exercise` (`C_id`, `E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (NULL, 1, 1, 1, 5, true, 1);
INSERT INTO `uebungsplattform`.`Exercise` (`C_id`, `E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (NULL, 2, 1, 2, 10, false, 1);
INSERT INTO `uebungsplattform`.`Exercise` (`C_id`, `E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (NULL, 3, 2, 3, 12, false, 3);
INSERT INTO `uebungsplattform`.`Exercise` (`C_id`, `E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (NULL, 4, 3, 1, 15, true, 4);
INSERT INTO `uebungsplattform`.`Exercise` (`C_id`, `E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (NULL, 5, 4, 2, 18, false, 5);
INSERT INTO `uebungsplattform`.`Exercise` (`C_id`, `E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (NULL, 6, 4, 3, 20, true, 6);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Submission`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `S_comment`, `S_date`, `S_accepted`, `E_id`, `ES_id`) VALUES (1, 1, 1, 'eins', '2013-12-03 00:00:01', true, 1, NULL);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `S_comment`, `S_date`, `S_accepted`, `E_id`, `ES_id`) VALUES (1, 2, 2, 'zwei', '2013-12-04 00:00:01', true, 1, NULL);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `S_comment`, `S_date`, `S_accepted`, `E_id`, `ES_id`) VALUES (2, 3, 3, 'drei', '2013-12-01 00:00:01', true, 1, NULL);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `S_comment`, `S_date`, `S_accepted`, `E_id`, `ES_id`) VALUES (2, 4, 4, 'vier', '2013-11-02 00:00:01', true, 2, NULL);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `S_comment`, `S_date`, `S_accepted`, `E_id`, `ES_id`) VALUES (3, 5, 5, 'fuenf', '2013-12-02 00:00:01', true, 2, NULL);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `S_comment`, `S_date`, `S_accepted`, `E_id`, `ES_id`) VALUES (4, 6, 6, 'sechs', '2013-12-02 00:00:01', true, 2, NULL);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `S_comment`, `S_date`, `S_accepted`, `E_id`, `ES_id`) VALUES (4, 7, 9, 'sieben', '2013-12-02 00:00:01', true, 2, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Marking`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Marking` (`M_id`, `U_id_tutor`, `F_id_file`, `S_id`, `M_tutorComment`, `M_outstanding`, `M_status`, `M_points`, `M_date`, `E_id`, `ES_id`) VALUES (1, 2, 8, 1, 'nichts', false, 0, 10, '2013-12-08 00:00:01', NULL, NULL);
INSERT INTO `uebungsplattform`.`Marking` (`M_id`, `U_id_tutor`, `F_id_file`, `S_id`, `M_tutorComment`, `M_outstanding`, `M_status`, `M_points`, `M_date`, `E_id`, `ES_id`) VALUES (2, 2, 7, 2, 'nichts', true, 0, 12, '2013-12-08 00:00:01', NULL, NULL);
INSERT INTO `uebungsplattform`.`Marking` (`M_id`, `U_id_tutor`, `F_id_file`, `S_id`, `M_tutorComment`, `M_outstanding`, `M_status`, `M_points`, `M_date`, `E_id`, `ES_id`) VALUES (3, 1, 6, 3, 'nichts', false, 0, 13, '2013-12-08 00:00:01', NULL, NULL);
INSERT INTO `uebungsplattform`.`Marking` (`M_id`, `U_id_tutor`, `F_id_file`, `S_id`, `M_tutorComment`, `M_outstanding`, `M_status`, `M_points`, `M_date`, `E_id`, `ES_id`) VALUES (4, 1, 5, 4, 'nichts', true, 0, 14, '2013-12-08 00:00:01', NULL, NULL);
INSERT INTO `uebungsplattform`.`Marking` (`M_id`, `U_id_tutor`, `F_id_file`, `S_id`, `M_tutorComment`, `M_outstanding`, `M_status`, `M_points`, `M_date`, `E_id`, `ES_id`) VALUES (5, 4, 4, 5, 'nichts', false, 0, 15, '2013-12-08 00:00:01', NULL, NULL);
INSERT INTO `uebungsplattform`.`Marking` (`M_id`, `U_id_tutor`, `F_id_file`, `S_id`, `M_tutorComment`, `M_outstanding`, `M_status`, `M_points`, `M_date`, `E_id`, `ES_id`) VALUES (6, 4, 3, 6, 'nichts', true, 0, 16, '2013-12-08 00:00:01', NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Attachment`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`, `ES_id`) VALUES (1, 1, 1, NULL);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`, `ES_id`) VALUES (2, 2, 2, NULL);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`, `ES_id`) VALUES (3, 3, 3, NULL);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`, `ES_id`) VALUES (4, 4, 4, NULL);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`, `ES_id`) VALUES (5, 1, 5, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ApprovalCondition`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ApprovalCondition` (`AC_id`, `C_id`, `ET_id`, `AC_percentage`) VALUES (1, 1, 1, 0.5);
INSERT INTO `uebungsplattform`.`ApprovalCondition` (`AC_id`, `C_id`, `ET_id`, `AC_percentage`) VALUES (2, 2, 2, 0.5);
INSERT INTO `uebungsplattform`.`ApprovalCondition` (`AC_id`, `C_id`, `ET_id`, `AC_percentage`) VALUES (3, 3, 3, 0.6);
INSERT INTO `uebungsplattform`.`ApprovalCondition` (`AC_id`, `C_id`, `ET_id`, `AC_percentage`) VALUES (4, 4, 1, 0.6);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`SelectedSubmission`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`SelectedSubmission` (`U_id_leader`, `S_id_selected`, `E_id`, `ES_id`) VALUES (1, 2, 1, NULL);
INSERT INTO `uebungsplattform`.`SelectedSubmission` (`U_id_leader`, `S_id_selected`, `E_id`, `ES_id`) VALUES (2, 3, 3, NULL);
INSERT INTO `uebungsplattform`.`SelectedSubmission` (`U_id_leader`, `S_id_selected`, `E_id`, `ES_id`) VALUES (3, 6, 2, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Component`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (1, 'FSControl', 'localhost/uebungsplattform/FS/FSControl', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (2, 'FSFile', 'localhost/uebungsplattform/FS/FSFile', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (3, 'FSZip', 'localhost/uebungsplattform/FS/FSZip', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (4, 'DBControl', 'localhost/uebungsplattform/DB/DBControl', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (5, 'FSBinder', 'localhost/uebungsplattform/FS/FSBinder', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (6, 'DBCourse', 'localhost/uebungsplattform/DB/DBCourse', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (7, 'DBUser', 'localhost/uebungsplattform/DB/DBUser', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (8, 'DBAttachment', 'localhost/uebungsplattform/DB/DBAttachment', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (9, 'DBExercise', 'localhost/uebungsplattform/DB/DBExercise', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (10, 'DBExerciseSheet', 'localhost/uebungsplattform/DB/DBExerciseSheet', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (11, 'DBExerciseType', 'localhost/uebungsplattform/DB/DBExerciseType', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (12, 'DBFile', 'localhost/uebungsplattform/DB/DBFile', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (13, 'DBQuery', 'localhost/uebungsplattform/DB/DBQuery', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (14, 'LController', 'localhost/uebungsplattform/logic/Controller/LController.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (15, 'LCourse', 'localhost/uebungsplattform/logic/Course/course.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (16, 'LGroup', 'localhost/uebungsplattform/logic/Group/Group.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (17, 'LUser', 'localhost/uebungsplattform/logic/User/user.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (18, 'DBCourseStatus', 'localhost/uebungsplattform/DB/DBCourseStatus', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (19, 'FSPdf', 'localhost/uebungsplattform/FS/FSPdf', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (20, 'DBExternalId', 'localhost/uebungsplattform/DB/DBExternalId', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (21, 'DBApprovalCondition', 'localhost/uebungsplattform/DB/DBApprovalCondition', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (22, 'DBGroup', 'localhost/uebungsplattform/DB/DBGroup', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (23, 'DBInvitation', 'localhost/uebungsplattform/DB/DBInvitation', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (24, 'DBMarking', 'localhost/uebungsplattform/DB/DBMarking', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (25, 'DBSession', 'localhost/uebungsplattform/DB/DBSession', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (26, 'DBSubmission', 'localhost/uebungsplattform/DB/DBSubmission', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (27, 'DBSelectedSubmission', 'localhost/uebungsplattform/DB/DBSelectedSubmission', '');

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ComponentLinkage`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (1, 3, 'getFile', '', 1);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (2, 1, 'out1', '', 2);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (3, 1, 'out', '', 3);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (4, 3, 'out', '0-f', 5);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (5, 2, 'out', '0-f', 5);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (6, 6, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (7, 7, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (8, 8, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (9, 9, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (10, 10, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (11, 11, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (12, 12, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (13, 4, 'out', '', 6);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (14, 4, 'out', '', 7);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (15, 4, 'out', '', 8);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (16, 4, 'out', '', 9);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (17, 4, 'out', '', 10);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (18, 4, 'out', '', 11);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (19, 4, 'out', '', 12);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (20, 4, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (21, 14, 'course', '', 15);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (22, 14, 'group', '', 16);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (23, 14, 'user', '', 17);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (24, 14, 'DB', '', 4);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (25, 14, 'FS', '', 1);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (26, 15, 'out', '', 14);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (27, 16, 'out', '', 14);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (28, 17, 'out', '', 14);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (29, 19, 'out', '0-f', 5);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (30, 1, 'out3', '', 19);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (31, 4, 'out', '', 25);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (32, 4, 'out', '', 20);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (33, 25, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (40, 20, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (34, 4, 'out', '', 21);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (35, 4, 'out', '', 22);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (36, 4, 'out', '', 23);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (37, 21, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (38, 22, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (39, 23, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (41, 4, 'out', '', 24);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (42, 24, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (43, 4, 'out', '', 18);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (44, 18, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (45, 4, 'out', '', 27);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (46, 27, 'out', '', 13);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (47, 4, 'out', '', 26);
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES (48, 26, 'out', '', 13);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ExternalId`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ExternalId` (`EX_id`, `C_id`) VALUES ('Ver1', 1);
INSERT INTO `uebungsplattform`.`ExternalId` (`EX_id`, `C_id`) VALUES ('Ver12', 1);
INSERT INTO `uebungsplattform`.`ExternalId` (`EX_id`, `C_id`) VALUES ('Ver2', 2);
INSERT INTO `uebungsplattform`.`ExternalId` (`EX_id`, `C_id`) VALUES ('Ver3', 3);
INSERT INTO `uebungsplattform`.`ExternalId` (`EX_id`, `C_id`) VALUES ('Ver4', 4);
INSERT INTO `uebungsplattform`.`ExternalId` (`EX_id`, `C_id`) VALUES ('Ver5', 5);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Session`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Session` (`U_id`, `SE_sessionID`) VALUES (1, 'abcd');
INSERT INTO `uebungsplattform`.`Session` (`U_id`, `SE_sessionID`) VALUES (3, 'abc');
INSERT INTO `uebungsplattform`.`Session` (`U_id`, `SE_sessionID`) VALUES (4, 'ab');

COMMIT;

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

USE `uebungsplattform`$$
CREATE TRIGGER `ExerciseSheet_BDEL` BEFORE DELETE ON `ExerciseSheet` FOR EACH ROW
BEGIN
DELETE IGNORE FROM `File` WHERE F_id = OLD.F_id_file or F_id = OLD.F_id_sampleSolution;
DELETE FROM `Invitation` WHERE ES_id = OLD.ES_id;
DELETE FROM `Group` WHERE ES_id = OLD.ES_id;
DELETE FROM `Exercise` WHERE ES_id = OLD.ES_id;
END;$$

USE `uebungsplattform`$$
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

USE `uebungsplattform`$$
CREATE TRIGGER `Group_BINS` BEFORE INSERT ON `Group` FOR EACH ROW
BEGIN
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;
END;$$

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
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Exercise_BUPD` BEFORE UPDATE ON `Exercise` FOR EACH ROW
SET NEW.C_id = (select ES.C_id from ExerciseSheet ES where ES.ES_id = NEW.ES_id limit 1);
if (NEW.C_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding exercisesheet";
END if;$$

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
SET NEW.ES_id = (select E.ES_id from Exercise ES where E.E_id = NEW.E_id limit 1);
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
SET NEW.E_id = (select E.E_id from Submission S where S.S_id = NEW.S_id limit 1);
if (NEW.E_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding submission";
END if;

SET NEW.ES_id = (select E.ES_id from Submission S where S.S_id = NEW.S_id limit 1);
if (NEW.ES_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding submission";
END if;
END;$$

USE `uebungsplattform`$$
CREATE TRIGGER `Marking_BUPD` BEFORE UPDATE ON `Marking` FOR EACH ROW
BEGIN
SET NEW.E_id = (select E.E_id from Submission S where S.S_id = NEW.S_id limit 1);
if (NEW.E_id = NULL) then
SIGNAL sqlstate '45001' set message_text = "no corresponding submission";
END if;

SET NEW.ES_id = (select E.ES_id from Submission S where S.S_id = NEW.S_id limit 1);
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
