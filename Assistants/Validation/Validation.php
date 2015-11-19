<?php
/**
 * @file Validation.php
 * Contains the Validation class
 *
 * @author Till Uhlig
 */

include_once dirname(__FILE__) . '/../../UI/include/Helpers.php';

class Validation {
    private $input = array();

    /**
     * The values that were found in the input.
     *
     * @var array $foundValues After evaluation this contains the values that
     * were found in the input.
     * @see Validation::validate()
     */
    private $foundValues = array();
    private $notifications = array();
    private $errors = array();
    
    private $validation_rules = array();
    private static $custom_Validations = array();
    
    private $settings = array('preRules' => array(),'postRules' => array(),'abortSetOnError'=>false,'abortValidationOnError'=>false);
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function resetResult()
    {
        $this->foundValues = array();
    }
    
    public function getResult()
    {
        return $this->foundValues;
    }
    
    public function convertRules($rules)
    {
        $tempRules = array();
        foreach($rules as $ruleName => $ruleParams){
            if (is_int($ruleName)){
                $tempRules[] = array($ruleParams, null);
            } else {
                $tempRules[] = array($ruleName, $ruleParams);
            }
        }
        return $tempRules;
    }
    
    public function addSet($fieldName, $rules)
    {
        
        if (!isset($this->validation_rules[$fieldName])){
            $this->validation_rules[$fieldName] = array_merge($this->convertRules($this->settings['preRules']),$this->convertRules($rules));
        } else {
            $this->validation_rules[$fieldName] = array_merge($this->validation_rules[$fieldName],$this->convertRules($rules));
        }
        
        return $this;
    }
    
    public function getNotifications()
    {
        return $this->notifications;
    }
    
    public function getPrintableNotifications()
    {
        $temp = array();
        $notes = $this->getNotifications();
        foreach($notes as $note){
            $temp[] = MakeNotification($note['type'],$note['text']);
        }
        return $temp;
    }
    
    public function addValidation($name, $callback)
    {
        $method = 'validate_'.$name;
        
        if (method_exists(__CLASS__, $method) || isset($this->custom_Validations[$name])) {
            throw new Exception("Validation rule '$rule' already exists.");
        }
        
        $this->custom_Validations[$name] = $callback;
        return $this;
    }
    
    /**
     * Construct a new Validation.
     *
     * @param array $input The values.
     */
    public function __construct($input=null, $settings = array())
    {
        if (isset($input)){
            $this->input = array_merge($this->input,$input);
        }
        
        $this->settings = array_merge($this->settings, $settings);
    }
    
    public function isValid($input = null)
    {
        $this->resetResult();
        
        if (isset($input)){
            $this->input = array_merge($this->input,$input);
        }
                
        $this->settings = array_merge($this->settings, array('setError' => false, 'validationError' => false));

        foreach($this->validation_rules as $fieldName => $ruleSet){            
            $setParameters = array_merge($ruleSet,$this->convertRules($this->settings['postRules']));
            
            $validRuleSet = true;
            $setResult = array();
            $this->settings['setError'] = false;

            foreach ($setParameters as $ruleId => $rule){
                $ruleName = $rule[0];
                $ruleParam = $rule[1];
                
                if (isset($this->custom_Validations[$ruleName])){
                    if (is_callable($this->custom_Validations[$ruleName])){
                        $res = call_user_func($this->custom_Validations[$ruleName], $fieldName, $this->input, $this->settings, $ruleParam);
                    } else {
                        throw new Exception("Validation '$ruleName' is not callable.");
                    }
                } elseif(is_callable(array($this,'validate_'.$ruleName))) {
                    $res = call_user_func(array($this,'validate_'.$ruleName), $fieldName, $this->input, $this->settings, $ruleParam);
                } else {
                    throw new Exception("Validation '$ruleName' does not exists.");
                }

                if (is_array($res) || $res === false){
                    if (isset($res['notification'])){
                        $this->notifications = array_merge($this->notifications,$res['notification']);
                    }
                    
                    if (!isset($res['valid']) || $res['valid'] === false){
                        $validRuleSet = false;
                        $this->settings['setError'] = true;
                        $this->settings['validationError'] = true;
                        $this->errors[] = array('field'=>$fieldName,'value'=>$this->input[$fieldName],'rule'=>$ruleName);
                    } elseif(isset($res['valid']) && $res['valid'] === true) {
                        if (isset($res['field']) && isset($res['value'])){
                            $this->input[$res['field']] = $res['value'];
                            $this->insertValue($fieldName, $res['value']);
                        }
                    }
                        
                    if ($this->settings['abortSetOnError'] || (isset($res['abortSet']) && $res['abortSet'] === true)){
                        break;
                    } elseif ($this->settings['abortValidationOnError'] || (isset($res['abortValidation']) && $res['abortValidation'] === true)){
                        $this->resetResult();
                        return false;
                    }
                }
            }
            
            if ($validRuleSet === true){
                $this->insertValue($fieldName, (isset($this->input[$fieldName]) ? $this->input[$fieldName] : null));
            }
        }
        
        if ($this->settings['validationError'] === false){
            return true;
        }
        
        $this->resetResult();
        return false;
    }
    
