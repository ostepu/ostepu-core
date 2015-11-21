<?php
class Selection_Key {
    public static function select_key($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        if (is_array($param)){
            return false;
        }
        
        return array('keys'=>array($param));
    }
    
    public static function select_key_list($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        if (!is_array($param)){
            return false;
        }
        
        return array('keys'=>$param);
    }
    
    public static function select_key_all($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        return array('keys'=>$keys);
    }
    
    public static function select_key_regex($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
    
    public static function select_key_numeric($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
    
    public static function select_key_integer($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
    
    public static function select_key_min_numeric($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
    
    public static function select_key_max_numeric($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
    
    public static function select_key_starts_with($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
    
    public static function select_key_union($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
    
    public static function select_key_intersection($keys, $input, $setting = null, $param = null)
    {
        if ($setting['setError']) {
            return;
        }
        
        /// ??? ///
        
        return array('keys'=>array());
    }
}