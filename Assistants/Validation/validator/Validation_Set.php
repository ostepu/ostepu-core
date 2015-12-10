<?php
class Validation_Set implements Validation_Interface
{
    private static $indicator = 'set';

    public static function getIndicator()
    {
        return self::$indicator;
    }

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

        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
        }

        return array('valid'=>true, 'field'=>$param, 'value'=>$input[$key]);
    }

    public static function validate_set_value($key, $input, $setting = null, $param = null)
    {       
        if ($setting['setError']) {
            return;
        }

        return array('valid'=>true, 'field'=>$key, 'value'=>$param);
    }

    public static function validate_set_field_value($key, $input, $setting = null, $param = null)
    {       
        if ($setting['setError']) {
            return;
        }

        if (!isset($param['field'])){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing \'field\'.');
        }

        if (!isset($param['value'])){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing \'value\'.');
        }

        return array('valid'=>true, 'field'=>$key, 'value'=>$param);
    }

    public static function validate_set_error($key, $input, $setting = null, $param = null)
    {  
        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
        }

        $setting['setError'] = $param;

        return;
    }
}