<?php
/**
 * @file LOOP.functions.php
 * @author  Ralf Busch
 */



function LOOP_createParameters(&$subexercise, $key, $oldparameter = null, $filepaths, $filenames, $fileerrors, $filesystemURI, $databaseURI)
{
    $parameters = array(Testcase::createTestcase(null,"compile",array($oldparameter)));

    if (isset($subexercise['showErrorsParameter'][$key][0]) && $subexercise['showErrorsParameter'][$key][0] == "1")
    {
        $parameters[0]->setErrorsEnabled("1");
    }
    else
    {
        $parameters[0]->setErrorsEnabled("0");
    }
    
    $testcaseType = explode(' ',$oldparameter)[0];
    $timestamp = time();

    if (isset($subexercise['inputDatatype'][$key]) && !empty($subexercise['inputDatatype'][$key]) && $subexercise['inputDatatype'][$key] !== array() 
        && isset($subexercise['outputDatatype'][$key]) && !empty($subexercise['outputDatatype'][$key]) && $subexercise['outputDatatype'][$key] !== array()
        && isset($subexercise['inputParameter'][$key]) && !empty($subexercise['inputParameter'][$key]) && $subexercise['inputParameter'][$key] !== array()
        && isset($subexercise['outputParameter'][$key]) && !empty($subexercise['outputParameter'][$key]) && $subexercise['outputParameter'][$key] !== array())
    {
        $Files = [];

        if (isset($filepaths) && !empty($filepaths) && isset($filenames) && !empty($filenames) && isset($fileerrors) && !empty($fileerrors))
        {
            //print_r($fileerrors);

            foreach ($filenames[$key] as $key2 => $filename) {
                $TempFile = File::createFile(NULL,$filename,NULL,$timestamp,NULL,NULL,NULL);
                $TempFile->setBody( Reference::createReference($filepaths[$key2][0]) );

                $TempFileJSON = File::encodeFile($TempFile);
                $output = http_post_data($filesystemURI."/file", $TempFileJSON, true, $message);

                $Files[$key2] = File::decodeFile($output);
            }
        }

        $hashes = [];

        foreach ($subexercise['inputParameter'][$key] as $key2 => $testcase)
        {
            $input = array();
            foreach ($testcase as $key3 => $inputvalue) {
                if($subexercise['inputDatatype'][$key][$key3] == "Data")
                {
                    if(preg_match("/[0-9]+[a-f]+/i", $inputvalue))
                    {
                        $response = http_get($databaseURI."/file/hash/".$inputvalue, true, $message);

                        if($message == 200)
                        {
                            $decodedFile = File::decodeFile($response);
                            $input[] = array($subexercise['inputDatatype'][$key][$key3],$decodedFile);
                            if (!in_array($decodedFile->getHash(), $hashes)) {
                                $hashes[] = $decodedFile->getHash();
                                $Files[] = $decodedFile;
                            }
                        }
                        else
                        {
                            $input[] = array($subexercise['inputDatatype'][$key][$key3],$inputvalue);
                        }
                    }
                    else if (is_numeric($inputvalue))
                    {
                        $input[] = array($subexercise['inputDatatype'][$key][$key3],$Files[$inputvalue]);
                    }
                    else
                    {
                        $input[] = array($subexercise['inputDatatype'][$key][$key3],$inputvalue);
                    }
                }
                else
                {
                    $input[] = array($subexercise['inputDatatype'][$key][$key3],$inputvalue);
                }
            }
            
            $output;
            if($subexercise['outputDatatype'][$key] == "Data")
            {
                if(preg_match("/[0-9]+[a-f]+/i", $subexercise['outputParameter'][$key][$key2]))
                {
                    $response = http_get($databaseURI."/file/hash/".$subexercise['outputParameter'][$key][$key2], true, $message);

                    if($message == 200)
                    {
                        $decodedFile = File::decodeFile($response);
                        $output = array($subexercise['outputDatatype'][$key],$decodedFile);
                        if (!in_array($decodedFile->getHash(), $hashes)) {
                            $hashes[] = $decodedFile->getHash();
                            $Files[] = $decodedFile;
                        }

                    }
                    else
                    {
                        $output = array($subexercise['outputDatatype'][$key],$subexercise['outputParameter'][$key][$key2]);
                    }
                }
                else if (is_numeric($subexercise['outputParameter'][$key][$key2]))
                {
                    $output = array($subexercise['outputDatatype'][$key],$Files[$subexercise['outputParameter'][$key][$key2]]);
                }
                else
                {
                    $output = array($subexercise['outputDatatype'][$key],$subexercise['outputParameter'][$key][$key2]);
                }
            }
            else
            {
                $output = array($subexercise['outputDatatype'][$key],$subexercise['outputParameter'][$key][$key2]);
            }

            //$output = array($subexercise['outputDatatype'][$key],$subexercise['outputParameter'][$key][$key2]);

            $parameters[] = Testcase::createTestcase(null,$testcaseType,$input,$output);
        }
        if(!empty($Files))
        {
            $parameters[0]->setFile($Files);
        }
    }

    return Testcase::encodeTestcase($parameters);
}

 ?>