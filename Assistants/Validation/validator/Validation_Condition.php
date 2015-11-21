<?php
class Validation_Condition {
    
    public static function validate_satisfy_exists($key, $input, $setting = null, $param = null)
    {
        return self::validate_satisfy_isset($key, $input, $setting, $param);
    }
    
    public static function validate_satisfy_required($key, $input, $setting = null, $param = null)
    {
        return self::validate_satisfy_isset($key, $input, $setting, $param);
    }
    
    public static function validate_satisfy_isset($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key])){
            return false;
        }
    }
    
    public static function validate_satisfy_not_empty($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key])){
            return;
        }
        
        if (empty($input[$key])){
            return false;
        }
        return;
    }
    
    public static function validate_satisfy_equals_field($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        if ((isset($input[$key]) && !isset($input[$param])) || (!isset($input[$key]) && isset($input[$param]))){
            return false;
        }
        
        if (!isset($input[$key]) && !isset($input[$param])){
            return;
        }
        
        if ($input[$key] === $input[$param]) {
            return;
        }
        
        return false;
    }
        
    public static function validate_satisfy_not_equals_field($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if ((isset($input[$key]) && !isset($input[$param])) || (!isset($input[$key]) && isset($input[$param]))){
            return;
        }
        
        if (!isset($input[$key]) && !isset($input[$param])){
            return false;
        }
        
        if ($input[$key] !== $input[$param]) {
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_regex($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (preg_match($param, $input[$key]) === 0) {
            return false;
        }
        
        return;
    }
    
    public static function validate_satisfy_equalTo($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']|| !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if ($input[$key] !== $param){
            return false;
        }
        
        return;
    }
  
    public static function validate_satisfy_min_numeric($key, $input, $setting = null, $param = null)
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
        
        if ($input[$key]>=$param){
            return;
        }
        
        return false;
    }

    public static function validate_satisfy_max_numeric($key, $input, $setting = null, $param = null)
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
        
        if ($input[$key]<=$param){
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_exact_numeric($key, $input, $setting = null, $param = null)
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
        
        if ($input[$key] == $param){
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_min_len($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input[$key]) >= (int) $param) {
                return;
            }
        } else {
            if (strlen($input[$key]) >= (int) $param) {
                return;
            }
        }
        return false;
    }

    public static function validate_satisfy_max_len($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input[$key]) <= (int) $param) {
                return;
            }
        } else {
            if (strlen($input[$key]) <= (int) $param) {
                return;
            }
        }
        return false;
    }
    
    public static function validate_satisfy_exact_len($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input[$key]) == (int) $param) {
                return;
            }
        } else {
            if (strlen($input[$key]) == (int) $param) {
                return;
            }
        }
        return false;
    }
  
    public static function validate_satisfy_in_list($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        if (in_array($input[$key], $param)) {
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_not_in_list($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        if (!in_array($input[$key], $param)) {
            return;
        }
        
        return false;
    }
}