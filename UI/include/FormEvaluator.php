<?php
/**
 * @file FormEvaluator.php
 * Contains the FormEvaluator class
 *
 * @todo better error checking for function parameters
 * @todo add option for email adresses.
 * @todo add check for integer numbers
 * @todo maybe use filter_var ?
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
                                    $notEmpty,
                                    $possibleValues,
                                    $notIn) {

        if (isset($this->formValues[$key])) {
            // the value is set
            $value = $this->formValues[$key];

            if ($value == '') {
                if ($notEmpty == true) {
                    // the value is empty, biut it should not be
                    return false;
                }
            }

            if (is_null($possibleValues) == false) {

                // check if $value is in $possibleValues
                $result = array_search($value, $possibleValues) !== false;

                if ($notIn) {
                    // value should not be found.
                    $result = !$result;
                }

                if ($result == true) {
                    return $value;
                } else {
                    return false;
                }
            }

            return $value;

        } elseif ($required == true) {
            // the value is not set and is required
            return false;
        }

        return NULL;
    }

    // checks if $this->formValues[$key] contains a valid array
    private function evaluateArray($key,
                                   $required,
                                   $notEmpty) {

        if (isset($this->formValues[$key])) {
            // the value is set

            $value = $this->formValues[$key];

            if (is_array($value) == false) {
                // the value is not an array
                return false;
            } else {
                if (count($value) == 0) {
                    if ($notEmpty == true) {
                        // the value is an array, but is empty and should not be
                        return false;
                    }
                }
            }

            return $value;

        } elseif ($required == true) {
            // the value is not set and is required
            return false;
        }

        return NULL;
    }

    // checks if $this->formValues[$key] contains a valid number
    private function evaluateNumber($key,
                                    $required,
                                    $range,
                                    $notIn) {

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

    /**
     * Construct a new form evaluator.
     *
     * @param array $values The form values.
     * @see FormEvaluator::ForFormValues($values)
     */
    public function __construct($values) {
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
                $notEmpty = $value['notEmpty'];

                // check if the value for $key is valid
                $result = $this->evaluateString($key,
                                                $required,
                                                $notEmpty,
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
                $notEmpty = $value['notEmpty'];

                // check if the value for $key is valid
                $result = $this->evaluateArray($key,
                                               $required,
                                               $notEmpty);
            } elseif ($type == 'integer') {
                $range = $value['range'];
                $notIn = $value['notIn'];

                // check if the value for $key is valid
                $result = $this->evaluateInteger($key,
                                                $required,
                                                $range,
                                                $notIn);
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
     * @param bool $notEmpty True if the value for $key should not be an empty
     * string, false otherwise.
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @param array $oneOf (optional) An array of values that are valid for
     * this string.
     * @param bool $notIn (optional) True if the value for $key may not be one
     * of the values in $oneOf, false otherwise.
     * @return self
     */
    public function checkStringForKey($key,
                                      $required,
                                      $notEmpty,
                                      $messageType,
                                      $message,
                                      $oneOf = NULL,
                                      $notIn = false)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'string',
                                'required' => $required,
                                'notEmpty' => $notEmpty,
                                'messageType' => $messageType,
                                'message' => $message,
                                'oneOf' => $oneOf,
                                'notIn' => $notIn);
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
     * @param array $range (optional) an associative array with optional keys
     * 'min' and 'max' that contain a number that corresponds to the minimum
     * and maximum value of $key's value (inclusive).
     * @param bool $notIn (optional) If true reverse the meaning of $range, to
     * exclude 'min', 'max' and all values in between.
     * @return self
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
     * Add check for an array.
     * @param string $key The key that should be checked.
     * @param bool $required True if it is required that there is a value for
     * this key, false otherwise.
     * @see FormEvaluator::REQUIRED
     * @see FormEvaluator::OPTIONAL
     * @param bool $notEmpty True if the value for $key should not be an empty
     * array, false otherwise.
     * @param string $messageType The type of message that is generated when
     * an error occurs.
     * @param string $message The message that is returned on error.
     * @return self
     */
    public function checkArrayForKey($key,
                                     $required,
                                     $notEmpty,
                                     $messageType,
                                     $message)
    {
        $this->values[] = array('key' => $key,
                                'type' => 'array',
                                'required' => $required,
                                'notEmpty' => $notEmpty,
                                'messageType' => $messageType,
                                'message' => $message);
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
}

?>