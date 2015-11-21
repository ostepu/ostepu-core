<?php
class Validation_Set {
          
    public static function validate_set_default($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key])){
            return array('valid'=>true, 'field'=>$key, 'value'=>$param);
        }

        return;
    }
    
    public static function validate_set_copy($key, $input, $setting = null, $param = null)
    {        
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        return array('valid'=>true, 'field'=>$param, 'value'=>$input[$key]);
    }
    
    public static function validate_set_value($key, $input, $setting = null, $param = null)
    {        
        if ($setting['setError']) {
            return;
        }
        
        return array('valid'=>true, 'field'=>$key, 'value'=>$parm);
    }
}