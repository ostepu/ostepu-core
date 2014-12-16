SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `SelectedSubmission` (
  `U_id_leader` INT NOT NULL,
  `S_id_selected` INT NOT NULL,
  `E_id` INT NOT NULL,
  `ES_id` INT NULL,
  INDEX `redundanz7` USING BTREE (`ES_id` ASC, `E_id` ASC),
  UNIQUE INDEX `S_id_selected_UNIQUE` (`S_id_selected` ASC),
  PRIMARY KEY (`E_id`, `U_id_leader`),
  CONSTRAINT `fk_SelectedSubmission_User1`
    FOREIGN KEY (`U_id_leader`)
    REFERENCES `User` (`U_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_SelectedSubmission_Submission1`
    FOREIGN KEY (`S_id_selected`)
    REFERENCES `Submission` (`S_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_SelectedSubmission_Exercise1`
    FOREIGN KEY (`E_id`)
    REFERENCES `Exercise` (`E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `redundanz7`
    FOREIGN KEY (`ES_id` , `E_id`)
    REFERENCES `Exercise` (`ES_id` , `E_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_SelectedSubmission_ExerciseSheet1`
    FOREIGN KEY (`ES_id`)
    REFERENCES `ExerciseSheet` (`ES_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `SelectedSubmission_BINS`;
CREATE TRIGGER `SelectedSubmission_BINS` BEFORE INSERT ON `SelectedSubmission` FOR EACH ROW
<?php
/*check if corresponding exercise exists
@if not send error message
@author Lisa, Till*/
?>
BEGIN
SET NEW.E_id = (SELECT S.E_id FROM Submission S WHERE S.S_id = NEW.S_id_selected limit 1);

SET NEW.U_id_leader = (SELECT G.U_id_member FROM `Group` G, Submission S WHERE S.S_id = NEW.S_id_selected and G.U_id_leader = S.U_id and G.ES_id = S.ES_id limit 1);

if (NEW.U_id_leader is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding group leader';
END if;


SET NEW.ES_id = (select E.ES_id from Exercise E, `Group` G, Submission S where 
E.E_id = NEW.E_id and
S.S_id = NEW.S_id_selected and 
G.U_id_leader = S.U_id and 
NEW.U_id_leader = G.U_id_member and
G.ES_id = E.ES_id
 limit 1);

if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;

DROP TRIGGER IF EXISTS `SelectedSubmission_BUPD`;
CREATE TRIGGER `SelectedSubmission_BUPD` BEFORE UPDATE ON `SelectedSubmission` FOR EACH ROW
<?php
/*check if corresponding exercise exists
@if not send error message
@author Lisa*/
?>
BEGIN
SET NEW.E_id = (SELECT S.E_id FROM Submission S WHERE S.S_id = NEW.S_id_selected limit 1);

SET NEW.U_id_leader = (SELECT G.U_id_member FROM `Group` G, Submission S WHERE S.S_id = NEW.S_id_selected and G.U_id_leader = S.U_id and G.ES_id = S.ES_id limit 1);

if (NEW.U_id_leader is NULL) then
SIGNAL sqlstate '23000' set message_text = 'no corresponding group leader';
END if;


SET NEW.ES_id = (select E.ES_id from Exercise E where E.E_id = NEW.E_id limit 1);
if (NEW.ES_id is NULL) then
SIGNAL sqlstate '45001' set message_text = 'no corresponding exercise';
END if;
END;
<?php include $sqlPath.'/procedures/GetCourseSelected.sql'; ?>
<?php include $sqlPath.'/procedures/GetExerciseSelected.sql'; ?>
<?php include $sqlPath.'/procedures/GetSheetSelected.sql'; ?>
<?php include $sqlPath.'/procedures/GetExistsPlatform.sql'; ?>