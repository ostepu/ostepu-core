<?php


/**
 * @file DefaultNormalizer.php contains the DefaultNormalizer class
 *
 * @author Till Uhlig
 * @date 2014
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