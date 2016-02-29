<?php
/**
 * @file AddPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

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

DROP PROCEDURE IF EXISTS `execute_if_index_not_exists`;
CREATE PROCEDURE `execute_if_index_not_exists` (in theTable varchar(128), in theIndexName varchar(128), in theStatement varchar(255))
begin
    IF((SELECT COUNT(*) AS index_exists FROM information_schema.statistics WHERE TABLE_SCHEMA = DATABASE() and table_name =
    theTable AND index_name = theIndexName) = 0) THEN
       SET @s = theStatement;
       PREPARE stmt FROM @s;
       EXECUTE stmt;
     END IF;
end;

DROP PROCEDURE IF EXISTS `execute_if_table_exists`;
CREATE PROCEDURE `execute_if_table_exists` (in theTable varchar(128), in theStatement varchar(255))
begin
    set @database = Database();
    IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA = @database and TABLE_NAME = theTable) > 0) THEN
       SET @s = theStatement;
       PREPARE stmt FROM @s;
       EXECUTE stmt;
     END IF;
end;

DROP PROCEDURE IF EXISTS `drop_constraint_if_exists`;
CREATE PROCEDURE `drop_constraint_if_exists` (in theTable varchar(255), in theForeignKey varchar(255))
begin
    set @database = Database();
    if((SELECT count(*) FROM information_schema.TABLE_CONSTRAINTS WHERE
            CONSTRAINT_SCHEMA = @database AND
            TABLE_NAME        = theTable AND
            CONSTRAINT_NAME   = theForeignKey) >0) THEN
               SET @s = CONCAT('ALTER TABLE `' , theTable , '` DROP FOREIGN KEY ' , theForeignKey);
               PREPARE stmt FROM @s;
               EXECUTE stmt;
    END IF;
end;

DROP PROCEDURE IF EXISTS `execute_if_column_not_exists`;
CREATE PROCEDURE `execute_if_column_not_exists` (in theTable varchar(128), in theColumnName varchar(128), in theStatement varchar(255))
begin
    set @database = Database();
    IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA = @database and TABLE_NAME = theTable and COLUMN_NAME = theColumnName) = 0) THEN
       SET @s = theStatement;
       PREPARE stmt FROM @s;
       EXECUTE stmt;
     END IF;
end;

DROP PROCEDURE IF EXISTS `execute_if_column_exists`;
CREATE PROCEDURE `execute_if_column_exists` (in theTable varchar(128), in theColumnName varchar(128), in theStatement varchar(255))
begin
    set @database = Database();
    IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA = @database and TABLE_NAME = theTable and COLUMN_NAME = theColumnName) > 0) THEN
       SET @s = theStatement;
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

CREATE TABLE IF NOT EXISTS `Component` (
  `CO_id` INT NOT NULL AUTO_INCREMENT,
  `CO_name` VARCHAR(45) NOT NULL,
  `CO_address` VARCHAR(255) NOT NULL,
  `CO_option` VARCHAR(255) NULL,
  PRIMARY KEY (`CO_id`),
  UNIQUE INDEX `CO_id_UNIQUE` (`CO_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;

call drop_index_if_exists('Component','CO_address_UNIQUE');
call drop_index_if_exists('Component','CO_name_35');
call drop_index_if_exists('Component','CO_name_34');
call drop_index_if_exists('Component','CO_name_33');
call drop_index_if_exists('Component','CO_name_32');
call drop_index_if_exists('Component','CO_name_31');
call drop_index_if_exists('Component','CO_name_30');
call drop_index_if_exists('Component','CO_name_29');
call drop_index_if_exists('Component','CO_name_28');
call drop_index_if_exists('Component','CO_name_27');
call drop_index_if_exists('Component','CO_name_26');
call drop_index_if_exists('Component','CO_name_25');
call drop_index_if_exists('Component','CO_name_24');
call drop_index_if_exists('Component','CO_name_23');
call drop_index_if_exists('Component','CO_name_22');
call drop_index_if_exists('Component','CO_name_21');
call drop_index_if_exists('Component','CO_name_20');
call drop_index_if_exists('Component','CO_name_19');
call drop_index_if_exists('Component','CO_name_18');
call drop_index_if_exists('Component','CO_name_17');
call drop_index_if_exists('Component','CO_name_16');
call drop_index_if_exists('Component','CO_name_15');
call drop_index_if_exists('Component','CO_name_14');
call drop_index_if_exists('Component','CO_name_13');
call drop_index_if_exists('Component','CO_name_12');
call drop_index_if_exists('Component','CO_name_11');
call drop_index_if_exists('Component','CO_name_10');
call drop_index_if_exists('Component','CO_name_9');
call drop_index_if_exists('Component','CO_name_8');
call drop_index_if_exists('Component','CO_name_7');
call drop_index_if_exists('Component','CO_name_6');
call drop_index_if_exists('Component','CO_name_5');
call drop_index_if_exists('Component','CO_name_4');
call drop_index_if_exists('Component','CO_name_3');
call drop_index_if_exists('Component','CO_name_2');
call execute_if_index_not_exists('Component','CO_name','ALTER TABLE `Component` ADD UNIQUE(`CO_name` ASC);');
call execute_if_column_not_exists('Component','CO_def','ALTER TABLE `Component` ADD COLUMN CO_def VARCHAR(255) NOT NULL DEFAULT \'\';');
call execute_if_column_not_exists('Component','CO_status','ALTER TABLE `Component` ADD COLUMN CO_status int NOT NULL DEFAULT 1;');


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

call execute_if_column_not_exists('ComponentLinkage','CL_priority','ALTER TABLE `ComponentLinkage` ADD COLUMN CL_priority int NOT NULL DEFAULT 100;');
call execute_if_column_not_exists('ComponentLinkage','CL_path','ALTER TABLE `ComponentLinkage` ADD COLUMN CL_path VARCHAR(255) NOT NULL DEFAULT \'\';');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;