SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `CourseStatus`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `CourseStatus` (
  `C_id` INT NOT NULL,
  `U_id` INT NOT NULL,
  `CS_status` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`C_id`, `U_id`),
  CONSTRAINT `fk_CourseStatus_Course1`
    FOREIGN KEY (`C_id`)
    REFERENCES `Course` (`C_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CourseStatus_User1`
    FOREIGN KEY (`U_id`)
    REFERENCES `User` (`U_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `CourseStatus_AINS`;
CREATE TRIGGER `CourseStatus_AINS` AFTER INSERT ON `CourseStatus` FOR EACH ROW
/*add group for the new member in this course
@author: Lisa Dietrich */
begin
if NEW.CS_status = 0 then
INSERT IGNORE INTO `Group` 
SELECT NEW.U_id , NEW.U_id , null, E.ES_id 
FROM ExerciseSheet E
WHERE E.C_id = NEW.C_id;
end if;
end;

DROP TRIGGER IF EXISTS `CourseStatus_AUPD`;
CREATE TRIGGER `CourseStatus_AUPD` AFTER UPDATE ON `CourseStatus` FOR EACH ROW
/*add group for the new member in this course
@author: Till Uhlig */
begin
if NEW.CS_status = 0 then
INSERT IGNORE INTO `Group` 
SELECT NEW.U_id , NEW.U_id , null, E.ES_id 
FROM ExerciseSheet E
WHERE E.C_id = NEW.C_id;
end if;
end;