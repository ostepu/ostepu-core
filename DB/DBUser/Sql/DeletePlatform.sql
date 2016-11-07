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

DROP TABLE IF EXISTS `User<?php echo $profile;?>`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP TRIGGER IF EXISTS `User_BUPD`;
DROP TRIGGER IF EXISTS `User_AUPD`;
DROP TRIGGER IF EXISTS `User_ADEL`;
DROP PROCEDURE IF EXISTS `DBUserGetCourseMember`;
DROP PROCEDURE IF EXISTS `DBUserGetCourseUserByStatus`;
DROP PROCEDURE IF EXISTS `DBUserGetExistsPlatform`;
DROP PROCEDURE IF EXISTS `DBUserGetGroupMember`;
DROP PROCEDURE IF EXISTS `DBUserGetIncreaseUserFailedLogin`;
DROP PROCEDURE IF EXISTS `DBUserGetUser`;
DROP PROCEDURE IF EXISTS `DBUserGetUserByStatus`;
DROP PROCEDURE IF EXISTS `DBUserGetUsers`;