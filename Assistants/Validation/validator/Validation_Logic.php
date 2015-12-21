<?php
class Validation_Logic implements Validation_Interface
{
    private static $indicator = 'logic';

    public static function getIndicator()
    {
        return self::$indicator;
    }

    public static function validate_logic_or($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || !isset($param)){
            return;
        }

        if (!isset($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', missing parameter.');
        }

        if (!is_array($param)){
            throw new Exception('Validation rule \''.__METHOD__.'\', array required as parameter.');
        }

        /// fehlermeldungen verodern ///
        /// fehlermeldungen verodern ///
        /// fehlermeldungen verodern ///
        foreach($param as $rules){
            $f = new Validation(array($key=>$input[$key]), $setting);
            $f->addSet($key,$rules);

            if ($f->isValid()){
                $res = $f->getResult()[$key];
                return array('valid'=>true, 'field'=>$key, 'value'=>$res);
            }
        }
        return false;
    }
}