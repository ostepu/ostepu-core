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

DROP TABLE IF EXISTS `ExerciseSheet`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `ExerciseSheet_BDEL`;
DROP TRIGGER IF EXISTS `ExerciseSheet_AINS`;
DROP TRIGGER IF EXISTS `ExerciseSheet_BINS`;
DROP TRIGGER IF EXISTS `ExerciseSheet_ADEL`;
DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExerciseSheetURL`;
DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExistsPlatform`;
DROP PROCEDURE IF EXISTS `DBExerciseSheetGetSheetExercises`;
DROP PROCEDURE IF EXISTS `DBExerciseSheetGetCourseExercises`;
DROP PROCEDURE IF EXISTS `DBExerciseSheetGetCourseSheets`;
DROP PROCEDURE IF EXISTS `DBExerciseSheetGetCourseSheetURLs`;
DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExerciseSheet`;