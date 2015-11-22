<?php
class Validation_Perform {
    public static function validate_perform_foreach($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }
        
        if (!is_array($input[$key])){
            return false;
        }
        
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
    
    public static function validate_perform_array($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || !isset($param)){
            return;
        }
        
        if (!is_array($input[$key])){
            return false;
        }
        
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
    
    public static function validate_perform_switch_case($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || !isset($param)){
            return;
        }
        
        /// ??? ///
        
        foreach ($param as $case){
            
        }
        
        return;
    }
}