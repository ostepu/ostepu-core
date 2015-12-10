<?php
class Validation_Perform implements Validation_Interface
{
    private static $indicator = 'perform';

    public static function getIndicator()
    {
        return self::$indicator;
    }

    public static function validate_perform_this_foreach($key, $input, $setting = null, $param = null)
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

    public static function validate_perform_foreach($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input)){
            return;
        }

        if (!is_array($input)){
            return false;
        }

        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
        }

        $result = array();
        foreach($input as $elemName => $elem){
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

        return array('valid'=>true, 'fields'=>$result);
    }

    public static function validate_perform_this_array($key, $input, $setting = null, $param = null)
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

    public static function validate_perform_array($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input) || !isset($param)){
            return;
        }

        if (!is_array($input)){
            return false;
        }

        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
        }

        $f = new Validation($input, $setting);
        foreach($param as $set){
            $f->addSet($set[0],$set[1]);
        }

        if ($f->isValid()){
            return array('valid'=>true, 'fields'=>$f->getResult());
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

        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
        }

        if (!is_array($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', parameter should be an array.');
        }

        foreach ($param as $case){
            $condition = $case[0]; // set (selector, rules)
            $rules = $case[1]; // rules only

            $satisfied = false;
            if (is_string($condition)){
                if ($input[$key] === $condition){
                    $satisfied = true;
                }
            } else {
                $f = new Validation($input, $setting);
                foreach($condition as $set){
                    $f->addSet($set[0],$set[1]);
                }
                if ($f->isValid()){
                    $satisfied = true;
                }
            }

            if ($satisfied){
                $f = new Validation($input, $setting);
                foreach($rules as $set){
                    $f->addSet($key,$set);
                }

                if ($f->isValid()){
                    $r = $f->getResult();
                    if (isset($r[$key])){
                        $r = $r[$key];
                    } else {
                      $r = null; 
                    }
                    return array('valid'=>true, 'field'=>$key, 'value'=>$r);
                } else {
                    return array('valid'=>false, 'notifications'=>$f->getNotifications(), 'errors'=>$f->getErrors());
                }
            }
        }

        return;
    }
}