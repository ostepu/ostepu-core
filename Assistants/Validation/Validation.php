<?php
/**
 * @file Validation.php
 * Contains the Validation class
 *
 * @author Till Uhlig
 */

include_once dirname(__FILE__) . '/Validation_Interface.php';
include_once dirname(__FILE__) . '/validator/Validation_Structure.php';
include_once dirname(__FILE__) . '/validator/Validation_Converter.php';
include_once dirname(__FILE__) . '/validator/Validation_Event.php';
include_once dirname(__FILE__) . '/validator/Validation_Perform.php';
include_once dirname(__FILE__) . '/validator/Validation_Logic.php';
include_once dirname(__FILE__) . '/validator/Validation_Set.php';
include_once dirname(__FILE__) . '/validator/Validation_Sanitize.php';
include_once dirname(__FILE__) . '/validator/Validation_Type.php';
include_once dirname(__FILE__) . '/validator/Validation_Condition.php';
include_once dirname(__FILE__) . '/selector/Selection_Key.php';
include_once dirname(__FILE__) . '/selector/Selection_Elem.php';

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
    private $custom_Validation = array();
    private $custom_Validation_Classes = array();
    private $custom_Selection = array();
    private $custom_Selection_Classes = array();

    private $settings = array('preRules' => array(),'postRules' => array(),'abortSetOnError'=>false,'abortValidationOnError'=>false);

    public function getErrors()
    {
        return $this->errors;
    }

    public function resetErrors()
    {
        $this->errors = array();
        return $this;
    }

    public function resetResult()
    {
        $this->foundValues = array();
        return $this;
    }

    public function resetValidationRules()
    {
        $this->validation_rules = array();
        return $this;
    }

    public function getResult()
    {
        return $this->foundValues;
    }

    private function convertRules($rules)
    {
        $tempRules = array();
        if (!is_array($rules)){
            $rules = array($rules);
        }

        foreach($rules as $ruleName => $ruleParams){
            if (is_int($ruleName)){
                $tempRules[] = array($ruleParams, null);
            } else {
                $tempRules[] = array($ruleName, $ruleParams);
            }
        }
        return $tempRules;
    }
    private function convertSelector($rules)
    {
        $tempRules = array();
        if (!is_array($rules)){
            $rules = array('key'=>$rules);
        }

        foreach($rules as $ruleName => $ruleParams){
            if (is_int($ruleName)){
                $tempRules[] = array($ruleParams, null);
            } else {
                $tempRules[] = array($ruleName, $ruleParams);
            }
        }
        return $tempRules;
    }

    public function addSet($fieldNames, $rules)
    {
        $convertedSelector = $this->convertSelector($fieldNames);
        $name = md5(json_encode($convertedSelector));

        if (!isset($this->validation_rules[$name])){
            $this->validation_rules[$name] = array($convertedSelector, array_merge($this->convertRules($this->settings['preRules']),$this->convertRules($rules)));
        } else {
            $this->validation_rules[$name][1] = array_merge($this->validation_rules[$name][1], $this->convertRules($rules));
        }

        $this->validated = null;
        return $this;
    }

    public function getNotifications()
    {
        return $this->notifications;
    }

    public function getPrintableNotifications($callback)
    {
        $temp = array();
        $notes = $this->getNotifications();
        foreach($notes as $note){
            $temp[] = $callback($note['type'],$note['text']);
        }
        return $temp;
    }

    public function resetNotifications()
    {
        $this->notifications = array();
        return $this;
    }

    private static $validatorClasses = array('Validation_Structure'=>null, 'Validation_Converter'=>null, 'Validation_Event'=>null, 'Validation_Set'=>null, 'Validation_Perform'=>null, 'Validation_Sanitize'=>null, 'Validation_Condition'=>null, 'Validation_Type'=>null, 'Validation_Logic'=>null);
    private static $selectionClasses = array('Selection_Key'=>null, 'Selection_Elem'=>null);

    public function addValidator($name, $callback)
    {
        if (isset($this->custom_Validation[$name])) {
            throw new Exception("Validation rule '{$methodName}' already exists (custom).");
        }

        $methods = array();
        foreach(self::$validatorClasses as $class => $indicator){
            $methods = array_merge($methods, get_class_methods($class));           
        }

        foreach($this->custom_Validation_Classes as $class){
            $methods = array_merge($methods, get_class_methods($class));           
        }

        $methodName = 'validate_'.$name;

        if (in_array($methodName, $methods)){
            throw new Exception("Validation rule '{$methodName}' already exists (static).");
        }

        $this->custom_Validation[$name] = $callback;
        return $this;
    }

    public function addValidationClass($name)
    {
        $this->custom_Validation_Classes[$name] = $name::getIndicator();
        return $this;
    }

    public function addSelector($name, $callback)
    {
        if (isset($this->custom_Selection[$name])) {
            throw new Exception("Selector rule '{$methodName}' already exists (custom).");
        }

        $methods = array();
        foreach(self::$selectionClasses as $class => $indicator){
            $methods = array_merge($methods, get_class_methods($class));           
        }

        foreach($this->custom_Selection_Classes as $class){
            $methods = array_merge($methods, get_class_methods($class));           
        }

        $methodName = 'select_'.$name;

        if (in_array($methodName, $methods)){
            throw new Exception("Selector rule '{$methodName}' already exists (static).");
        }

        $this->custom_Selection[$name] = $callback;
        return $this;
    }

    public function addSelectionClass($name)
    {
        $this->custom_Selection_Classes[$name] = $name::getIndicator();
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

        foreach(self::$validatorClasses as $class => $indicator){
           self::$validatorClasses[$class] = $class::getIndicator();
        }

        foreach(self::$selectionClasses as $class => $indicator){
           self::$selectionClasses[$class] = $class::getIndicator();
        }

        return $this;
    }

    public static function open($input=null, $settings = array())
    {
        $temp = new Validation($input, $settings);

        return $temp;
    }

    public function close()
    {
        $this->input = array();   
        $this->validation_rules = array();
        $this->custom_Validation = array();
        $this->custom_Validation_Classes = array();
        $this->custom_Selection = array();
        $this->custom_Selection_Classes = array();
        $this->settings = array('preRules' => array(),'postRules' => array(),'abortSetOnError'=>false,'abortValidationOnError'=>false);

    }

    public function findValidator($ruleName)
    {
        if (trim($ruleName) === ''){
            return null;
        }

        $validatorFunction = null;

        if (isset($this->custom_Validation[$ruleName])){
            if (is_callable($this->custom_Validation[$ruleName])){
                $validatorFunction = $this->custom_Validation[$ruleName];
            } else {
                throw new Exception("Validation '{$ruleName}' is not callable (custom).");
            }
        } else {
            $indicator = explode('_',$ruleName);
            if (isset($indicator[0])){
                $indicator = $indicator[0];
            } else {
                throw new Exception("invalid rule name '{$ruleName}'.");
            }

            $possibleClasses = array();
            foreach(self::$validatorClasses as $class => $classIndicator){
                if ($classIndicator === $indicator){
                    $possibleClasses[] = $class;
                }
            }

            foreach($this->custom_Validation_Classes as $class => $classIndicator){
                if ($classIndicator === $indicator){
                    $possibleClasses[] = $class;
                }
            }

            if (empty($possibleClasses)){              
                throw new Exception("Invalid indicator '{$indicator}'.");
            }

            $found = false;
            foreach($possibleClasses as $class){
                if(is_callable($class.'::validate_'.$ruleName)) {
                    $validatorFunction = $class.'::validate_'.$ruleName;
                    $found = true;
                    break;
                }
            }

            if (!$found){
                throw new Exception("Validation '".$class.'::validate_'.$ruleName."' does not exists.");
            }
        }

        return $validatorFunction;
    }

    public function findSelector($ruleName)
    {
        if (trim($ruleName) === ''){
            return null;
        }

        $selectionFunction = null;

        if (isset($this->custom_Selection[$ruleName])){
            if (is_callable($this->custom_Selection[$ruleName])){
                $selectionFunction = $this->custom_Selection[$ruleName];
            } else {
                throw new Exception("Selection '{$ruleName}' is not callable (custom).");
            }
        } else {
            $indicator = explode('_',$ruleName);
            if (isset($indicator[0])){
                $indicator = $indicator[0];
            } else {
                throw new Exception("invalid rule name '{$ruleName}'.");
            }

            $possibleClasses = array();
            foreach(self::$selectionClasses as $class => $classIndicator){
                if ($classIndicator === $indicator){
                    $possibleClasses[] = $class;
                }
            }

            foreach($this->custom_Selection_Classes as $class => $classIndicator){
                if ($classIndicator === $indicator){
                    $possibleClasses[] = $class;
                }
            }

            if (empty($possibleClasses)){              
                throw new Exception("Invalid indicator '{$indicator}'.");
            }

            $found = false;
            foreach($possibleClasses as $class){
                if(is_callable($class.'::select_'.$ruleName)) {
                    $selectionFunction = $class.'::select_'.$ruleName;
                    $found = true;
                    break;
                }
            }

            if (!$found){
                throw new Exception("Selection '".$class.'::select_'.$ruleName."' does not exists.");
            }
        }

        return $selectionFunction;
    }

    public function collectKeys($fieldNames, $selectors)
    {       
        foreach ($selectors as $ruleId => $rule){
            $ruleName = $rule[0];
            $ruleParam = $rule[1];

            $callable = $this->findSelector($ruleName);

            if ($callable === null){
                continue;
            }

            $res = call_user_func($callable, $fieldNames, $this->input, $this->settings, $ruleParam);

            if (is_array($res) || $res === false){
                if (isset($res['notification'])){
                    $this->notifications = array_merge($this->notifications,$res['notification']);
                }

                if (isset($res['errors'])){
                    $this->errors = array_merge($this->errors,$res['errors']);
                }

                if(isset($res['keys'])) {
                    $fieldNames = $res['keys'];
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

        return $fieldNames;
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

        foreach($this->validation_rules as $set){
            $selector = $set[0];
            $ruleSet = $set[1];

            $setParameters = array_merge($ruleSet,$this->convertRules($this->settings['postRules']));

            $validRuleSet = true;
            $setResult = array();
            $this->settings['setError'] = false;

            $fieldNames = $this->collectKeys(array_keys($this->input), $selector);
            if ($fieldNames === false){
                return false;
            }

           
            foreach ($fieldNames as $fieldName){
                $abort = false;

                foreach ($setParameters as $ruleId => $rule){
                    $ruleName = $rule[0];
                    $ruleParam = $rule[1];

                    $callable = $this->findValidator($ruleName);

                    if ($callable === null){
                        continue;
                    }

                   $res = call_user_func($callable, $fieldName, $this->input, $this->settings, $ruleParam);

                    if (is_array($res) || $res === false){
                        if (isset($res['notification'])){
                            $this->notifications = array_merge($this->notifications,$res['notification']);
                        }

                        if (isset($res['errors'])){
                            $this->errors = array_merge($this->errors,$res['errors']);
                        }

                        if (!isset($res['valid']) || $res['valid'] === false){
                            $validRuleSet = false;
                            $this->settings['setError'] = true;
                            $this->settings['validationError'] = true;

                            $value = (isset($this->input[$fieldName]) ? $this->input[$fieldName] : null);
                            $this->errors[] = array('field'=>$fieldName,'value'=>$value,'rule'=>$ruleName);
                        } elseif(isset($res['valid']) && $res['valid'] === true) {
                            if (isset($res['field']) && isset($res['value'])){
                                $this->input[$res['field']] = $res['value'];
                                $this->insertValue($fieldName, $res['value']);
                            }

                            if (isset($res['fields'])){
                                if (!is_array($res['fields'])){
                                    throw new Exception('Validation rule \''.__METHOD__.'\', array expected.');
                                }
                                foreach($res['fields'] as $field => $value){
                                    $this->input[$field] = $value;
                                    $this->insertValue($field, $value);
                                }
                            }
                        }

                        if ($this->settings['abortSetOnError'] || (isset($res['abortSet']) && $res['abortSet'] === true)){
                            $abort = true;
                            break;
                        } elseif ($this->settings['abortValidationOnError'] || (isset($res['abortValidation']) && $res['abortValidation'] === true)){
                            $this->resetResult();
                            $this->resetValidationRules();
                            $this->validated=false;
                            return false;
                        }
                    }
                }

                if ($abort === true){
                    break;
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