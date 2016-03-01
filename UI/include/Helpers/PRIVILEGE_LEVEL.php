<?php
/**
 * @file PRIVILEGE_LEVEL.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */


/**
 * An enumeration of different privilege levels.
 */
class PRIVILEGE_LEVEL
{
    const STUDENT = 0;
    const TUTOR = 1;
    const LECTURER = 2;
    const ADMIN = 3;
    const SUPER_ADMIN = 4;

    static $NAMES = array(
        self::STUDENT => 'Student',
        self::TUTOR => 'Tutor',
        self::LECTURER => 'Dozent',
        self::ADMIN => 'Admin');

    static $SITES = array(
        self::STUDENT => 'Student.php',
        self::TUTOR => 'Tutor.php',
        self::LECTURER => 'Lecturer.php',
        self::ADMIN => 'Admin.php');
}