<?php
class Validation_File {
    
    public static function validate_file_required($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        /// ??? ///
        
        return false;
    }
    
    public static function validate_file_extension($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        /// ??? ///
        /*if (is_array($check)) {
            return static::extension(array_shift($check), $extensions);
        }
        $extension = strtolower(pathinfo($check, PATHINFO_EXTENSION));
        foreach ($extensions as $value) {
            if ($extension === strtolower($value)) {
                return true;
            }
        }*/
        return false;
    }
    
    public static function validate_file_mime($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        /// ??? ///
        
        return false;
    }
    
    public static function validate_file_name($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        /// ??? ///
        
        return false;
    }
    
    public static function validate_file_name_strict($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        /// ??? ///
        
        return false;
    }
}