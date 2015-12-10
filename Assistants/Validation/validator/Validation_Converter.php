<?php
class Validation_Converter implements Validation_Interface
{
    private static $indicator = 'to';

    public static function getIndicator()
    {
        return self::$indicator;
    }

    public static function validate_to_float($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        if (empty($input[$key])) {
            return array('valid'=>true,'field'=>$key,'value'=>null);
        }

        if (is_float($input[$key])){
            return;
        }

        if (is_int($input[$key])){
            return array('valid'=>true,'field'=>$key,'value'=>floatval($input[$key]));
        }

        if (preg_match('%^\\d+\\.\\d+$%', $input[$key]) && is_float((float) $input[$key])){
            return array('valid'=>true,'field'=>$key,'value'=>floatval($input[$key]));           
        }

        return false;
    }

    public static function validate_to_string($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        if (empty($input[$key])) {
            return array('valid'=>true,'field'=>$key,'value'=>null);
        }

        if (is_string($input[$key])) {
            return;
        }

        return array('valid'=>true,'field'=>$key,'value'=>strval($input[$key]));
    }

    public static function validate_to_lower($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        if (empty($input[$key])) {
            return;
        }

        $var = $input[$key];

        if (!is_string($var)) {
            $var = strval($var);
        }

        return array('valid'=>true,'field'=>$key,'value'=>strtolower($var));
    }

    public static function validate_to_upper($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        if (empty($input[$key])) {
            return;
        }

        $var = $input[$key];

        if (!is_string($var)) {
            $var = strval($var);
        }

        return array('valid'=>true,'field'=>$key,'value'=>strtoupper($var));
    }

    public static function validate_to_integer($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        if (empty($input[$key])) {
            return array('valid'=>true,'field'=>$key,'value'=>null);
        }

        if (is_string($input[$key]) && !ctype_digit($input[$key])) {
            return false; // contains non digit characters
        }
        if (!is_int((int) $input[$key])) {
            return false; // other non-integer value or exceeds PHP_MAX_INT
        }

        return array('valid'=>true,'field'=>$key,'value'=>intval($input[$key]));
    }

    public static function validate_to_boolean($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        if (empty($input[$key])) {
            return array('valid'=>true,'field'=>$key,'value'=>null);
        }

        $boolean = filter_var($input[$key], FILTER_VALIDATE_BOOLEAN);
        if ($boolean !== null){
            return array('valid'=>true,'field'=>$key,'value'=>$boolean);
        }

        return false;
    }

    public static function validate_to_md5($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        return array('valid'=>true,'field'=>$key,'value'=>md5($input[$key]));
    }

    public static function validate_to_sha1($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        return array('valid'=>true,'field'=>$key,'value'=>sha1($input[$key]));
    }

    public static function validate_to_base64($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        $obj = @base64_encode($input[$key]);

        if ($obj === false){
            return false;
        }

        return array('valid'=>true,'field'=>$key,'value'=>$obj);
    }

    public static function validate_to_string_from_base64($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }

        $obj = @base64_decode($input[$key]);

        if ($obj === false){
            return false;
        }

        return array('valid'=>true,'field'=>$key,'value'=>$obj);
    }

    public static function validate_to_object_from_json($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }

        if (!isset($input[$key])) {
            return;
        }

        $obj = @json_decode($input[$key]);

        if ($obj === null){
            return false;
        }

        return array('valid'=>true,'field'=>$key,'value'=>$obj);
    }

    public static function validate_to_array_from_json($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }

        if (!isset($input[$key])) {
            return;
        }

        $obj = @json_decode($input[$key], true);

        if ($obj === null){
            return false;
        }

        return array('valid'=>true,'field'=>$key,'value'=>$obj);
    }

    public static function validate_to_json($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }

        if (!isset($input[$key])) {
            return;
        }

        $obj = @json_encode($input[$key]);

        if ($obj === false){
            return false;
        }

        return array('valid'=>true,'field'=>$key,'value'=>$obj);
    }
}