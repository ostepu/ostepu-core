<?php
class Validator {
    
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
        
        if (!filter_var($input[$key], FILTER_VALIDATE_URL)) {
            return false;
        }
        
        return;
    }
    
    public static function validate_valid_hash($key, $input, $setting = null, $param = null)
    {
        return self::validate_regex($key, $input, $setting, '%^([a-fA-F0-0]+)$%');
    }
    
    public static function validate_valid_md5($key, $input, $setting = null, $param = null)
    {
        return self::validate_regex($key, $input, $setting, '%^[0-9A-Fa-f]{32}$%');
    }
    
    public static function validate_valid_sha1($key, $input, $setting = null, $param = null)
    {
        return self::validate_regex($key, $input, $setting, '%^[0-9A-Fa-f]{40}$%');
    }
    
    public static function validate_valid_identifier($key, $input, $setting = null, $param = null)
    {
        return self::validate_regex($key, $input, $setting, '%^([0-9_]+)$%');
    }

    public static function validate_valid_userName($key, $input, $setting = null, $param = null)
    {
        return self::validate_regex($key, $input, $setting, '%^([a-zA-Z0-9äöüÄÖÜß]+)$%');
    }
    
    public static function validate_valid_timestamp($key, $input, $setting = null, $param = null)
    {
        return self::validate_is_integer($key, $input, $setting, null);
    }

    public static function validate_required($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key]) || empty($input[$key])){
            return false;
        }
    }
    
    public static function validate_copy($key, $input, $setting = null, $param = null)
    {        
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        return array('valid'=>true, 'field'=>$param, 'value'=>$input[$key]);
    }
        
    public static function validate_clean_input($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($param) || $param === false){
            return;
        }
        
        $input = cleanInput($input);
        return;
    }
    
    public static function validate_default($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key])){
            return array('valid'=>true, 'field'=>$key, 'value'=>$param);
        }

        return;
    }
    
    public static function validate_equalsfield($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if ($input[$key] === $input[$param]) {
            return;
        }
        
        return false;
    }
    
    public static function validate_regex($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (!preg_match($param, $input[$key])) {
            return false;
        }
        
        return;
    }
    
    public static function validate_equalTo($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']|| !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if ($input[$key] !== $param){
            return false;
        }
        
        return;
    }
  
    public static function validate_min_numeric($key, $input, $setting = null, $param = null)
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

    public static function validate_max_numeric($key, $input, $setting = null, $param = null)
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
    
    public static function validate_exact_numeric($key, $input, $setting = null, $param = null)
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
    
    public static function validate_min_len($key, $input, $setting = null, $param = null)
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

    public static function validate_max_len($key, $input, $setting = null, $param = null)
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
    
    public static function validate_exact_len($key, $input, $setting = null, $param = null)
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
        
        return self::validate_regex($key, $input, $setting, '%^\d+\.\d+$%');
    }
    
    public static function validate_is_alpha($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (!is_string($input[$key])){
            return false;
        }
        
        return self::validate_regex($key, $input, $setting, '%^([a-zA-Z]+)$%');
    }
    
    public static function validate_is_alpha_numeric($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (!is_string($input[$key])){
            return false;
        }
        
        return self::validate_regex($key, $input, $setting, '%^([0-9sa-zA-Z]+)$%');
    }
        
    public static function validate_is_boolean($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        $booleanList = array(0, 1, '0', '1', true, false);
        if (in_array($input[$key], $booleanList, true)){
            return;
        }
        
        return false;
    }
    
    public static function validate_is_integer($key, $input, $setting = null, $param = null)
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
  
    public static function validate_foreach($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }
        
        if (!is_array($input[$key])){
            return false;
        }
        
        $allValid = true;
        $result = array();
        foreach($input[$key] as $elemName => $elem){
            $f = new Validation(array('key'=>$elemName, 'elem'=>$elem), $setting);
            foreach($param as $set){
                $f->addSet($set[0],$set[1]);
            }

            if ($f->isValid()){
                $result[$elemName] = $f->getResult()['elem'];
            } else {
                return array('valid'=>false, 'notifications'=>$f->getNotifications(), 'errors'=>$f->getErrors());
            }
        }

        return array('valid'=>true, 'field'=>$key, 'value'=>$result);
    }
    
    public static function validate_useArray($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($param) || $param === false){
            return;
        }
        
        if (!is_array($input[$key])){
            return false;
        }
        
        $allValid = true;
        $result = array();
        $f = new Validation($input[$key], $setting);
        foreach($param as $set){
            $f->addSet($set[0],$set[1]);
        }
        
        if ($f->isValid()){
            return array('valid'=>true, 'field'=>$key, 'value'=>$f->getResult());
        } else {
            return array('valid'=>false, 'notifications'=>$f->getNotifications(), 'errors'=>$f->getErrors());
        }
        return false;
    }
    
    public static function validate_on_error($key, $input, $setting = null, $param = null)
    {
        if (!isset($setting['setError'])){
            return;            
        }
        
        if (!isset($param['type']) || !isset($param['text'])){
            throw new Exception(__FUNCTION__.": invalid param.");
        }
        
        if ($setting['setError'] === true){
            return array('valid'=>true,'abortSet'=>true, 'notification'=>array(array('type'=>$param['type'],'text'=>$param['text'])));
        }
        return;
    }
}