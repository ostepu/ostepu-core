<?php
class DefaultNormalizer
{
    public static function normalizeText($text){
        $text = mb_convert_case($text, MB_CASE_LOWER, 'UTF-8');
        $text = trim($text);

        return utf8_decode($text); 
    }
}
?>