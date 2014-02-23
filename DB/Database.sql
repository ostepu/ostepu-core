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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`File`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`File` (
  `F_id` INT NOT NULL AUTO_INCREMENT,
  `F_displayName` VARCHAR(255) NULL,
  `F_address` VARCHAR(255) NULL,
  `F_timeStamp` TIMESTAMP NULL,
  `F_fileSize` INT NULL,
  `F_hash` VARCHAR(255) NULL,
  PRIMARY KEY (`F_id`),
  UNIQUE INDEX `F_id_UNIQUE` (`F_id` ASC),
  UNIQUE INDEX `F_address_UNIQUE` (`F_address` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Backup`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Backup` (
  `B_id` INT NOT NULL AUTO_INCREMENT,
  `B_date` TIMESTAMP NOT NULL,
  `F_id_file` INT NOT NULL,
  PRIMARY KEY (`B_id`),
  INDEX `fk_Backup_File1_idx` (`F_id_file` ASC),
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
  `U_username` VARCHAR(45) NOT NULL,
  `U_email` VARCHAR(45) NULL,
  `U_lastName` VARCHAR(45) NULL,
  `U_firstName` VARCHAR(45) NULL,
  `U_title` VARCHAR(45) NULL,
  `U_password` VARCHAR(45) NOT NULL,
  `U_flag` SMALLINT NOT NULL DEFAULT 1,
  `U_oldUsername` VARCHAR(45) NULL,
  PRIMARY KEY (`U_id`),
  UNIQUE INDEX `U_id_UNIQUE` (`U_id` ASC),
  UNIQUE INDEX `U_email_UNIQUE` (`U_email` ASC))
ENGINE = InnoDB;


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
  INDEX `fk_ExerciseSheet_Course1_idx` (`C_id` ASC),
  INDEX `fk_ExerciseSheet_File1_idx` (`F_id_sampleSolution` ASC),
  INDEX `fk_ExerciseSheet_File2_idx` (`F_id_file` ASC),
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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Group` (
  `U_id_leader` INT NOT NULL,
  `U_id_member` INT NOT NULL,
  `ES_id` INT NOT NULL,
  INDEX `fk_Group_User1_idx` (`U_id_member` ASC),
  INDEX `fk_Group_ExerciseSheet1_idx` (`ES_id` ASC),
  PRIMARY KEY (`U_id_leader`, `ES_id`),
  CONSTRAINT `fk_Group_User1`
    FOREIGN KEY (`U_id_member`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`ES_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Group_User2`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `uebungsplattform`.`User` (`U_id`)
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
  INDEX `fk_Invitation_User1_idx` (`U_id_member` ASC),
  INDEX `fk_Invitation_User2_idx` (`U_id_leader` ASC),
  INDEX `fk_Invitation_ExerciseSheet1_idx` (`ES_id` ASC),
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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`CourseStatus`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`CourseStatus` (
  `C_id` INT NOT NULL,
  `U_id` INT NOT NULL,
  `CS_status` INT NOT NULL DEFAULT 0,
  INDEX `fk_CourseStatus_Course1_idx` (`C_id` ASC),
  INDEX `fk_CourseStatus_User1_idx` (`U_id` ASC),
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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Exercise`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Exercise` (
  `E_id` INT NOT NULL AUTO_INCREMENT,
  `ES_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `E_maxPoints` DECIMAL(3) NULL,
  `E_bonus` TINYINT(1) NULL,
  `E_id_link` INT NULL,
  PRIMARY KEY (`E_id`),
  INDEX `fk_Exercise_ExerciseSheet1_idx` (`ES_id` ASC),
  INDEX `fk_Exercise_ExerciseTypes1_idx` (`ET_id` ASC),
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
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Submission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Submission` (
  `U_id` INT NOT NULL,
  `S_id` INT NOT NULL AUTO_INCREMENT,
  `F_id_file` INT NOT NULL,
  `E_id` INT NOT NULL,
  `S_comment` VARCHAR(120) NULL,
  `S_date` TIMESTAMP NULL,
  `S_accepted` TINYINT(1) NOT NULL DEFAULT false,
  PRIMARY KEY (`S_id`),
  INDEX `fk_Submission_Exercise_idx` (`E_id` ASC),
  INDEX `fk_Submission_User1_idx` (`U_id` ASC),
  INDEX `fk_Submission_File1_idx` (`F_id_file` ASC),
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
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


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
  PRIMARY KEY (`M_id`),
  INDEX `fk_Marking_Submission1_idx` (`S_id` ASC),
  INDEX `fk_Marking_User1_idx` (`U_id_tutor` ASC),
  INDEX `fk_Marking_File1_idx` (`F_id_file` ASC),
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
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Attachment` (
  `A_id` INT NOT NULL AUTO_INCREMENT,
  `E_id` INT NOT NULL,
  `F_id` INT NOT NULL,
  PRIMARY KEY (`A_id`),
  INDEX `fk_Attachment_File1_idx` (`F_id` ASC),
  INDEX `fk_Attachment_Exercise1_idx` (`E_id` ASC),
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
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ApprovalCondition`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ApprovalCondition` (
  `AC_id` INT NOT NULL AUTO_INCREMENT,
  `C_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `AC_percentage` FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (`AC_id`),
  INDEX `fk_ApprovalConditions_ExerciseTypes1_idx` (`ET_id` ASC),
  INDEX `fk_ApprovalConditions_Course1_idx` (`C_id` ASC),
  UNIQUE INDEX `AC_id_UNIQUE` (`AC_id` ASC),
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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ZIP`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ZIP` (
  `Z_id` INT NOT NULL AUTO_INCREMENT,
  `Z_requestHash` VARCHAR(45) NOT NULL,
  `F_id` INT NOT NULL,
  PRIMARY KEY (`Z_id`),
  INDEX `fk_ZIP_File1_idx` (`F_id` ASC),
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
  INDEX `fk_SelectedSubmission_User1_idx` (`U_id_leader` ASC),
  INDEX `fk_SelectedSubmission_Submission1_idx` (`S_id_selected` ASC),
  PRIMARY KEY (`U_id_leader`, `S_id_selected`, `E_id`),
  INDEX `fk_SelectedSubmission_Exercise1_idx` (`E_id` ASC),
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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ComponentLinkage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ComponentLinkage` (
  `CL_id` INT NOT NULL AUTO_INCREMENT,
  `CO_id_owner` INT NOT NULL,
  `CO_id_target` INT NOT NULL,
  `CL_name` VARCHAR(120) NULL,
  `CL_relevanz` VARCHAR(255) NULL,
  PRIMARY KEY (`CL_id`),
  UNIQUE INDEX `CL_id_UNIQUE` (`CL_id` ASC),
  INDEX `fk_ComponentLinkage_Component1_idx` (`CO_id_owner` ASC),
  INDEX `fk_ComponentLinkage_Component2_idx` (`CO_id_target` ASC),
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
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`ExternalId`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ExternalId` (
  `EX_id` VARCHAR(255) NOT NULL,
  `C_id` INT NOT NULL,
  PRIMARY KEY (`EX_id`),
  INDEX `fk_ExternalId_Course1_idx` (`C_id` ASC),
  UNIQUE INDEX `EX_id_UNIQUE` (`EX_id` ASC),
  CONSTRAINT `fk_ExternalId_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `uebungsplattform`.`Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`RemovableFiles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`RemovableFiles` (
  `F_address` VARCHAR(255) NULL)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `uebungsplattform`.`Session`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`Session` (
  `U_id` INT NOT NULL,
  `SE_sessionID` VARCHAR(45) NOT NULL,
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
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (1, 1, 1);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (2, 1, 1);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (3, 1, 1);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (1, 1, 2);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (2, 2, 2);
INSERT INTO `uebungsplattform`.`Group` (`U_id_leader`, `U_id_member`, `ES_id`) VALUES (3, 3, 2);

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
INSERT INTO `uebungsplattform`.`Exercise` (`E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (1, 1, 1, 5, true, 1);
INSERT INTO `uebungsplattform`.`Exercise` (`E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (2, 1, 2, 10, false, 1);
INSERT INTO `uebungsplattform`.`Exercise` (`E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (3, 2, 3, 12, false, 3);
INSERT INTO `uebungsplattform`.`Exercise` (`E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (4, 3, 1, 15, true, 4);
INSERT INTO `uebungsplattform`.`Exercise` (`E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (5, 4, 2, 18, false, 5);
INSERT INTO `uebungsplattform`.`Exercise` (`E_id`, `ES_id`, `ET_id`, `E_maxPoints`, `E_bonus`, `E_id_link`) VALUES (6, 4, 3, 20, true, 6);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Submission`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `E_id`, `S_comment`, `S_date`, `S_accepted`) VALUES (1, 1, 1, 1, 'eins', '2013-12-03 00:00:01', true);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `E_id`, `S_comment`, `S_date`, `S_accepted`) VALUES (1, 2, 2, 1, 'zwei', '2013-12-04 00:00:01', true);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `E_id`, `S_comment`, `S_date`, `S_accepted`) VALUES (2, 3, 3, 1, 'drei', '2013-12-01 00:00:01', true);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `E_id`, `S_comment`, `S_date`, `S_accepted`) VALUES (2, 4, 4, 2, 'vier', '2013-11-02 00:00:01', true);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `E_id`, `S_comment`, `S_date`, `S_accepted`) VALUES (3, 5, 5, 2, 'fuenf', '2013-12-02 00:00:01', true);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `E_id`, `S_comment`, `S_date`, `S_accepted`) VALUES (4, 6, 6, 2, 'sechs', '2013-12-02 00:00:01', true);
INSERT INTO `uebungsplattform`.`Submission` (`U_id`, `S_id`, `F_id_file`, `E_id`, `S_comment`, `S_date`, `S_accepted`) VALUES (4, 7, 9, 2, 'sieben', '2013-12-02 00:00:01', true);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Attachment`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`) VALUES (1, 1, 1);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`) VALUES (2, 2, 2);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`) VALUES (3, 3, 3);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`) VALUES (4, 4, 4);
INSERT INTO `uebungsplattform`.`Attachment` (`A_id`, `E_id`, `F_id`) VALUES (5, 1, 5);

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
INSERT INTO `uebungsplattform`.`SelectedSubmission` (`U_id_leader`, `S_id_selected`, `E_id`) VALUES (1, 2, 1);
INSERT INTO `uebungsplattform`.`SelectedSubmission` (`U_id_leader`, `S_id_selected`, `E_id`) VALUES (2, 3, 3);
INSERT INTO `uebungsplattform`.`SelectedSubmission` (`U_id_leader`, `S_id_selected`, `E_id`) VALUES (3, 6, 2);

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

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ComponentLinkage`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (1, 3, 1, 'getFile', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (2, 1, 2, 'out1', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (3, 1, 3, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (4, 3, 5, 'out', '0-f');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (5, 2, 5, 'out', '0-f');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (6, 6, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (7, 7, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (8, 8, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (9, 9, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (10, 10, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (11, 11, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (12, 12, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (13, 4, 6, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (14, 4, 7, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (15, 4, 8, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (16, 4, 9, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (17, 4, 10, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (18, 4, 11, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (19, 4, 12, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (20, 4, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (21, 14, 15, 'course', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (22, 14, 16, 'group', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (23, 14, 17, 'user', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (24, 14, 4, 'DB', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (25, 14, 1, 'FS', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (26, 15, 14, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (27, 16, 14, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (28, 17, 14, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (29, 19, 5, 'out', '0-f');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (30, 1, 19, 'out3', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (31, 4, 25, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (32, 4, 20, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (33, 25, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (40, 20, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (34, 4, 21, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (35, 4, 22, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (36, 4, 23, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (37, 21, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (38, 22, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (39, 23, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (41, 4, 24, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (42, 24, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (43, 4, 18, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (44, 18, 13, 'out', '');

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
if not exists(select * from Invitation where ES_id = NEW.ES_id and U_id_member = NEW.U_id_member and U_id_leader = NEW.U_id_leader) then 
SIGNAL sqlstate '45001' set message_text = "no invitation";
ELSE 
delete from Invitation where ES_id = NEW.ES_id and U_id_member = NEW.U_id_member and U_id_leader = NEW.U_id_leader;
END if;
end;


$$

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
CREATE TRIGGER `Attachment_ADEL` AFTER DELETE ON `Attachment` FOR EACH ROW
Delete IGNORE From File where F_id = OLD.F_id
$$

USE `uebungsplattform`$$
CREATE TRIGGER `deleteComponentLinks` BEFORE DELETE ON `Component` FOR EACH ROW
DELETE FROM ComponentLinkage WHERE CO_id_owner = OLD.CO_id or CO_id_target = OLD.CO_id;
$$


DELIMITER ;
