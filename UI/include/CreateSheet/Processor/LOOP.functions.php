<?php
/**
 * @file LOOP.functions.php
 * @author  Ralf Busch
 */

function LOOP_createParameters(&$subexercise, $key, $oldparameter = null)
{
    $parameters = array(Testcase::createTestcase(null,"compile",array($oldparameter)));
    $testcaseType = explode(' ',$oldparameter)[0];

    if (isset($subexercise['inputDatatype'][$key]) && !empty($subexercise['inputDatatype'][$key]) && $subexercise['inputDatatype'][$key] !== array() 
        && isset($subexercise['outputDatatype'][$key]) && !empty($subexercise['outputDatatype'][$key]) && $subexercise['outputDatatype'][$key] !== array()
        && isset($subexercise['inputParameter'][$key]) && !empty($subexercise['inputParameter'][$key]) && $subexercise['inputParameter'][$key] !== array()
        && isset($subexercise['ouputParameter'][$key]) && !empty($subexercise['ouputParameter'][$key]) && $subexercise['ouputParameter'][$key] !== array())
    {
        foreach ($subexercise['inputParameter'][$key] as $key2 => $testcase)
        {
            $input = array();
            foreach ($testcase as $key3 => $inputvalue) {
                $input[] = array($subexercise['inputDatatype'][$key][$key3],$inputvalue);
            }

            $output = array($subexercise['outputDatatype'][$key],$subexercise['ouputParameter'][$key][$key2]);

            $parameters[] = Testcase::createTestcase(null,$testcaseType,$input,$output);
        }
    }

    return Testcase::encodeTestcase($parameters);
}

 ?>