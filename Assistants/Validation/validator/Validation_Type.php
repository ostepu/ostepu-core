<?php
class Validation_Type implements Validation_Interface
{
    private static $indicator = 'is';

    public static function getIndicator()
    {
        return self::$indicator;
    }

    public static function validate_is_float($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (is_float($input[$key])){
            return;
        }

        if (!is_float((float) $input[$key])){
            return false;
        }

        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^\d+\.\d+$%');
    }

    public static function validate_is_boolean($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        $boolean = filter_var($input[$key], FILTER_VALIDATE_BOOLEAN);
        if ($boolean !== null){
            return;
        }

        return false;
    }

    public static function validate_is_integer($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        if (is_int($input[$key])){
            return;
        }
        if (is_string($input[$key]) && !ctype_digit($input[$key])) {
            return false; // contains non digit characters
        }
        if (!is_int((int) $input[$key])) {
            return false; // other non-integer value or exceeds PHP_MAX_INT
        }

        return;
    }

    public static function validate_is_string($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (is_string($input[$key])) {
            return;
        }

        return false;
    }

    public static function validate_is_array($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (is_array($input[$key])) {
            return;
        }

        return false;
    }
}