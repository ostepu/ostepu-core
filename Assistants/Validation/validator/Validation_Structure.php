<?php
class Validation_Structure implements Validation_Interface
{
    private static $indicator = 'valid';

    public static function getIndicator()
    {
        return self::$indicator;
    }

    public static function validate_valid_email($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (!filter_var($input[$key], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return;
    }

    public static function validate_valid_url($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (parse_url($input[$key]) === false) {
            return false;
        }

        return;
    }

    public static function validate_valid_url_query($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        $var = parse_url($input[$key]);

        if ($var === false) {
            return false;
        }

        if (isset($var['path'])) unset($var['path']);
        if (isset($var['query'])) unset($var['query']);

        if (!empty($var)){
            return false;
        }

        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '/^[a-zA-Z0-9+&@#\/%?=~_|!:,.;]*[a-zA-Z0-9+&@#\/%=~_|]$/i');
    }

    public static function validate_valid_regex($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (!filter_var($input[$key], FILTER_VALIDATE_REGEXP)) {
            return false;
        }

        return;
    }

    public static function validate_valid_hash($key, $input, $setting = null, $param = null)
    {
        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^([a-fA-F0-0]+)$%');
    }

    public static function validate_valid_md5($key, $input, $setting = null, $param = null)
    {
        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^[0-9A-Fa-f]{32}$%');
    }

    public static function validate_valid_sha1($key, $input, $setting = null, $param = null)
    {
        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^[0-9A-Fa-f]{40}$%');
    }

    public static function validate_valid_identifier($key, $input, $setting = null, $param = null)
    {
        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^([0-9_]+)$%');
    }

    public static function validate_valid_user_name($key, $input, $setting = null, $param = null)
    {
        return Validation_Condition::validate_valid_userName($key, $input, $setting, $param);
    }

    public static function validate_valid_userName($key, $input, $setting = null, $param = null)
    {
        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^([a-zA-Z0-9äöüÄÖÜß]+)$%');
    }

    public static function validate_valid_timestamp($key, $input, $setting = null, $param = null)
    {
        return self::validate_valid_integer($key, $input, $setting, null);
    }

    public static function validate_valid_alpha($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (!is_string($input[$key])){
            return false;
        }

        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^([a-zA-Z]+)$%');
    }

    public static function validate_valid_alpha_space($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (!is_string($input[$key])){
            return false;
        }

        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^([a-zA-Z\h]+)$%');
    }

    public static function validate_valid_integer($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
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

    public static function validate_valid_alpha_numeric($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (!is_string($input[$key])){
            return false;
        }

        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^([0-9a-zA-Z]+)$%');
    }

    public static function validate_valid_alpha_space_numeric($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (!is_string($input[$key])){
            return false;
        }

        return Validation_Condition::validate_satisfy_regex($key, $input, $setting, '%^([0-9a-zA-Z\h]+)$%');
    }

    public static function validate_valid_json($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }

        $temp = @json_decode($input[$key]);

        if ($temp === null){
            return false;
        }

        return;
    }
}