<?php
/**
 * @file Validation.php
 * Contains the Validation class
 *
 * @author Till Uhlig
 */

include_once dirname(__FILE__) . '/../../UI/include/Helpers.php';
include_once dirname(__FILE__) . '/include/Validator.php';
include_once dirname(__FILE__) . '/include/Converter.php';
include_once dirname(__FILE__) . '/../Structures.php';

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
    private $validated = null;
    
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
    
    public function resetValidationRules()
    {
        $this->validation_rules = array();
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
        $this->validated = null;
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
    
    public function addValidator($name, $callback)
    {
        
        if (is_callable('Validator::validate_'.$name) || is_callable('Converter::convert_'.$name) || isset($this->custom_Validations[$name])) {
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
        if ($this->validated !== null){
            return $this->validated;
        }
        
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
                
                if (trim($ruleName) === ''){
                    continue;
                }
                
                if (isset($this->custom_Validations[$ruleName])){
                    if (is_callable($this->custom_Validations[$ruleName])){
                        $res = call_user_func($this->custom_Validations[$ruleName], $fieldName, $this->input, $this->settings, $ruleParam);
                    } else {
                        throw new Exception("Validation '$ruleName' is not callable.");
                    }
                } elseif(is_callable('Validator::validate_'.$ruleName)) {
                    $res = call_user_func('Validator::validate_'.$ruleName, $fieldName, $this->input, $this->settings, $ruleParam);
                } elseif(is_callable('Converter::convert_'.$ruleName)) {
                    $res = call_user_func('Converter::convert_'.$ruleName, $fieldName, $this->input, $this->settings, $ruleParam);
                } else {
                    throw new Exception("Validation '$ruleName' does not exists.");
                }

                if (is_array($res) || $res === false){
                    if (isset($res['notification'])){
                        $this->notifications = array_merge($this->notifications,$res['notification']);
                    }
                    
                    if (isset($res['errors'])){
                        $this->errors = array_merge($this->errors,$res['notification']);
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
                        $this->resetValidationRules();
                        $this->validated=false;
                        return false;
                    }
                }
            }
            
            if ($validRuleSet === true){
                $this->insertValue($fieldName, (isset($this->input[$fieldName]) ? $this->input[$fieldName] : null));
            } else {
                $this->removeValue($fieldName);
            }
        }
        
        if ($this->settings['validationError'] === false){
            $this->resetValidationRules();
            $this->validated=true;
            return true;
        }
        
        $this->resetResult();
        $this->resetValidationRules();
        $this->validated=false;
        return false;
    }
    
    public function validate()
    {
        if ($this->isValid()){
            return $this->getResult();
        }
        return false;
    }
    
    public static function validateValue($value, $rules)
    {
        $f = new static(array('elem'=>$value));
        $f->addSet('elem',$rules);
        
        if ($f->isValid()){
            $value = $f->getResult();
            return new ValidationResult(true,$value['elem']);
        }
        return new ValidationResult(false,null);
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
}

class ValidationResult
{
    private $isValid = true;
    private $value = null;
    
    public function __construct($isValid=true, $value = null)
    {
        $this->isValid = $isValid;
        $this->value = $value;
    }
    
    public function isValid()
    {
        return $this->isValid;
    }
    
    public function getResult()
    {
        return $this->value;
    }
    
}