<?php
/**
 * @file DeletePlatform.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP TABLE IF EXISTS `ExerciseFileType`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `ExerciseType_BDEL`;
DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExerciseFileType`;
DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExistsPlatform`;
DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetSheetExerciseFileTypes`;
DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetAllExerciseFileTypes`;
DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExerciseExerciseFileTypes`;