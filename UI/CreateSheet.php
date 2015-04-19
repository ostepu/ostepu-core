<?php
/**
 * @file CreateSheet.php
 * Constructs a page where a user can create an exercise sheet.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 *
 * @todo choose correct groupsize for no Group (0 or 1)
 * @todo evaluate correct exercisetype in $subeval
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/include/FormEvaluator.php';
require_once(dirname(__FILE__).'/phplatex.php');

function unmap($map, $id){
    foreach ($map as $m){
        if ($m[1]==$id)
            return $m[2];
    }
    return null;
}

if (!isset($sid))
    $sid=null;
if (!isset($cid))
    $cid=null;
    
// load user data from the database
$URL = $getSiteURI . "/createsheet/user/{$uid}/course/{$cid}";
$createsheetData = http_get($URL, true);
$createsheetData = json_decode($createsheetData, true);

$noContent = false;

$result = http_get($serverURI."/DB/DBProcess/processList/process/course/{$cid}",true);
$processorModules = Process::decodeProcess($result);
if (!is_array($processorModules)) $processorModules = array($processorModules);
$components = array();
foreach ($processorModules as $processor)
    $components[] = $processor->getTarget();
$processorModules = array('processors' => $components);
        
$exerciseTypes = array();
if (isset($createsheetData['exerciseTypes'])) {
    $exerciseTypes = array('exerciseTypes' => $createsheetData['exerciseTypes']);
    $_SESSION['JSCACHE'] = json_encode($exerciseTypes['exerciseTypes']);
} else {
    $_SESSION['JSCACHE'] = "";
    $errormsg = "Bitte weisen Sie der Veranstaltung zugelassene Punktearten zu!";
    array_push($notifications, MakeNotification('warning', $errormsg));
    $noContent = true;
}



// load exerciseSheet from DB, to check which element needs to be removed
$sheet_data_old=null;
if (isset($_POST['action'])){
    $URL = $databaseURI . "/exercisesheet/exercisesheet/{$sid}/exercise";
    $sheet_data_old = http_get($URL, true);
    $sheet_data_old = json_decode($sheet_data_old, true);
}
$form_data_old=null;
if (isset($_POST['action'])){
    $URL = $serverURI."/DB/DBForm/form/exercisesheet/{$sid}";
    $form_data_old = http_get($URL, true);
    $form_data_old = json_decode($form_data_old, true);
}
$process_data_old=null;
if (isset($_POST['action'])){
    $URL = $serverURI."/DB/DBProcess/process/exercisesheet/{$sid}";
    $process_data_old = http_get($URL, true);
    $process_data_old = json_decode($process_data_old, true);
}

$timestamp = time();

// validate subtasks
$correctExercise = true;
$validatedExercises = array();
if (isset($_POST['exercises']) == true && empty($_POST['exercises']) == false) {
    foreach ($_POST['exercises'] as $key1 => $exercise) {
        if ($correctExercise == false) {
            break;
        }

        array_push($validatedExercises, $exercise['subexercises']);
        // evaluate subexercises
        foreach ($exercise['subexercises'] as $key2 => $subexercise) {

            // evaluate mime-types
            $mimeTypes = array();
            if (!isset($subexercise['type']) && isset($subexercise['mime-type'])){    
                $mimeTypesForm = explode(",", $subexercise['mime-type']);
                foreach ($mimeTypesForm as $mimeType) {
                    if ($mimeType=='')continue;
                    $mimeType = explode('.',trim(strtolower($mimeType)));
                    $ending=isset($mimeType[1]) ? $mimeType[1] : null;
                    $mimeType=$mimeType[0];
                    
                    if (FILE_TYPE::checkSupportedFileType($mimeType) == false) {
                        $errormsg = "Sie haben eine nicht unterstützte Dateiendung verwendet.";
                        array_push($notifications, MakeNotification('warning', $errormsg));
                        $correctExercise = true;
                        //break;
                    } else { // if mime-type is supported add mimeTypes
                        $mimes = FILE_TYPE::getMimeTypeByFileEnding(trim(strtolower($mimeType)));
                        foreach ($mimes as &$mime){
                            $mime = ExerciseFileType::createExerciseFileType(null,$mime.''.(isset($ending) ?  ' *.'.$ending : ''),null);
                        }
                        $mimeTypes = array_merge($mimeTypes, $mimes);
                    }
                }
            }
            
            // save mimeTypes in validated Exercises
            $validatedExercises[$key1][$key2]['mime-type'] = $mimeTypes;
        }
    }
}

if (isset($_POST['action']))
if ($correctExercise == true) {
    // get sheetPDF
    if (isset($_FILES['sheetPDF']['error']) && !$_FILES['sheetPDF']['error'] == 4){
        $filePath = $_FILES['sheetPDF']['tmp_name'];
        $displayName = $_FILES['sheetPDF']['name'];
        $data = file_get_contents($filePath);
        $data = base64_encode($data);
        $sheetPDFFile = File::createFile(NULL,$displayName,NULL,$timestamp,NULL,NULL,NULL);
        $sheetPDFFile->setBody($data);
    } elseif(isset($_POST['sheetPDFId'])) {
        $sheetPDFFile = File::createFile(NULL,null,NULL,null,NULL,NULL,NULL);
        $sheetPDFFile->setFileId(isset($_POST['sheetPDFId']) ? $_POST['sheetPDFId'] : null);
        $sheetPDFFile->setAddress(isset($_POST['sheetPDFAddress']) ? $_POST['sheetPDFAddress'] : null);
        $sheetPDFFile->setDisplayName(isset($_POST['sheetPDFDisplayName']) ? $_POST['sheetPDFDisplayName'] : null);
    }
    else{
        $sheetPDFFile = null;
    }
    
    // create exerciseSheet
    $sheetId = (isset($_POST['sheetId']) ? $_POST['sheetId'] : (isset($sid) ? $sid : null));
    $sheetName = isset($_POST['sheetName']) ? $_POST['sheetName'] : null;
    $startDate = isset($_POST['startDate']) ? strtotime(str_replace(" - ", " ", isset($_POST['startDate'])?$_POST['startDate']:null)) : null;
    $endDate = isset($_POST['endDate']) ? strtotime(str_replace(" - ", " ", isset($_POST['endDate'])?$_POST['endDate']:null)) : null;
    $groupSize = isset($_POST['groupSize'])?$_POST['groupSize']:null;

    $myExerciseSheet = ExerciseSheet::createExerciseSheet($sheetId,$cid,$endDate,$startDate,$groupSize,NULL, NULL,$sheetName);
    $myExerciseSheet->setSheetFile($sheetPDFFile);

    // get sheetSolution if it exists
    if (isset($_FILES['sheetSolution']['error']) && $_FILES['sheetSolution']['error'] != 4) {
        $filePath = $_FILES['sheetSolution']['tmp_name'];
        $displayName = $_FILES['sheetSolution']['name'];
        $data = file_get_contents($filePath);
        $data = base64_encode($data);

        $sheetSolutionFile = File::createFile(NULL,$displayName,NULL,$timestamp,NULL,NULL,NULL);
        $sheetSolutionFile->setBody($data);
        $myExerciseSheet->setSampleSolution($sheetSolutionFile);
    } elseif(isset($_POST['sheetSolutionId'])) {
        $sheetSolutionFile = File::createFile(NULL,null,NULL,null,NULL,NULL,NULL);
        $sheetSolutionFile->setFileId(isset($_POST['sheetSolutionId']) ? $_POST['sheetSolutionId'] : null);
        $sheetSolutionFile->setAddress(isset($_POST['sheetSolutionAddress']) ? $_POST['sheetSolutionAddress'] : null);
        $sheetSolutionFile->setDisplayName(isset($_POST['sheetSolutionDisplayName']) ? $_POST['sheetSolutionDisplayName'] : null);
        $myExerciseSheet->setSampleSolution($sheetSolutionFile);  
    }

    ///ExerciseSheet::encodeExerciseSheet($myExerciseSheet)
    $sheet_data = json_decode(ExerciseSheet::encodeExerciseSheet($myExerciseSheet),true);

    $newExerciseId=-1;
    $exercises = array();
    // create subtasks as exercise
    foreach ($validatedExercises as $key1 => $exercise) {

        // creation
        foreach ($exercise as $key2 => &$subexercise) {

            // create subexercise object
            $sheetId = (isset($_POST['sheetId']) ? $_POST['sheetId'] : (isset($sid) ? $sid : null));

            // set bonus
            if (preg_match("#[0-9]+b$#", $subexercise['exerciseType']) == true) {
                $bonus = "1";
                // delete ending b from exerciseType if its bonus
                $subexercise['exerciseType'] = rtrim($subexercise['exerciseType'], "b");
            } else {
                $bonus = "0";
            }
            
            // create exercise
            $exerciseId = (isset($subexercise['id']) ? $subexercise['id'] : null);
            if ($exerciseId===null){
                $exerciseId=$newExerciseId;
                $newExerciseId--;
            }
            $subexercise['id']=$exerciseId;
            
            $subexerciseObj = Exercise::createExercise($exerciseId,$cid,$sheetId, $subexercise['maxPoints'],
                                                       $subexercise['exerciseType'],$key1+1,$bonus,$key2+1);
            
            // set FileTypes (only as an array with strings in it)
            $subexerciseObj->setFileTypes($subexercise['mime-type']);
            
            // add attachement if given
            if (isset($_FILES['exercises']['error'][$key1]['subexercises'][$key2]['attachment']) && $_FILES['exercises']['error'][$key1]['subexercises'][$key2]['attachment'] != 4) {
                $filePath = $_FILES['exercises']['tmp_name'][$key1]['subexercises'][$key2]['attachment'];
                $displayName = $_FILES['exercises']['name'][$key1]['subexercises'][$key2]['attachment'];
                $attachments = array();
                
                $data = file_get_contents($filePath);
                $data = base64_encode($data);

                $attachementFile = File::createFile(NULL,$displayName,NULL,$timestamp,NULL,NULL,NULL);
                $attachementFile->setBody($data);
                
                $subexerciseObj->setAttachments(array($attachementFile));
            } elseif(isset($_POST['exercises'][$key1]['subexercises'][$key2]['attachment']['fileId'])) {
                $attachementFile = File::createFile(NULL,null,NULL,null,NULL,NULL,NULL);
                $attachementFile->setFileId(isset($_POST['exercises'][$key1]['subexercises'][$key2]['attachment']['fileId']) ? $_POST['exercises'][$key1]['subexercises'][$key2]['attachment']['fileId'] : null);
                $attachementFile->setAddress(isset($_POST['exercises'][$key1]['subexercises'][$key2]['attachment']['address']) ? $_POST['exercises'][$key1]['subexercises'][$key2]['attachment']['address'] : null);
                $attachementFile->setDisplayName(isset($_POST['exercises'][$key1]['subexercises'][$key2]['attachment']['displayName']) ? $_POST['exercises'][$key1]['subexercises'][$key2]['attachment']['displayName'] : null);
                
                $subexerciseObj->setAttachments(array($attachementFile));            
            }
            // add subexercise to exercises
            array_push($exercises, $subexerciseObj);
        }
        
        #region create_forms
        ##########################
        ### begin create_forms ###
        ##########################
        // create form data
        $forms = array();
        foreach ($exercise as $key2 => $subexercise) {  
            if (isset($subexercise['type'])){
                $exerciseId = (isset($subexercise['id']) ? $subexercise['id'] : null);
                $task = html_entity_decode(isset($subexercise['task']) ? $subexercise['task'] : '');
                $solution = html_entity_decode(isset($subexercise['solution']) ? $subexercise['solution'] : '');

                // inline math-tex
                $first='<span class="math-tex">';
                $second='</span>';
                $pos = strpos ( $task , $first );
                while ($pos!==false){
                    $pos2 = strpos ( $task , $second );
                    if ($pos2 !== false){
                        $mathTex = substr($task, $pos+strlen($first), $pos2-$pos-strlen($first));
                        $replace = false;$replace = texify('$'.$mathTex.'$');
                        if ($replace===false)
                            $replace = '<img src="http://latex.codecogs.com/gif.latex?'.rawurlencode($mathTex).'">';
                        $task = substr($task,0,$pos).$replace.substr($task,$pos2+strlen($second));
                    }
                    $pos = strpos ( $task , $first, $pos+strlen($first)+strlen($second) );
                }
                $pos = strpos ( $solution , $first );
                while ($pos!==false){
                    $pos2 = strpos ( $solution , $second );
                    if ($pos2 !== false){
                        $mathTex = substr($solution, $pos+strlen($first), $pos2-$pos-strlen($first));
                        $replace = false;$replace = texify('$'.$mathTex.'$');
                        if ($replace===false)
                            $replace = '<img src="http://latex.codecogs.com/gif.latex?'.rawurlencode($mathTex).'">';
                        $solution = substr($solution,0,$pos).$replace.substr($solution,$pos2+strlen($second));
                    }
                    $pos = strpos ( $solution , $first, $pos+strlen($first)+strlen($second) );
                }   
                
                $formId = (isset($subexercise['formId']) ? $subexercise['formId'] : null);
                
                $form = Form::createForm(
                                   $formId,
                                   $exerciseId,
                                   $solution,
                                   $task,
                                   isset($subexercise['type']) ? $subexercise['type'] : null
                                  );
                                  
                $choiceText = $subexercise['choice'];
                $choices = array();
                foreach ($choiceText as $tempKey => $choiceData) {
                    $choice = new Choice();
                    $choice->SetText($choiceData); 
                    $choices[$tempKey] = $choice;
                }
                
                if (isset($subexercise['correct'])){
                    $choiceCorrect = $subexercise['correct'];
                    foreach ($choiceCorrect as $tempKey => $choiceData) {
                        if (isset($choices[$tempKey]))                          
                            $choices[$tempKey]->setCorrect(1);                   
                    }
                }
                
                if (isset($subexercise['choiceId'])){
                    $choiceIds = $subexercise['choiceId'];
                    foreach ($choiceIds as $tempKey => $choiceData) {
                        if (isset($choices[$tempKey]))                          
                            $choices[$tempKey]->setChoiceId($choiceData);                   
                    }
                }
                $choices = array_values( $choices );
                
                $form->setChoices($choices);
                $forms[] = $form;
            }
        }

        $forms = json_decode(Form::encodeForm($forms),true);
        
        ########################
        ### end create_forms ###
        ########################
        #endregion
        
        #region create_processors
        ###############################
        ### begin create_processors ###
        ###############################
        
        // create processor data
        $processors = array();
        foreach ($exercise as $key2 => $subexercise) {
            if (isset($subexercise['processorType'])){                                        
                
                $tempProcessors = array();
                
                $processorType = $subexercise['processorType'];
                
                foreach ($processorType as $tempKey => $Data) {
                    $processor = new Process();
                    $processor->setExercise(Exercise::decodeExercise(Exercise::encodeExercise($subexercise)));
                    $component = new Component();
                    $component->setId($Data);
                    $processor->SetTarget($component); 
                    $processor->SetProcessId(isset($subexercise['processorId'][$tempKey]) ? $subexercise['processorId'][$tempKey] : null);
                    
                    // add attachement if given
                    if (isset($_FILES['exercises']) && isset($_FILES['exercises']['error']) && isset($_FILES['exercises']['error'][$key1]) && isset($_FILES['exercises']['error'][$key1]['subexercises']) && isset($_FILES['exercises']['error'][$key1]['subexercises'][$key2]) && isset($_FILES['exercises']['error'][$key1]['subexercises'][$key2]['processAttachment']) && isset($_FILES['exercises']['error'][$key1]['subexercises'][$key2]['processAttachment'][$tempKey]))
                    if ($_FILES['exercises']['error'][$key1]['subexercises'][$key2]['processAttachment'][$tempKey] != 4) {
                        $filePath = $_FILES['exercises']['tmp_name'][$key1]['subexercises'][$key2]['processAttachment'][$tempKey];
                        $displayName = $_FILES['exercises']['name'][$key1]['subexercises'][$key2]['processAttachment'][$tempKey];
                        $attachments = array();
                        
                        foreach ($filePath as $attachKey => $attachPath){
                        $data = file_get_contents($attachPath);
                        $data = base64_encode($data);
                        
                        $attachment = new Attachment();
                        $attachementFile = File::createFile(NULL,$displayName[$attachKey],NULL,$timestamp,NULL,NULL,NULL);
                        $attachementFile->setBody($data);
                        $attachment->setFile($attachementFile);
                        $attachments[] = $attachment;
                        }
                        
                        $processor->setAttachment($attachments);
                    }
                    
                    $tempProcessors[$tempKey] = $processor;
                }
                
                if (isset($subexercise['processorParameterList']) && !empty($subexercise['processorParameterList']) && $subexercise['processorParameterList'] !== ''){
                    $processorParameter = $subexercise['processorParameterList'];
                    foreach ($processorParameter as $tempKey => $Data) {
                        $Data2 = array();
                        foreach ($Data as &$dat)
                            if ($dat!=='')
                                $Data2[] = $dat;
                            
                        if (isset($tempProcessors[$tempKey]))
                            $tempProcessors[$tempKey]->setParameter(implode(' ',array_values($Data2)));                   
                    }
                }

                $processors = array_merge($processors,$tempProcessors);
            }
        }

        $processes = json_decode(Process::encodeProcess($processors),true);

        #############################
        ### end create_processors ###
        #############################
        #endregion
        
    }

    $sheet_data['exercises'] = json_decode(Exercise::encodeExercise($exercises),true);
}




if (isset($_POST['action'])) {// && $_POST['action'] == "new"
    $timestamp = time();
    $errorInSent=false;

    // validate all sheet data
    $f = new FormEvaluator($_POST);
    $f->checkStringForKey('sheetName',
                          FormEvaluator::REQUIRED,
                          'error',
                          'Ungültiger Blattname.');
    $f->checkStringForKey('startDate',
                          FormEvaluator::REQUIRED,
                          'warning',
                          'Leerer Bearbeitungsanfang.');
    $f->checkStringForKey('endDate',
                          FormEvaluator::REQUIRED,
                          'warning',
                          'Leerer Bearbeitungsende.');

    // check if defaultGroupSize is bigger than standard groupsize 10
    if ($createsheetData['user']['courses'][0]['course']['defaultGroupSize'] < 10) {
        $maxgroup = 10;
    } else {
        $maxgroup = $createsheetData['user']['courses'][0]['course']['defaultGroupSize'];
    }

    $f->checkIntegerForKey('groupSize',
                           FormEvaluator::REQUIRED,
                           'warning',
                           'Ungültige Gruppenstärke.',
                           array('min' => 0,'max' => $maxgroup));
    /*$f->checkArrayOfArraysForKey('exercises',
                                 FormEvaluator::REQUIRED,
                                 'warning',
                                 'Bitte erstellen Sie mindestens eine Aufgabe.');*/

    // check if startDate is not later than endDate and if it matches format
    $correctDates = true;
    /*if (strtotime(str_replace(" - ", " ", $_POST['startDate'])) > strtotime(str_replace(" - ", " ", $_POST['endDate']))
        || !preg_match("#^\d\d.\d\d.\d\d\d\d - \d\d:\d\d$#", $_POST['startDate'])
        || !preg_match("#^\d\d.\d\d.\d\d\d\d - \d\d:\d\d$#", $_POST['endDate'])) {
        $correctDates = false;
        $errormsg = "Überprüfen Sie Bearbeitungsanfang sowie Bearbeitungsende!";
        array_push($notifications, MakeNotification('warning', $errormsg));
    }*/

    // check if sheetPDF is given
    $noFile = false;
    ///if ($_FILES['sheetPDF']['error'] == 4) {
    ///    $noFile = true;
    ///    $errormsg = "Bitte laden Sie ein Übungsblatt (PDF) hoch.";
    ///    array_push($notifications, MakeNotification('warning', $errormsg));
    ///}

    // validate subtasks
    $correctExercise = true;
    //$validatedExercises = array();
    /*if (isset($_POST['exercises']) == true && empty($_POST['exercises']) == false) {
        foreach ($_POST['exercises'] as $key1 => $exercise) {
            if ($correctExercise == false) {
                break;
            }
            // evaluate if subexercises per exercise isnt empty
            $eval = new FormEvaluator($exercise);
            
            $eval->checkArrayOfArraysForKey('subexercises',
                                    FormEvaluator::REQUIRED,
                                    'warning',
                                    'Ungültige Anzahl an Teilaufgaben.');
                                    
            if ($eval->evaluate(true)) {
                // clean Exercises
                $foundValues = $eval->foundValues;
                array_push($validatedExercises, $foundValues['subexercises']);
                // evaluate subexercises
                foreach ($exercise['subexercises'] as $key2 => $subexercise) {
                    // evaluate given subexercises
                    $subeval = new FormEvaluator($subexercise);
                    $subeval->checkIntegerForKey('maxPoints',
                                                 FormEvaluator::REQUIRED,
                                                 'warning',
                                                 'Ungültige Punkteanzahl angegeben.',
                                                 array('min' => 0));
                    if ($subeval->evaluate() == false) {
                        $notifications = array_merge($notifications, $subeval->notifications);
                        $correctExercise = false;
                        break;
                    }
                    // evaluate ExerciseTypes
                    if (preg_match("#[0-9]+b?$#", $subexercise['exerciseType']) == false) {
                        $errormsg = "Falsche Aufgabentypen.";
                        array_push($notifications, MakeNotification('warning', $errormsg));
                        $correctExercise = false;
                        break;
                    }

                    // evaluate mime-types
                    $mimeTypes = array();
                    if (!isset($subexercise['type']) && isset($subexercise['mime-type'])){    
                        $mimeTypesForm = explode(",", $subexercise['mime-type']);
                        foreach ($mimeTypesForm as &$mimeType) {
                            if ($mimeType=='')continue;
                            $mimeType = explode('.',trim(strtolower($mimeType)));
                            $ending=isset($mimeType[1]) ? $mimeType[1] : null;
                            $mimeType=$mimeType[0];
                            
                            if (FILE_TYPE::checkSupportedFileType($mimeType) == false) {
                                $errormsg = "Sie haben eine nicht unterstützte Dateiendung verwendet.";
                                array_push($notifications, MakeNotification('warning', $errormsg));
                                $correctExercise = false;
                                break;
                            } else { // if mime-type is supported add mimeTypes
                                $mimes = FILE_TYPE::getMimeTypeByFileEnding(trim(strtolower($mimeType)));
                                if ($ending!=null)
                                    foreach ($mimes as &$mime){
                                        $mime.=' *.'.$ending;
                                    }
                                $mimeTypes = array_merge($mimeTypes, $mimes);
                            }
                        }
                    }
                    
                    // save mimeTypes in validated Exercises
                    $validatedExercises[$key1][$key2]['mime-type'] = $mimeTypes;
                }
            }  else {
                $notifications = array_merge($notifications, $eval->notifications);
                $correctExercise = false;
                break;
            }
        }
    }*/

    // only if validation was correct
    //$ready = $f->evaluate(true);
    $ready=true;
    if ($ready == true && $noFile == false && $correctExercise == true && $correctDates == true) {

        // encode to JSON
        $myExerciseSheetJSON = ExerciseSheet::encodeExerciseSheet($myExerciseSheet);
        
        // Post ExcercisSheet to logic Controllers to create it and get saved data
        $output = http_post_data($logicURI."/exercisesheet", $myExerciseSheetJSON, true, $message);
        $output = json_decode($output, true);


        // create subtasks as exercise
        if ($message == 201) {
            if (isset($output['id'])) {
                    $id = $output['id'];
                    $sid=$id;
            }
            
            if ($sheet_data_old!==null){
                // get removed exercises
                if (isset($sheet_data_old['exercises'])){
                    function comp_func_cr($a, $b) {if (!isset($b['id'])) return 1; if ($a['id'] === $b['id']) return 0; return ($a['id'] > $b['id'])? 1:-1; }
                    
                    $oldExercises = $sheet_data_old['exercises'];
                    foreach ($oldExercises as $ex){
                        if (isset($ex['attachments'])){
                            $oldAttachments = $ex['attachments'];
                        
                            foreach ($sheet_data['exercises'] as $ex2){
                                if ($ex2['id'] == $ex['id']){
                                    if (!isset($ex2['attachments'])) $ex2['attachments'] = array();
                                    $result = array_udiff($oldAttachments, $ex2['attachments'],"comp_func_cr");
                                        foreach ($result as $del){
                                            if (!isset($del['fileId'])) continue;
                                            $URL = $databaseURI . "/attachment/exercise/{$ex['id']}/file/{$del['fileId']}";
                                            ///echo $URL;
                                            $removed = http_delete($URL, true);
                                            if (isset($removed['status']) && $removed['status']==201){
                                                // removed
                                            } else {
                                                // isn't removed
                                            }
                                        }
                                    break;
                                }
                            }
                        }
                    }

                    $result = array_udiff($oldExercises, $sheet_data['exercises'],"comp_func_cr");
                    // remove exercises
                    foreach ($result as $ex){
                        $URL = $databaseURI . "/exercise/exercise/{$ex['id']}";
                        $removed = http_delete($URL, true);
                        if (isset($removed['status']) && $removed['status']==201){
                            // removed
                        } else {
                            // isn't removed
                        }
                    }
                }
            }
    
            $sheetId = (isset($_POST['sheetId']) ? $_POST['sheetId'] : (isset($sid) ? $sid : null));
            $URL = $databaseURI . "/exercisefiletype/exercisesheet/{$sheetId}";
            $removed = http_delete($URL, true);
            
            $exerciseMap = array();
            foreach ($exercises as $key => &$exercise){
                $exercise->setSheetId($sheetId);
                $exerciseMap[$key] = array($key, $exercise->getId());
                if ($exercise->getId()<0)
                    $exercise->setId(null);
            }

            // Post Excercise to logic Controller to create it
            $exercisesJSON = Exercise::encodeExercise($exercises);
            ///echo $exercisesJSON;
            $output2 = http_post_data($logicURI."/exercise", $exercisesJSON, true, $message);
            if ($message == 201) {
               
            
            $exercises2 = Exercise::decodeExercise($output2);
            foreach ($exercises2 as $key => &$exercise2){
                if ($exercises[$key]->getId()!==null){
                    $exerciseMap[$key][] = $exercises[$key]->getId();
                } else 
                    $exerciseMap[$key][] = $exercise2->getId();
                if ($exercise2->getId()!==null)
                    $exercises[$key]->setId($exercise2->getId());
            }
                                         
            #region create_forms
            ##########################
            ### begin create_forms ###
            ##########################
            if ($errorInSent == false) {
                $formMap = array();
                foreach ($forms as &$form){
                    if (!isset($form['formId'])) $form['formId'] = null;
                    $formMap[$key] = array($key, $form['formId']);
                    if ($form['formId']<0)
                        $form['formId']=null;
                    $form['exerciseId'] = unmap($exerciseMap,$form['exerciseId']);
                }
                
                function comp_func2($a, $b) {if (!isset($b['formId'])) return 1; if ($a['formId'] === $b['formId']) return 0; return ($a['formId'] > $b['formId'])? 1:-1; }   
                $result = array_udiff($form_data_old, $forms,"comp_func2");
                // remove exercises
                foreach ($result as $ex){
                    $URL = $databaseURI . "/form/{$ex['formId']}";
                    $removed = http_delete($URL, true);
                    if (isset($removed['status']) && $removed['status']==201){
                        // removed
                    } else {
                        // isn't removed
                    }
                }
                
                if (!empty($forms)){
                    // upload forms
                    $URL = $serverURI."/logic/LForm/form";
                    ///echo Form::encodeForm($forms);
                    http_post_data($URL, Form::encodeForm($forms), true, $message);
                    if ($message != 201) {
                        $errorInSent = true;
                    }
                }
            }
            
            ########################
            ### end create_forms ###
            ########################
            #endregion
            
            #region create_processors
            ###############################
            ### begin create_processors ###
            ###############################
            if ($errorInSent == false) {
                $processesMap = array();
                foreach ($processes as &$process){
                    if (!isset($process['processId'])) $process['processId'] = null;
                    $processesMap[$key] = array($key, $process['processId']);
                    if ($process['processId']<0)
                        $process['processId']=null;
                    $process['exercise']['id'] = unmap($exerciseMap,$process['exercise']['id']);
                }
                
                function comp_func3($a, $b) {if (!isset($b['processId'])) return 1; if ($a['processId'] === $b['processId']) return 0; return ($a['processId'] > $b['processId'])? 1:-1; }   
                $result = array_udiff($process_data_old, $processes,"comp_func3");
                // remove exercises
                foreach ($result as $ex){
                    $URL = $databaseURI . "/process/{$ex['processId']}";
                    $removed = http_delete($URL, true);
                    if (isset($removed['status']) && $removed['status']==201){
                        // removed
                    } else {
                        // isn't removed
                    }
                }
                
                if (!empty($processes)){
                    // upload processors
                    $URL = $serverURI."/logic/LProcessor/process";
                    http_post_data($URL, Process::encodeProcess($processes), true, $message);

                    if ($message != 201) {
                        $errorInSent = true; 
                    }
                }
            }
            
            #############################
            ### end create_processors ###
            #############################
            #endregion

            } else {
                $errorInSent = true;
            }
            
            if ($errorInSent == false) {
                $errormsg = "Die Serie wurde ".($_POST['action']=='edit' ? 'bearbeitet' : 'erstellt').".";
                array_push($notifications, MakeNotification('success', $errormsg));
            } else {
                $errormsg = "Beim ".($_POST['action']=='edit' ? 'Bearbeiten' : 'Erstellen')." ist ein Fehler aufgetreten.";
                array_push($notifications, MakeNotification('error', $errormsg));

                // delete exercisesheet if exercises are going wrong
                if ($_POST['action']=='new')
                    http_delete($logicURI.'/DB/exercisesheet/exercisesheet/'.$output['id'], true, $message);
            }
        } else {
            $errormsg = "Beim ".($_POST['action']=='edit' ? 'Bearbeiten' : 'Erstellen')." ist ein Fehler aufgetreten.";
            array_push($notifications, MakeNotification('error', $errormsg));
        }
    }  else {
        $notifications = array_merge($notifications, $f->notifications);
    }
}

