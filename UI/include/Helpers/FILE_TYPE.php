<?php
/**
 * @file FILE_TYPE.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */


/*
 * An enumeration of different mime-types.
 */
class FILE_TYPE
{
    public static $mimeType = array(
        'gz' => array('application/gzip'),
        'xls' => array('application/msexcel'),
        'ppt' => array('application/mspowerpoint'),
        'doc' => array('application/msword'),
        'pdf' => array('application/pdf'),
        'ai' => array('application/postscript'),
        'eps' => array('application/postscript'),
        'ps' => array('application/postscript'),
        'htm' => array('text/html', 'application/xhtml+xml'),
        'html' => array('text/html', 'application/xhtml+xml'),
        'shtml' => array('text/html', 'application/xhtml+xml'),
        'xhtml' => array('text/html', 'application/xhtml+xml'),
        'xml' => array('application/xml', 'text/xml', 'text/xml-external-parsed-entity'),
        'gtar' => array('application/x-gtar'),
        'php' => array('application/x-httpd-php'),
        'tar' => array('application/x-tar'),
        'zip' => array('application/zip'),
        'jpg' => array('image/jpeg'),
        'png' => array('image/png'),
        'gif' => array('image/gif'),
        'csv' => array('text/comma-separated-values'),
        'css' => array('text/css'),
        'js' => array('text/javascript', 'application/x-javascript'),
        'txt' => array('text/*'),
        'img' => array('image/*'));

    /**
     * Check if FileType has a given MimeType.
     *
     * @param string $end The fileending string without ".".
     *
     * @return bool Returns true if filetype is supported.
     */
    public static function checkSupportedFileType($end) {
        return array_key_exists($end, self::$mimeType);
    }

    /**
     * Returns a mime-type to given fileending.
     *
     * @param string $end The fileending string without ".".
     *
     * @return string Returns mime-type.
     */
    public static function getMimeTypeByFileEnding($end) {
        return self::$mimeType[$end];
    }
}