    public function validate()
    {
        if ($this->isValid()){
            return $this->getResult();
        }
        return false;
    }
    
    
    private function insertValue($key, $value)
    {
        $this->foundValues[$key] = $value;
    }
    
    private function removeValue($key)
    {
        if (isset($this->foundValues[$key])){
            unset($this->foundValues[$key]);
        }
    }
    
    private function isValue($key)
    {
        if (isset($this->foundValues[$key])){
            return true;
        }
        return false;
    }
    
    
    
    
    
    
    function validate_valid_email($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (!filter_var($input[$key], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return;
    }
    
    function validate_valid_url($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (!filter_var($input[$key], FILTER_VALIDATE_URL)) {
            return false;
        }
        
        return;
    }
    
    function validate_valid_hash($key, $input, $setting = null, $param = null)
    {
        return $this->validate_regex($key, $input, $setting, '%^([a-fA-F0-0]+)$%');
    }
    
    function validate_valid_md5($key, $input, $setting = null, $param = null)
    {
        return $this->validate_regex($key, $input, $setting, '%^[0-9A-Fa-f]{32}$%');
    }
    
    function validate_valid_sha1($key, $input, $setting = null, $param = null)
    {
        return $this->validate_regex($key, $input, $setting, '%^[0-9A-Fa-f]{40}$%');
    }
    
    function validate_valid_identifier($key, $input, $setting = null, $param = null)
    {
        return $this->validate_regex($key, $input, $setting, '%^([0-9_]+)$%');
    }

    function validate_valid_userName($key, $input, $setting = null, $param = null)
    {
        return $this->validate_regex($key, $input, $setting, '%^([a-zA-Z0-9äöüÄÖÜß]+)$%');
    }
    
    function validate_valid_timestamp($key, $input, $setting = null, $param = null)
    {
        return $this->validate_is_integer($key, $input, $setting, null);
    }

    function validate_required($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key]) || empty($input[$key])){
            return false;
        }
    }
    
    function validate_copy($key, $input, $setting = null, $param = null)
    {        
        if ($setting['setError'] || !isset($input[$key])) {
            return;
        }
        
        return array('valid'=>true, 'field'=>$param, 'value'=>$input[$key]);
    }
        
    function validate_clean_input($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($param) || $param === false){
            return;
        }
        
        $input = cleanInput($input);
        return;
    }
    
    function validate_default($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key])){
            return array('valid'=>true, 'field'=>$key, 'value'=>$param);
        }

        return;
    }
    
    function validate_equalsfield($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if ($input[$key] === $input[$param]) {
            return;
        }
        
        return false;
    }
    
    function validate_regex($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (!preg_match($param, $input[$key])) {
            return false;
        }
        
        return;
    }
    
    function validate_equalTo($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']|| !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if ($input[$key] !== $param){
            return false;
        }
        
        return;
    }
  
    function validate_min_numeric($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        if (is_string($input[$key]) && !ctype_digit($input[$key])) {
            return false; // contains non digit characters
        }
        
        if (!is_int((int) $input[$key])) {
            return false; // other non-integer value or exceeds PHP_MAX_INT
        }
        
        if ($input[$key]>=$param){
            return;
        }
        
        return false;
    }

    function validate_max_numeric($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (is_string($input[$key]) && !ctype_digit($input[$key])) {
            return false; // contains non digit characters
        }
        
        if (!is_int((int) $input[$key])) {
            return false; // other non-integer value or exceeds PHP_MAX_INT
        }
        
        if ($input[$key]<=$param){
            return;
        }
        
        return false;
    }
    
    function validate_exact_numeric($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (is_string($input[$key]) && !ctype_digit($input[$key])) {
            return false; // contains non digit characters
        }
        
        if (!is_int((int) $input[$key])) {
            return false; // other non-integer value or exceeds PHP_MAX_INT
        }
        
        if ($input[$key] == $param){
            return;
        }
        
        return false;
    }
    
    function validate_min_len($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input[$key]) >= (int) $param) {
                return;
            }
        } else {
            if (strlen($input[$key]) >= (int) $param) {
                return;
            }
        }
        return false;
    }

    function validate_max_len($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input[$key]) <= (int) $param) {
                return;
            }
        } else {
            if (strlen($input[$key]) <= (int) $param) {
                return;
            }
        }
        return false;
    }
    
    function validate_exact_len($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (function_exists('mb_strlen')) {
            if (mb_strlen($input[$key]) == (int) $param) {
                return;
            }
        } else {
            if (strlen($input[$key]) == (int) $param) {
                return;
            }
        }
        return false;
    }
    
    function validate_float($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key]) || empty($input[$key])) {
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
    
    function validate_is_float($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (is_float($input[$key])){
            return;
        }
        
        if (!is_float((float) $input[$key])){
            return false;
        }
        
        return $this->validate_regex($key, $input, $setting, '%^\d+\.\d+$%');
    }
    
    function validate_boolean($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key]) || empty($input[$key])) {
            return array('valid'=>true,'field'=>$key,'value'=>null);
        }

        $booleanList = array(0, 1, '0', '1', true, false);
        if (in_array($input[$key], $booleanList, true)){
            $boolResult = array(false,true,false,true,true,false);
            return array('valid'=>true,'field'=>$key,'value'=>$boolResult[$input[$key]]);
        }
        
        return false;
    }
    
    function validate_is_boolean($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }

        $booleanList = array(0, 1, '0', '1', true, false);
        if (in_array($input[$key], $booleanList, true)){
            return;
        }
        
        return false;
    }
    
    function validate_integer($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key]) || empty($input[$key])) {
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
    
    function validate_is_integer($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (is_string($input[$key]) && !ctype_digit($input[$key])) {
            return false; // contains non digit characters
        }
        if (!is_int((int) $input[$key])) {
            return false; // other non-integer value or exceeds PHP_MAX_INT
        }
        
        return;
    }
    
    function validate_string($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError']){
            return;
        }
        
        if (!isset($input[$key]) || empty($input[$key])) {
            return array('valid'=>true,'field'=>$key,'value'=>null);
        }
        
        if (is_string($input[$key])) {
            return;
        }
        
        return array('valid'=>true,'field'=>$key,'value'=>strval($input[$key]));
    }
    
    function validate_is_string($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (is_string($input[$key])) {
            return;
        }
        
        return false;
    }
 
    function validate_is_array($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key]) || empty($input[$key])) {
            return;
        }
        
        if (is_array($input[$key])) {
            return;
        }
        
        return false;
    }
  
    function validate_foreach($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($input[$key])){
            return;
        }
        
        if (!is_array($input[$key])){
            return false;
        }
        
        $allValid = true;
        $result = array();
        foreach($input[$key] as $elemName => $elem){
            $f = new static(array('key'=>$elemName, 'elem'=>$elem), $setting);
            foreach($param as $set){
                $f->addSet($set[0],$set[1]);
            }

            if ($f->isValid()){
                $result[$elemName] = $f->getResult()['elem'];
            } else {
                $this->notifications = array_merge($this->notifications,$f->getNotifications());
                $this->errors = array_merge($this->errors,$f->getErrors());
                return false;
            }
        }

        return array('valid'=>true, 'field'=>$key, 'value'=>$result);
    }
    
    function validate_useArray($key, $input, $setting = null, $param = null)
    {
        if ($setting['setError'] || !isset($param) || $param === false){
            return;
        }
        
        if (!is_array($input[$key])){
            return false;
        }
        
        $allValid = true;
        $result = array();
        $f = new static($input[$key], $setting);
        foreach($param as $set){
            $f->addSet($set[0],$set[1]);
        }
        
        if ($f->isValid()){
            return array('valid'=>true, 'field'=>$key, 'value'=>$f->getResult());
        }
        return false;
    }
    
    function validate_on_error($key, $input, $setting = null, $param = null)
    {
        if (!isset($setting['setError'])){
            return;            
        }
        
        if (!isset($param['type']) || !isset($param['text'])){
            throw new Exception(__FUNCTION__.": invalid param.");
        }
        
        if ($setting['setError'] === true){
            return array('valid'=>true,'abortSet'=>true, 'notification'=>array(array('type'=>$param['type'],'text'=>$param['text'])));
        }
        return;
    }
}
