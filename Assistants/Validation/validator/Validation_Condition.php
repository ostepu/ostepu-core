<?php
class Validation_Condition {
    
    public static function validate_satisfy_exists($key, $input, $setting = null, $param = null)
    {
        return self::validate_satisfy_isset($key, $input, $setting, $param);
    }
    
    public static function validate_satisfy_not_exists($key, $input, $setting = null, $param = null)
    {
        return self::validate_satisfy_not_isset($key, $input, $setting, $param);
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
        
        return;
    }
    
    public static function validate_satisfy_not_isset($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (isset($input[$key])){
            return false;
        }
        
        return;
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
    
    public static function validate_satisfy_empty($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key])){
            return;
        }
        
        if (!empty($input[$key])){
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
    
    public static function validate_satisfy_value($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        if ($input[$key] === $param) {
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_file_exists($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]) || !isset($input[$key]['error']) || !isset($input[$key]['tmp_name']))) {
            return false;
        }
        
        if ($setting['setError']) {
            return;
        }
        
        $file = $input[$key];
        
        if ($file['error'] === 0 && file_exists($file['tmp_name'])){
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_file_isset($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]) || !isset($input[$key]['error']) || !isset($input[$key]['tmp_name']) || !isset($input[$key]['name']) || !isset($input[$key]['size']))) {
            return false;
        }
        
        if ($setting['setError']) {
            return;
        }
        
        return;
    }
    
    public static function validate_satisfy_file_error($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]['error']))) {
            return;
        }
        
        $file = $input[$key];
        
        if ($file['error'] !== 0 && $file['error'] !== 4){
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_file_no_error($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]) || !isset($input[$key]['error']))) {
            return false;
        }
        
        if ($setting['setError']) {
            return;
        }
        
        $file = $input[$key];
        
        if ($file['error'] === 0 || $file['error'] === 4){
            return;
        }
        
        return false;
    }
    
    public static function validate_satisfy_file_extension($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]) || !isset($input[$key]['tmp_name']))) {
            return;
        }
        
        if ($setting['setError']) {
            return;
        }
        
        $file = $input[$key];
        
        if (!file_exists($file['tmp_name'])){
            return false;
        }
        
        $ext = strtolower(pathinfo($file['tmp_name'], PATHINFO_EXTENSION));
       
        if (!is_array($param)){
            if ($ext === strtolower($param)){
                return;
            } else {
                return false;
            }
        } else {
            $f = new Validation(array('ext'=>$ext), $setting);
            foreach($param as $rule){
                $f->addSet('ext',$rule);
            }
        
            if ($f->isValid()){
                return;
            } else {
                return false;
            }
        }
       
        return false;
    }
    
    public static function validate_satisfy_file_mime($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]) || !isset($input[$key]['tmp_name']))) {
            return;
        }
        
        if ($setting['setError']) {
            return;
        }
        
        $file = $input[$key];
        
        if (!file_exists($file['tmp_name'])){
            return false;
        }
        
        $mime = MimeReader::get_mime($file['tmp_name']);
       
        if (!is_array($param)){
            if ($mime === strtolower($param)){
                return;
            } else {
                return false;
            }
        } else {
            $f = new Validation(array('mime'=>$mime), $setting);
            foreach($param as $rule){
                $f->addSet('mime',$rule);
            }
        
            if ($f->isValid()){
                return;
            } else {
                return false;
            }
        }
       
        return false;
    }
    
    public static function validate_satisfy_file_size($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        /// ??? ///
        throw new Exception('Validation rule \''.__METHOD__.'\' is not implemented.');
        
        return false;
    }
    
    public static function validate_satisfy_file_name($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]) || !isset($input[$key]['name']))) {
            return;
        }
        
        if ($setting['setError']) {
            return;
        }
        
        $file = $input[$key];
       
        if (!is_array($param)){
            if ($file['name'] === $param){
                return;
            } else {
                return false;
            }
        } else {
            $f = new Validation(array('name'=>$file['name']), $setting);
            foreach($param as $rule){
                $f->addSet('name',$rule);
            }
        
            if ($f->isValid()){
                return;
            } else {
                return false;
            }
        }
       
        return false;
    }
    
    public static function validate_satisfy_file_name_strict($key, $input, $setting = null, $param = null)
    {
        if (!$setting['setError'] && ( !isset($input[$key]) || !isset($input[$key]['name']))) {
            return;
        }
        
        if ($setting['setError']) {
            return;
        }
        
        $file = $input[$key];
       
        if (preg_match("%^((?!\.)[a-zA-Z0-9\\.\\-_]+)$%", $file['name']) === 1){
            return;
        } else {
            return false;
        }
        
        return false;
    }
}