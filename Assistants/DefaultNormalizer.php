<?php
/**
 * @file DefaultNormalizer.php contains the DefaultNormalizer class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

/**
 * the class provides functions for text normalization
 */
class DefaultNormalizer
{
    /**
     * trims the text and converts it to lowercase
     *
     * @param string $text the text which should be normalized
     *
     * @return the normalized text
     */
    public static function normalizeText($text){
        $text = mb_convert_case($text, MB_CASE_LOWER, 'UTF-8');
        $text = trim($text);

        return utf8_decode($text); 
    }
}