if (isset($sid)){
    $URL = $databaseURI . "/exercisesheet/exercisesheet/{$sid}/exercise";
    $sheet_data = http_get($URL, true);
    $sheet_data = json_decode($sheet_data, true);
}

Authentication::checkRights(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $createsheetData['user']);

$menu = MakeNavigationElement($createsheetData['user'],
                              PRIVILEGE_LEVEL::LECTURER,true);
                              
// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($createsheetData['user']);
$h->bind(array("name" => $createsheetData['user']['courses'][0]['course']['name'],
               "notificationElements" => $notifications,
               "navigationElement" => $menu));



$sheetSettings = Template::WithTemplateFile('include/CreateSheet/SheetSettings.template.html');
$createExercise = Template::WithTemplateFile('include/CreateSheet/CreateExercise.template.html');

$sheetSettings->bind($createsheetData['user']);

if (isset($cid))
    $sheetSettings->bind(array('cid'=>$cid));
if (isset($uid))
    $sheetSettings->bind(array('uid'=>$uid));
if (isset($sid)){
    $sheetSettings->bind(array('sid'=>$sid));
    
   // if (!isset($_POST['action']) || $_POST['action']=='new'){
        $result = http_get($serverURI."/DB/DBForm/form/exercisesheet/{$sid}",true);
        $forms = json_decode($result,true);
        
        $result = http_get($serverURI."/DB/DBProcess/process/exercisesheet/{$sid}",true);
        $processes = json_decode($result,true);
   // }
}

if (isset($processes))
    $sheetSettings->bind(array('processes'=>$processes));
if (isset($forms))
    $sheetSettings->bind(array('forms'=>$forms));
$sheetSettings->bind($exerciseTypes);
$sheetSettings->bind($processorModules);    

if (isset($sheet_data))
    $sheetSettings->bind($sheet_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $sheetSettings, $createExercise);
$w->defineForm(basename(__FILE__)."?cid=".$cid.(isset($sid) ? "&sid={$sid}" : ''), true, $sheetSettings, $createExercise);
$w->set_config_file('include/configs/config_createSheet.json');
$w->show();
