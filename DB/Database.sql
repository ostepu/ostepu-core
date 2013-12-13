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
  `C_name` VARCHAR(60) NULL,
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
  `F_displayName` VARCHAR(45) NULL,
  `F_address` VARCHAR(45) NULL,
  `F_timeStamp` TIMESTAMP NULL,
  `F_fileSize` INT NULL,
  `F_hash` VARCHAR(45) NULL,
  `F_links` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`F_id`),
  UNIQUE INDEX `F_id_UNIQUE` (`F_id` ASC))
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
  PRIMARY KEY (`U_id`),
  UNIQUE INDEX `U_username_UNIQUE` (`U_username` ASC),
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
  PRIMARY KEY (`U_id_leader`, `U_id_member`, `ES_id`),
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
  PRIMARY KEY (`U_id_leader`, `U_id_member`, `ES_id`),
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
-- Table `uebungsplattform`.`ExerciseTypes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ExerciseTypes` (
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
  `E_id_link` INT NOT NULL,
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
    REFERENCES `uebungsplattform`.`ExerciseTypes` (`ET_id`)
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
  `S_accepted` TINYINT(1) NULL,
  `S_date` TIMESTAMP NULL,
  `S_outstanding` TINYINT(1) NULL,
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
-- Table `uebungsplattform`.`ApprovalConditions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `uebungsplattform`.`ApprovalConditions` (
  `AC_id` INT NOT NULL AUTO_INCREMENT,
  `C_id` INT NOT NULL,
  `ET_id` INT NOT NULL,
  `AC_proportion` FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (`AC_id`),
  INDEX `fk_ApprovalConditions_ExerciseTypes1_idx` (`ET_id` ASC),
  INDEX `fk_ApprovalConditions_Course1_idx` (`C_id` ASC),
  UNIQUE INDEX `AC_id_UNIQUE` (`AC_id` ASC),
  CONSTRAINT `fk_ApprovalConditions_ExerciseTypes1`
    FOREIGN KEY (`ET_id`)
    REFERENCES `uebungsplattform`.`ExerciseTypes` (`ET_id`)
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
  `ES_id` INT NOT NULL,
  `S_id_selected` INT NOT NULL,
  INDEX `fk_SelectedSubmission_User1_idx` (`U_id_leader` ASC),
  INDEX `fk_SelectedSubmission_Submission1_idx` (`S_id_selected` ASC),
  INDEX `fk_SelectedSubmission_ExerciseSheet1_idx` (`ES_id` ASC),
  PRIMARY KEY (`U_id_leader`, `ES_id`, `S_id_selected`),
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
  CONSTRAINT `fk_SelectedSubmission_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `uebungsplattform`.`ExerciseSheet` (`ES_id`)
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
  `CO_option` VARCHAR(255) NOT NULL,
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


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Course`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (1, '﻿Bengalisch II', 'SS 11', 1);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (2, 'Fachschaftsseminar für Mathematik', 'WS 11/12', 2);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (3, 'Kulturgeschichte durch den Magen: Essen in Italien', 'SS 12', 3);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (4, 'Märkte im vor- und nachgelagerten Bereich der Landwirtschaft', 'WS 12/13', 4);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (5, 'Portugiesische Literatur nach 2000', 'SS 13', 5);
INSERT INTO `uebungsplattform`.`Course` (`C_id`, `C_name`, `C_semester`, `C_defaultGroupSize`) VALUES (6, 'Umwelt- und Planungsrecht', 'WS 13/14', 6);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`User`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`) VALUES (4, 'till', 'till@email.de', 'Uhlig', 'Till', NULL, 'test', 1);
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`) VALUES (2, 'lisa', 'lisa@email.de', 'Dietrich', 'Lisa', NULL, 'test', 1);
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`) VALUES (3, 'jörg', 'jörg@email.de', 'Baumgarten', 'Jörg', NULL, 'test', 1);
INSERT INTO `uebungsplattform`.`User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`) VALUES (1, 'super-admin', NULL, NULL, NULL, NULL, 'test', 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ExerciseSheet`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (1, 1, NULL, NULL, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (1, 2, NULL, NULL, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, 'Bonus');
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (2, 3, NULL, NULL, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (2, 4, NULL, NULL, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, 'Zusatz');
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (4, 5, NULL, NULL, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (5, 6, NULL, NULL, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, 'Weihnachtsaufgabe');
INSERT INTO `uebungsplattform`.`ExerciseSheet` (`C_id`, `ES_id`, `F_id_sampleSolution`, `F_id_file`, `ES_startDate`, `ES_endDate`, `ES_groupSize`, `ES_name`) VALUES (5, 7, NULL, NULL, '2013-12-02 00:00:01', '2013-12-31 23:59:59', 1, NULL);

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
-- Data for table `uebungsplattform`.`ExerciseTypes`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ExerciseTypes` (`ET_id`, `ET_name`) VALUES (1, 'Theorie');
INSERT INTO `uebungsplattform`.`ExerciseTypes` (`ET_id`, `ET_name`) VALUES (2, 'Praxis');
INSERT INTO `uebungsplattform`.`ExerciseTypes` (`ET_id`, `ET_name`) VALUES (3, 'Klausur');

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ApprovalConditions`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ApprovalConditions` (`AC_id`, `C_id`, `ET_id`, `AC_proportion`) VALUES (1, 1, 1, 0.5);
INSERT INTO `uebungsplattform`.`ApprovalConditions` (`AC_id`, `C_id`, `ET_id`, `AC_proportion`) VALUES (2, 2, 2, 0.5);
INSERT INTO `uebungsplattform`.`ApprovalConditions` (`AC_id`, `C_id`, `ET_id`, `AC_proportion`) VALUES (3, 3, 3, 0.6);
INSERT INTO `uebungsplattform`.`ApprovalConditions` (`AC_id`, `C_id`, `ET_id`, `AC_proportion`) VALUES (4, 4, 1, 0.6);

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`Component`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (1, 'FSControl', 'localhost/FS/FSControl/FSControl.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (2, 'FSFile', 'localhost/FS/FSFile/FSFile.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (3, 'FSZip', 'localhost/FS/FSZip/FSZip.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (4, 'DBControl', 'localhost/DB/DBControl/DBControl.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (5, 'FSBinder', 'localhost/FS/FSBinder/FSBinder.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (6, 'DBCourse', 'localhost/DB/DBCourse/DBCourse.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (7, 'DBUser', 'localhost/DB/DBUser/DBUser.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (8, 'DBCourseStatus', 'localhost/DB/DBCourseStatus/DBCourseStatus.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (9, 'DBExercise', 'localhost/DB/DBExercise/DBExercise.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (10, 'DBExerciseSheet', 'localhost/DB/DBExerciseSheet/DBExerciseSheet.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (11, 'DBExerciseType', 'localhost/DB/DBExerciseType/DBExerciseType.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (12, 'DBFile', 'localhost/DB/DBFile/DBFile.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (13, 'DBQuery', 'localhost/DB/DBQuery/DBQuery.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (14, 'LController', 'localhost/logic/Controller/LController.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (15, 'LCourse', 'localhost/logic/Course/course.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (16, 'LGroup', 'localhost/logic/Group/Group.php', '');
INSERT INTO `uebungsplattform`.`Component` (`CO_id`, `CO_name`, `CO_address`, `CO_option`) VALUES (17, 'LUser', 'localhost/logic/User/user.php', '');

COMMIT;


-- -----------------------------------------------------
-- Data for table `uebungsplattform`.`ComponentLinkage`
-- -----------------------------------------------------
START TRANSACTION;
USE `uebungsplattform`;
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (1, 3, 1, 'getFile', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (2, 1, 2, 'out1', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (3, 1, 3, 'out2', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (4, 3, 5, 'out', '0-f');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (5, 2, 5, 'out', '0-f');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (6, 6, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (7, 7, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (8, 8, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (9, 9, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (10, 10, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (11, 11, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (12, 12, 13, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (13, 4, 6, 'out1', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (14, 4, 7, 'out2', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (15, 4, 8, 'out3', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (16, 4, 9, 'out4', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (17, 4, 10, 'out5', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (18, 4, 11, 'out6', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (19, 4, 12, 'out7', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (20, 4, 13, 'out8', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (21, 14, 15, 'course', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (22, 14, 16, 'group', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (23, 14, 17, 'user', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (24, 14, 4, 'DB', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (25, 14, 1, 'FS', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (26, 15, 14, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (27, 16, 14, 'out', '');
INSERT INTO `uebungsplattform`.`ComponentLinkage` (`CL_id`, `CO_id_owner`, `CO_id_target`, `CL_name`, `CL_relevanz`) VALUES (28, 17, 14, 'out', '');

COMMIT;

