SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `File`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `File` (
  `F_id` INT NOT NULL AUTO_INCREMENT,
  `F_displayName` VARCHAR(255) NULL,
  `F_address` CHAR(55) NOT NULL,
  `F_timeStamp` BIGINT NULL DEFAULT 0,
  `F_fileSize` INT NULL DEFAULT 0,
  `F_hash` CHAR(40) NULL,
  `F_comment` VARCHAR(255) NULL,
  PRIMARY KEY (`F_id`),
  UNIQUE INDEX `F_id_UNIQUE` (`F_id` ASC),
  UNIQUE INDEX `F_hash_UNIQUE` (`F_hash` ASC),
  UNIQUE INDEX `F_address_UNIQUE` (`F_address` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- procedure deleteFile
-- -----------------------------------------------------

CREATE PROCEDURE `deleteFile` (IN fileid int(11))
BEGIN
DECLARE count char(55);
select F_address into count
from File where F_id = fileid;

Delete from File
where F_id = fileid;

SELECT 
    count as F_address;
END;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `File_ADEL`;
CREATE TRIGGER `File_ADEL` AFTER DELETE ON `File` FOR EACH ROW
/* insert fileaddress into removableFiles
@author Lisa*/
begin
#insert IGNORE into RemovableFiles 
#set F_address = OLD.F_address;
end;

DROP TRIGGER IF EXISTS `File_AINS`;
CREATE TRIGGER `File_AINS` AFTER INSERT ON `File` FOR EACH ROW
/*delete from removableFiles if address exists
@author Lisa*/
begin
#Delete From RemovableFiles where F_address = NEW.F_address
end;