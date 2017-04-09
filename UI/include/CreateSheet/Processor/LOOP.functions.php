<?php
/**
 * @file LOOP.functions.php
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.4.4
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2015-2016
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

function LOOP_createParameters(&$subexercise, $key, $exercisekey, $subexercisekey, $oldparameter = null, $filepaths, $filenames, $fileerrors, $filesystemURI, $databaseURI)
{
    $testcaseType = explode(' ',$oldparameter)[0];
    $compileinput = array($oldparameter);

    // custom Aufruf verarbeiten
    if ($testcaseType == "custom") {
        // compileFile verarbeiten
        if (isset($_FILES['exercises']['tmp_name'][$exercisekey]['subexercises'][$subexercisekey]['compileFileParameter'][0]) && !empty($_FILES['exercises']['tmp_name'][$exercisekey]['subexercises'][$subexercisekey]['compileFileParameter'][0])) {
            $timestamp = time();
            $TempFile = File::createFile(NULL,$_FILES['exercises']['name'][$exercisekey]['subexercises'][$subexercisekey]['compileFileParameter'][0],NULL,$timestamp,NULL,NULL,NULL);
            $TempFile->setBody( Reference::createReference($_FILES['exercises']['tmp_name'][$exercisekey]['subexercises'][$subexercisekey]['compileFileParameter'][0]) );

            $TempFileJSON = File::encodeFile($TempFile);
            $output = http_post_data($filesystemURI."/file", $TempFileJSON, true, $message);

            if($message >= 200 && $message <= 299)
            {
                $compileinput[] = File::decodeFile($output);
            }
            else
            {
                $compileinput[] = "";
            }
        } elseif (isset($subexercise['compileFileParameter'][0]) && $subexercise['compileFileParameter'][0] != "" && is_numeric($subexercise['compileFileParameter'][0])) {
            $response = http_get($databaseURI."/file/file/".$subexercise['compileFileParameter'][0], true, $message);

            if($message == 200)
            {
                $compileinput[] = File::decodeFile($response);
            }
            else
            {
                $compileinput[] = "";
            }
        } else {
            $compileinput[] = "";
        }

        // runParameter speichern
        if (isset($subexercise['runParameter'])) {
            $compileinput[] = $subexercise['runParameter'];
        }

        // runFile verarbeiten
        if (isset($_FILES['exercises']['tmp_name'][$exercisekey]['subexercises'][$subexercisekey]['runFileParameter'][0]) && !empty($_FILES['exercises']['tmp_name'][$exercisekey]['subexercises'][$subexercisekey]['runFileParameter'][0])) {
            $timestamp = time();
            $TempFile = File::createFile(NULL,$_FILES['exercises']['name'][$exercisekey]['subexercises'][$subexercisekey]['runFileParameter'][0],NULL,$timestamp,NULL,NULL,NULL);
            $TempFile->setBody( Reference::createReference($_FILES['exercises']['tmp_name'][$exercisekey]['subexercises'][$subexercisekey]['runFileParameter'][0]) );

            $TempFileJSON = File::encodeFile($TempFile);
            $output = http_post_data($filesystemURI."/file", $TempFileJSON, true, $message);

            if($message >= 200 && $message <= 299)
            {
                $compileinput[] = File::decodeFile($output);
            }
            else
            {
                $compileinput[] = "";
            }
        } elseif (isset($subexercise['runFileParameter'][0]) && $subexercise['runFileParameter'][0] != "" && is_numeric($subexercise['runFileParameter'][0])) {
            $response = http_get($databaseURI."/file/file/".$subexercise['runFileParameter'][0], true, $message);

            if($message == 200)
            {
                $compileinput[] = File::decodeFile($response);
            }
            else
            {
                $compileinput[] = "";
            }
        } else {
            $compileinput[] = "";
        }
    }

    $parameters = array(Testcase::createTestcase(null,"compile",$compileinput));

    if (isset($subexercise['showErrorsParameter'][$key][0]) && $subexercise['showErrorsParameter'][$key][0] == "1")
    {
        $parameters[0]->setErrorsEnabled("1");
    }
    else
    {
        $parameters[0]->setErrorsEnabled("0");
    }

    if (isset($subexercise['rejectOnErrorsParameter'][$key][0]) && $subexercise['rejectOnErrorsParameter'][$key][0] == "1")
    {
        $parameters[0]->setRejectSubmissionOnError("1");
    }
    else
    {
        $parameters[0]->setRejectSubmissionOnError("0");
    }
    
    
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
                $TempFile->setBody( Reference::createReference($filepaths[$key][$key2]) );

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
                    if(preg_match("/ID_[0-9]+/i", $inputvalue))
                    {

                        $response = http_get($databaseURI."/file/file/".preg_replace("/ID_/i", "", $inputvalue), true, $message);

                        if($message == 200)
                        {
                            $decodedFile = File::decodeFile($response);
                            $input[] = array($subexercise['inputDatatype'][$key][$key3],$decodedFile);
                            if (!in_array($decodedFile->getFileId(), $hashes)) {
                                $hashes[] = $decodedFile->getFileId();
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
                if(preg_match("/ID_[0-9]+/i", $subexercise['outputParameter'][$key][$key2]))
                {
                    $response = http_get($databaseURI."/file/file/".preg_replace("/ID_/i", "", $subexercise['outputParameter'][$key][$key2]), true, $message);

                    if($message == 200)
                    {
                        $decodedFile = File::decodeFile($response);
                        $output = array($subexercise['outputDatatype'][$key],$decodedFile);
                        if (!in_array($decodedFile->getFileId(), $hashes)) {
                            $hashes[] = $decodedFile->getFileId();
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