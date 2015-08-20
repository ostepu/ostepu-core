<?php

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