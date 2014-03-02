<?php
/**
 * @file FormEvaluator.php
 * Contains the FormEvaluator class
 *
 * @todo better error checking for function parameters
 *
 * @author Florian Lücke
 */

include_once 'include/Helpers.php';

class FormEvaluator {
    /**
     * @defgroup Constants Theses constants should make the code easier to read.
     * @{
     */

    /**
     * Equivalent to true
     */
    const REQUIRED = true;

    /**
     * Equivalent to false
     */
    const OPTIONAL = false;

    /**
     * @}
     */

    private $formValues;
    private $values;
    private $cleanInput = false;

    /**
     * The values that were found in the input.
     *
     * @var array $foundValues After evaluation this contains the values that
     * were found in the form.
     * @see FormEvaluator::evaluate($cleaninput)
     */
    public $foundValues = array();

    /**
     * The notifications that were generated during evaluation.
     *
     * @var array $notifications After evaluation this contains the
     * notificatiosn that were generated
     * @see FormEvaluator::evaluate($cleaninput)
     */
    public $notifications = array();


    private function insertValue($key, $value)
    {
        // insert $value into $foundValues
        if ($this->cleanInput == true) {
            $this->foundValues[$key] = cleanInput($value);
        } else {
            $this->foundValues[$key] = $value;
        }
    }

    // checks if $this->formValues[$key] contains a valid string
    private function evaluateString($key,
                                    $required,
                                    $length,
                                    $possibleValues,
                                    $notIn)
    {

        if (isset($this->formValues[$key])) {
            // the value is set
            $value = $this->formValues[$key];

            if (is_null($length) == false) {
                if (isset($length['max'])) {
                    $max = $length['max'];
                } else {
                    $max = INF;
                }

                if (isset($length['min'])) {
                    $min = $length['min'];
                } else {
                    $min = 0;
                }
            } else {
                $min = 0;
                $max = INF;
            }

            if (is_null($possibleValues) == false) {

                // check if $value is in $possibleValues
                $result = array_search($value, $possibleValues, true);
                $result = $result !== false;

                if ($notIn == true) {
                    // value should not be found.
                    $result = !$result;
                }

                if ($result == true) {
                    return $value;
                } else {
                    return false;
                }
            }

            if (strlen($value) <= $max && strlen($value) >= $min) {
                return $value;
            } else {
                return false;
            }

        } elseif ($required == true) {
            // the value is not set and is required
            return false;
        }

        return NULL;
    }

    // checks if $this->formValues[$key] contains a valid array
    private function evaluateArray($key,
                                   $required,
                                   $options)
    {
        if (isset($this->formValues[$key])) {
            $values = $this->formValues[$key];

            if (! is_array($values)) {
                return $required ? false : NULL;
            }

            $type = $options['type'];

            $arrayValues = array();

            if ($type == 'string') {
                $length = $options['length'];
                $possibleValues = $options['oneOf'];
                $notIn = $options = $options['notIn'];

                foreach ($values as $value) {
                    $this->formValues['temp'] = $value;
                    $result = $this->evaluateString('temp',
                                                    $required,
                                                    $length,
                                                    $possibleValues,
                                                    $notIn);

                    if ($result === false) {
                        return false;
                    }

                    $arrayValues[] = $result;
                    unset($this->formValues['temp']);
                }


            } elseif ($type == 'number') {
                $range = $options['range'];
                $notIn = $options['notIn'];

                foreach ($values as $value) {
                    $this->formValues['temp'] = $value;
                    $result = $this->evaluateNumber('temp',
                                                    $required,
                                                    $range,
                                                    $notIn);

                    if ($result === false) {
                        return false;
                    }

                    $arrayValues[] = $result;
                    unset($this->formValues['temp']);
                }
            } elseif ($type == 'integer') {
                $range = $options['range'];
                $notIn = $options['notIn'];

                foreach ($values as $value) {
                    $this->formValues['temp'] = $value;
                    $result = $this->evaluateInteger('temp',
                                                    $required,
                                                    $range,
                                                    $notIn);

                    if ($result === false) {
                        return false;
                    }

                    $arrayValues[] = $result;
                    unset($this->formValues['temp']);
                }
            }

            return $arrayValues;

        } elseif ($required == true) {

            return false;
        }

        return NULL;
    }

