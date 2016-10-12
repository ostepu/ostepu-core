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

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP TABLE IF EXISTS `Submission<?php echo $profile;?>`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `Submission<?php echo $profile;?>_BDEL`;
DROP TRIGGER IF EXISTS `Submission<?php echo $profile;?>_BINS`;
DROP TRIGGER IF EXISTS `Submission<?php echo $profile;?>_BUPD`;
DROP TRIGGER IF EXISTS `Submission<?php echo $profile;?>_ADEL`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetExerciseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetExistsPlatform`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupCourseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupExerciseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupSelectedCourseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupSelectedExerciseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupSelectedSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetSelectedCourseUserSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetSelectedExerciseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetSelectedSheetSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetSheetSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetSubmission`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetUserExerciseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetUserSheetSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetAllSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetCourseSubmissions`;
DROP PROCEDURE IF EXISTS `DBSubmissionGetCourseUserSubmissions`;