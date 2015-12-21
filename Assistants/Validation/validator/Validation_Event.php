<?php
class Validation_Event implements Validation_Interface
{
    private static $indicator = 'on';

    public static function getIndicator()
    {
        return self::$indicator;
    }

    public static function validate_on_error($key, $input, $setting = null, $param = null)
    {
        if (!isset($setting['setError'])){
            return;           
        }

        if ($setting['setError'] === true){
            $result = array('valid'=>true);
            if (isset($param['type']) && isset($param['text'])){
                $result['notification'] = array(array('type'=>$param['type'],'text'=>$param['text']));
            }

            if (isset($param['value'])){
                $result['field'] = $key;
                $result['field'] = $param['value'];
            }

            if (!isset($param['abortSet']) || $param['abortSet'] === true){
                $result['abortSet'] = true;
            } else {
                $result['abortSet'] = false;
            }

            return $result;
        }
        return;
    }

    public static function validate_on_no_error($key, $input, $setting = null, $param = null)
    {
        return self::validate_on_success($key, $input, $setting, $param);
    }

    public static function validate_on_success($key, $input, $setting = null, $param = null)
    {
        if (!isset($setting['setError'])){
            return;           
        }

        if ($setting['setError'] !== true){
            $result = array('valid'=>true);
            if (isset($param['type']) && isset($param['text'])){
                $result['notification'] = array(array('type'=>$param['type'],'text'=>$param['text']));
            }

            if (isset($param['value'])){
                $result['field'] = $key;
                $result['field'] = $param['value'];
            }

            if (isset($param['abortSet']) && $param['abortSet'] === true){
                $result['abortSet'] = true;
            } else {
                $result['abortSet'] = false;
            }

            return $result;
        }
        return;
    }
}