    // checks if $this->formValues[$key] contains a valid number
    private function evaluateNumber($key,
                                    $required,
                                    $range,
                                    $notIn)
    {

        if (isset($this->formValues[$key])) {
            // the value is set

            $number = $this->formValues[$key];

            if (is_numeric($number) == false) {
                // the value is not a number
                return false;
            }

            if (is_null($range) == false) {
                if (isset($range['max'])) {
                    $max = $range['max'];
                } else {
                    $max = INF;
                }

                if (isset($range['min'])) {
                    $min = $range['min'];
                } else {
                    $min = -INF;
                }

                // check if the value is in $range
                $result = ($number <= $max) && ($number >= $min);

                if ($notIn == true) {
                    // the value should not be in $range
                    $result = !$result;
                }

                if ($result == true) {
                    // valid number
                    return $number;

                } else {
                    // invalid number
                    return false;
                }
            }

            return $number;

        } elseif ($required == true) {
            // the value is not set and is required
            return false;
        }

        return NULL;
    }

    // check if value is an integer
    private function evaluateInteger($key,
                                     $required,
                                     $range,
                                     $notIn)
    {
        $result = $this->evaluateNumber($key,
                                        $required,
                                        $range,
                                        $notIn);
        if ($result !== false) {
            if (filter_var($result, FILTER_VALIDATE_INT) !== false) {
                return $result;
            }
        }

        return false;
    }

    // chek if value is an email address
    private function evaluateEmail($key,
                                   $required)
    {
        if (isset($this->formValues[$key])) {
            $value = $this->formValues[$key];

            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $value;
            }

            return false;

        } elseif ($required == true) {
            return false;
        }

