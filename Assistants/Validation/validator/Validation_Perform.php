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
        
        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
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
        
        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
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
        throw new Exception('Validation rule \''.__METHOD__.'\' is not implemented.');
        
        foreach ($param as $case){
            $condition = $case[0];
            $rules = $case[1];
            
            $satisfied = false;
            if (is_string($condition)){
                if ($input[$key] === $condition){
                    $satisfied = true;
                }
            } else {
                $f = new Validation($input[$key], $setting);
                foreach($param as $set){
                    $f->addSet($set[0],$set[1]);
                }
            }
            
            if ($satisfied){
                
                break;
            }
        }
        
        return;
    }
}