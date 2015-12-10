<?php
class Selection_Elem implements Validation_Interface
{
    private static $indicator = 'elem';

    public static function getIndicator()
    {
        return self::$indicator;
    }

}