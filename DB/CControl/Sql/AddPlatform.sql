DROP PROCEDURE IF EXISTS `drop_index_if_exists`;
CREATE PROCEDURE `drop_index_if_exists` (in theTable varchar(128), in theIndexName varchar(128))
begin
    IF((SELECT COUNT(*) AS index_exists FROM information_schema.statistics WHERE TABLE_SCHEMA = DATABASE() and table_name =
    theTable AND index_name = theIndexName) > 0) THEN
       SET @s = CONCAT('DROP INDEX ' , theIndexName , ' ON ' , theTable);
       PREPARE stmt FROM @s;
       EXECUTE stmt;
     END IF;
end;

DROP PROCEDURE IF EXISTS `alter_table_attribute`;
CREATE PROCEDURE `alter_table_attribute` (in theTable varchar(128), in theAttrName varchar(128), in theType varchar(128), in isNullable varchar(128), in theDefault varchar(128))
begin
    set @database = Database();
    CREATE TEMPORARY TABLE IF NOT EXISTS `ColData`
    SELECT COLUMN_NAME as 'aName', COLUMN_TYPE as 'aType', IS_NULLABLE as 'aNullable', COLUMN_DEFAULT as 'aDefault'
      FROM INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA = @database and TABLE_NAME = theTable and COLUMN_NAME = theAttrName;
     
    if ((select count(*) from `ColData`)=0) then
        set @c2 = theDefault;
        if (@c2 <> 'NULL') then
            set @c2 = concat('\'',@c2,'\'');
        end if;
        SET @s = concat('ALTER TABLE ',theTable,' ADD `',theAttrName,'` ',theType,' ',isNullable,' DEFAULT ',@c2,'');
        PREPARE stmt1 FROM @s;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;
    end if;
    
    if ((select count(*) from `ColData`)>0) then
        set @a = (select aType from `ColData` limit 1);
        set @b = (select aNullable from `ColData` limit 1);
        set @c = (select aDefault from `ColData` limit 1);
        set @a2 = lower(theType);
        set @b2 = lower(isNullable);
        set @c2 = theDefault;
        if (@b = 'YES') then
            set @b = 'null';
        else
            set @b = 'not null';
        end if;
        if (@a<>@a2 or @b<>@b2 or @c<>@c2 or (@c is NULL and @c2 <> 'NULL') or (@c is not NULL and @c2 = 'NULL')) then
            if (@c2 <> 'NULL') then
                set @c2 = concat('\'',@c2,'\'');
            end if;
            
            SET @s = concat('ALTER TABLE ',theTable,' MODIFY COLUMN `',theAttrName,'` ',@a2,' ',@b2,' DEFAULT ',@c2,'');
            PREPARE stmt1 FROM @s;
            EXECUTE stmt1;
            DEALLOCATE PREPARE stmt1;
        end if;
    end if;
    DROP TEMPORARY TABLE IF EXISTS `ColData`;
    select 1;
end;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `Component` (
  `CO_id` INT NOT NULL AUTO_INCREMENT,
  `CO_name` VARCHAR(45) NOT NULL,
  `CO_address` VARCHAR(255) NOT NULL,
  `CO_option` VARCHAR(255) NULL,
  PRIMARY KEY (`CO_id`),
  UNIQUE INDEX `CO_id_UNIQUE` (`CO_id` ASC),
  UNIQUE INDEX `CO_address_UNIQUE` (`CO_address` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `ComponentLinkage` (
  `CL_id` INT NOT NULL AUTO_INCREMENT,
  `CO_id_owner` INT NOT NULL,
  `CL_name` VARCHAR(120) NULL,
  `CL_relevanz` VARCHAR(255) NULL,
  `CO_id_target` INT NOT NULL,
  PRIMARY KEY (`CL_id`),
  UNIQUE INDEX `CL_id_UNIQUE` (`CL_id` ASC),
  CONSTRAINT `fk_ComponentLinkage_Component1`
    FOREIGN KEY (`CO_id_owner`)
    REFERENCES `Component` (`CO_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ComponentLinkage_Component2`
    FOREIGN KEY (`CO_id_target`)
    REFERENCES `Component` (`CO_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;