        return NULL;
    }

    /**
     * Construct a new form evaluator.
     *
     * @param array $values The form values.
     * @see FormEvaluator::ForFormValues($values)
     */
    public function __construct($values)
    {
        $this->formValues = $values;
    }

    /**
     * Construct a new form evaluator.
     *
     * @param array $values The form values.
     * @see FormEvaluator::__construct($values)
     */
    public static function ForFormValues($values)
    {
        $f = new FormEvaluator($values);

        return $f;
    }

    /**
     * Evaluate the form.
     *
     * @param bool $cleanInput (optional) True if the form values should be
     * sanitized, false otherwise.
     * @return bool True if all values marked as required were found,
     * false otherwise
     */
    public function evaluate($cleanInput = false)
    {
        $foundAllRequired = true;

        $this->cleanInput = $cleanInput;
        foreach ($this->values as $value) {
            $key = $value['key'];
            $type = $value['type'];
            $required = $value['required'];
            $messageType = $value['messageType'];
            $message = $value['message'];

            if ($type == 'string') {
                $oneOf = $value['oneOf'];
                $notIn = $value['notIn'];
                $length = $value['length'];

                // check if the value for $key is valid
                $result = $this->evaluateString($key,
                                                $required,
                                                $length,
                                                $oneOf,
                                                $notIn);
            } elseif ($type == 'number') {
                $range = $value['range'];
                $notIn = $value['notIn'];

                // check if the value for $key is valid
                $result = $this->evaluateNumber($key,
                                                $required,
                                                $range,
                                                $notIn);
            } elseif ($type == 'array') {
                $options = $value['options'];

                // check if the value for $key is valid
                $result = $this->evaluateArray($key,
                                               $required,
                                               $options);
            } elseif ($type == 'integer') {
                $range = $value['range'];
                $notIn = $value['notIn'];

                // check if the value for $key is valid
                $result = $this->evaluateInteger($key,
                                                $required,
                                                $range,
                                                $notIn);
            } elseif ($type == 'email') {
                $result = $this->evaluateEmail($key,
                                               $required);
            }

            if ($result === false) {
                // the value for $key is invalid

                // if the value was not required, we don't care
                $success = $required ? $result : true;

                // remember if we failed before
                $foundAllRequired &= $success;

                // create a notification
                $this->notifications[] = MakeNotification($value['messageType'],
                                                          $value['message']);
            } else {
                $this->insertValue($key, $result);
            }
        }

        return $foundAllRequired;
    }

    /**
     * Add check for a string.
     *
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @param bool $length (optional) An associative array with optional keys
     * 'min' and 'max' that contain a number that corresponds to the minimum
     * and maximum length of $key's value (inclusive).
     * @param array $oneOf (optional) An array of values that are valid for
     * this string.
     * @param bool $notIn (optional) True if the value for $key may not be one
     * of the values in $oneOf, false otherwise.
     * @return self
     */
    public function checkStringForKey($key,
                                      $required,
                                      $messageType,
                                      $message,
                                      $length = NULL,
                                      $oneOf = NULL,
                                      $notIn = false)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'string',
                                'required' => $required,
                                'length' => $length,
                                'messageType' => $messageType,
                                'message' => $message,
                                'oneOf' => $oneOf,
                                'notIn' => $notIn);
        return $this;
    }

    /**
     * Add check for an email address.
     *
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @return self
     */
    public function checkEmailForKey($key,
                                      $required,
                                      $notEmpty,
                                      $messageType,
                                      $message)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'email',
                                'required' => $required,
                                'messageType' => $messageType,
                                'message' => $message);
        return $this;
    }

    /**
     * Add check for a number.
     *
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @param array $range (optional) An associative array with optional keys
     * 'min' and 'max' that contain a number that corresponds to the minimum
     * and maximum value of $key's value (inclusive).
     * @param bool $notIn (optional) If true reverse the meaning of $range, to
     * exclude 'min', 'max' and all values in between.
     * @return self
     * @see FormEvaluator::checkIntegerForKey
     */
    public function checkNumberForKey($key,
                                      $required,
                                      $messageType,
                                      $message,
                                      $range = NULL,
                                      $notIn = NULL)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'number',
                                'required' => $required,
                                'messageType' => $messageType,
                                'message' => $message,
                                'range' => $range,
                                'notIn' => $notIn);

        return $this;
    }

    /**
     * Add check for an integer.
     *
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @param array $range (optional) an associative array with optional keys
     * 'min' and 'max' that contain a number that corresponds to the minimum
     * and maximum value of $key's value (inclusive).
     * @param bool $notIn (optional) If true reverse the meaning of $range, to
     * exclude 'min', 'max' and all values in between.
     * @return self
     * @see FormEvaluator::checkNumberForKey
     */
    public function checkIntegerForKey($key,
                                      $required,
                                      $messageType,
                                      $message,
                                      $range = NULL,
                                      $notIn = NULL)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'integer',
                                'required' => $required,
                                'messageType' => $messageType,
                                'message' => $message,
                                'range' => $range,
                                'notIn' => $notIn);

        return $this;
    }

    /**
     * Add check for an array.
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @param bool $length (optional) An associative array with optional keys
     * 'min' and 'max' that contain a number that corresponds to the minimum
     * and maximum length of the strings in the array (inclusive).
     * @param array $oneOf (optional) An array of values that are valid for
     * the strings.
     * @param bool $notIn (optional) True if the strings in the array may not
     * be one of the values in $oneOf, false otherwise.
     * @return self
     */
    public function checkArrayOfStringsForKey($key,
                                              $required,
                                              $messageType,
                                              $message,
                                              $length = NULL,
                                              $oneOf = NULL,
                                              $notIn = NULL)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'array',
                                'required' => $required,
                                'messageType' => $messageType,
                                'message' => $message,
                                'options' => array('type' => 'string',
                                                   'required' => $required,
                                                   'length' => $length,
                                                   'oneOf' => $oneOf,
                                                   'notIn' => $notIn));
        return $this;
    }

    /**
     * Add check for an array.
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @param array $range (optional) An associative array with optional keys
     * 'min' and 'max' that contain a number that corresponds to the minimum
     * and maximum value of $key's value (inclusive).
     * @param bool $notIn (optional) If true reverse the meaning of $range, to
     * exclude 'min', 'max' and all values in between.
     * @return self
     */
    public function checkArrayOfNumbersForKey($key,
                                              $required,
                                              $messageType,
                                              $message,
                                              $range = NULL,
                                              $notIn = NULL)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'array',
                                'required' => $required,
                                'messageType' => $messageType,
                                'message' => $message,
                                'options' => array('type' => 'number',
                                                   'range' => $range,
                                                   'notIn' => $notIn));
        return $this;
    }

    /**
     * Add check for an array.
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @param array $range (optional) An associative array with optional keys
     * 'min' and 'max' that contain a number that corresponds to the minimum
     * and maximum value of $key's value (inclusive).
     * @param bool $notIn (optional) If true reverse the meaning of $range, to
     * exclude 'min', 'max' and all values in between.
     * @return self
     */
    public function checkArrayOfIntegersForKey($key,
                                              $required,
                                              $messageType,
                                              $message,
                                              $range = NULL,
                                              $notIn = NULL)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'array',
                                'required' => $required,
                                'messageType' => $messageType,
                                'message' => $message,
                                'options' => array('type' => 'integer',
                                                   'range' => $range,
                                                   'notIn' => $notIn));
        return $this;
    }
}

?>
