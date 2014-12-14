<?php
/**
 * @file MarkingTool.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */
///echo count($_REQUEST['exercises'],COUNT_RECURSIVE);
include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';
include_once 'include/FormEvaluator.php';

$timestamp = time();

/**
 * Creates a submission.
 * The submission contains a dummy file for consistency reasons
 * which isn't shown to anyone by setting the 'hideFile' flag 
 *
 * @param $leaderID The userID of the group leader
 * @param $eID The id of the exercisesheet
 *
 * @return Returns the submission on success, NULL otherwise 
 */
function createSubmission($leaderID, $eID)
{
    global $databaseURI;
    global $filesystemURI;
    global $timestamp;

    // creates the new submission including the dummy file
    $newSubmission = Submission::createSubmission(null,
                                                  $leaderID,
                                                  null,
                                                  $eID,
                                                  null,
                                                  1,
                                                  $timestamp,
                                                  null,
                                                  null,
                                                  true);

    $newSubmission = Submission::encodeSubmission($newSubmission);

    $URI = $databaseURI . "/submission";
    $submission = http_post_data($URI, $newSubmission, true, $message);

    if ($message != "201") {
        return NULL;
    }

    $submission = json_decode($submission, true);
    $submissionID = $submission['id'];

    // makes the currently created submission selected
    updateSelectedSubmission($databaseURI,
                             $leaderID,
                             $submissionID,
                             $eID,
                             $message);

    if ($message != "201") {
        return NULL;
    }

    return $submission;
}


/**
 * Creates a marking to an already existing submission.
 * The marking contains a dummy file for consistency reasons
 * which isn't shown to anyone by setting the 'hideFile' flag 
 *
 * @param $points The points of the marking
 * @param $tutorComment The tutor's comment
 * @param $status The status of the marking
 * @param $submissionID The id of the submission that belongs to the marking
 * @param $tutorID The id of the tutor who creates the marking
 *
 * @return bool Returns the marking on success, NULL otherwise 
 */
function createMarking($points, $tutorComment, $status, $submissionID, $tutorID)
{
    global $databaseURI;
    global $filesystemURI;
    global $timestamp;

        // creates the new marking including the dummy file
        $newMarking = Marking::createMarking(null,
                                             $tutorID,
                                             null,
                                             $submissionID,
                                             $tutorComment,
                                             null,
                                             $status,
                                             $points,
                                             $timestamp,
                                             true);

        $newMarking = Marking::encodeMarking($newMarking);
        $URI = $databaseURI . "/marking";
        $marking = http_post_data($URI, $newMarking, true, $message);

        if ($message != "201") {
            return NULL;
        }

    return $marking;
}


/**
 * Stores a marking in the database.
 *
 * @param $points The points of the marking
 * @param $tutorComment The tutor's comment
 * @param $status The status of the marking
 * @param $submissionID The id of the submission, if set, -1 otherwise
 * @param $markingID The id of the marking, if set, -1 otherwise
 * @param $leaderID The id of the group leader
 * @param $tutorID The id of the tutor who creates the marking
 * @param $eID The id of the exercisesheet
 *
 * @return bool Returns true on success, false otherwise 
 */
function saveMarking($points, $tutorComment, $status, $submissionID, $markingID, $leaderID, $tutorID, $eID)
{
    global $databaseURI;
    global $timestamp;

    // submission and marking already exist and don't 
    // need to be created before adding the marking data
    if (($submissionID != -1 && $markingID != -1)) {
        $newMarking = Marking::createMarking($markingID, 
                                             null, 
                                             null, 
                                             null,
                                             $tutorComment,
                                             null,
                                             $status,
                                             $points,
                                             $timestamp);

        $newMarking = Marking::encodeMarking($newMarking);
        $URI = $databaseURI . "/marking/{$markingID}";
        http_put_data($URI, $newMarking, true, $message);

        if ($message != 201) {
            return false;
        } else {
            return true;
        }
    } elseif ($submissionID != -1 && $markingID == -1) {
        // only the submission exists, the marking still
        // needs to be created before adding the marking data

        // creates the marking in the database
        $marking = createMarking($points, $tutorComment, $status, $submissionID, $tutorID);
        if (empty($marking)) {
            return false;
        } else {
            return true;
        }
    } elseif (($submissionID == -1 && $markingID == -1)) {
        // neither the submission nor the marking exist - they both
        // need to be created before adding the marking data

        // creates the submission in the database
        $submission = createSubmission($leaderID, $eID);

        if (!empty($submission)) {
            // creates the marking in the database
            $submissionID = $submission['id'];
            $marking = createMarking($points, $tutorComment, $status, $submissionID, $tutorID);
            if (!empty($marking)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

// changes search settings
if (isset($_POST['action']) && $_POST['action'] == "ShowMarkingTool") {
    //if (isset($_POST['sheetID']) && isset($_POST['tutorID']) && isset($_POST['statusID'])) {
    if (isset($_POST['sheetID']))
        $sid = cleanInput($_POST['sheetID']);

    if (isset($_POST['tutorID']))
        if ($_POST['tutorID'] != "all") {
            $tutorID = cleanInput($_POST['tutorID']);
        }
    if (isset($_POST['statusID']))
        if ($_POST['statusID'] != "all") {
            $statusID = cleanInput($_POST['statusID']);
        }
    //}
}

if (isset($_GET['downloadCSV'])) {
    $sid = cleanInput($_GET['downloadCSV']);
}
if (isset($_GET['tutorID'])){
    if ($_GET['tutorID'] != "all") {
        $tutorID = cleanInput($_GET['tutorID']);
    }
}
if (isset($_GET['statusID']))
    if ($_GET['statusID'] != "all") {
        $statusID = cleanInput($_GET['statusID']);
    }

// saves marking changes of a groups
$GroupNotificationElements=array();

if (isset($_POST['MarkingTool'])) {
    $leaderID = cleanInput($_POST['MarkingTool']);
    $maxMarkingStatus = cleanInput($_POST['maxMarkingStatus']);
    $userName='???';
    foreach ($_POST['exercises'] as $key => $exercises) {
        if ($key == $leaderID) {

            // bool which is true if any error occured
            $RequestError = false;
            $hasChanged=false;

            foreach ($exercises as $exerciseId => $exercise) {
                $maxPoints = cleanInput($exercise['maxPoints']);
                $submissionID = cleanInput($exercise['submissionID']);
                $markingID = cleanInput($exercise['markingID']);
                $userName = (isset($exercise['user']) ? $exercise['user'] : '???');
                if (isset($exercise['points'])) $exercise['points'] = str_replace(',','.',$exercise['points']);
                
                $f = new FormEvaluator($exercise);

                $f->checkNumberForKey('points',
                                       FormEvaluator::REQUIRED,
                                       'warning',
                                       'Ungültige Punktzahl.',
                                       array('min' => 0, 'max' => $maxPoints));

                $f->checkStringForKey('tutorComment',
                                      FormEvaluator::OPTIONAL,
                                      'warning',
                                      'Ungültiger Kommentar.',
                                      array('min' => 1));

                /**
                 * @todo get maxStatusID for FormEvaluator.
                 */
                $f->checkIntegerForKey('status',
                                       FormEvaluator::REQUIRED,
                                       'warning',
                                       'Ungültiger Status.',
                                       array('min' => 0, 'max' => $maxMarkingStatus));

                if ($f->evaluate(true)) {
                    $foundValues = $f->foundValues;
                    $changed = false; 
                    
                    $points = (isset($foundValues['points']) ? $foundValues['points'] : null);
                    if ((!isset($exercise['oldPoints']) && $points!=null) || (isset($exercise['oldPoints']) && $points!=$exercise['oldPoints'])){
                          $changed=true;///echo "A";
                    }
                    
                    $tutorComment = (isset($foundValues['tutorComment']) ? $foundValues['tutorComment'] : '');
                    if (isset($exercise['oldTutorComment'])) $exercise['oldTutorComment']=htmlspecialchars($exercise['oldTutorComment']);
                    if ((!isset($exercise['oldTutorComment']) && isset($foundValues['tutorComment'])) || (isset($exercise['oldTutorComment']) && $tutorComment!=$exercise['oldTutorComment'])){
                          $changed=true;///echo "B";
                    }
                          
                    $status = (isset($foundValues['status']) ? $foundValues['status'] : null);
                    if ((!isset($exercise['oldStatus']) && isset($foundValues['status'])) || (isset($exercise['oldStatus']) && $status!=$exercise['oldStatus'])){
                          $changed=true;///echo "C";
                    }
                          
                    if ($changed){
                        $hasChanged=true;

                        if (!saveMarking($points, 
                                         $tutorComment, 
                                         $status, 
                                         $submissionID, 
                                         $markingID, 
                                         $leaderID, 
                                         $uid, 
                                         $exerciseId)) {
                            $RequestError = true;echo "FAIL";
                        }
                    }

                } else {
                    //$GroupNotificationElements[$key] = $notifications + $f->notifications;
                    //$RequestError = true;echo "OK";
                }
            }

            if ($hasChanged){
                if ($RequestError) {
                    //$msg = "Beim Speichern für ".$userName." ist ein Fehler aufgetreten.";
                    $msg = "Beim Speichern ist ein Fehler aufgetreten.";
                    if (!isset($GroupNotificationElements[$key])) $GroupNotificationElements[$key]=array();
                    $GroupNotificationElements[$key][] = MakeNotification("error", $msg);
                    
                } else {
                    //$msg = "Die Korrektur für ".$userName." wurde erfolgreich gespeichert.";
                    $msg = "Die Korrektur wurde erfolgreich gespeichert.";
                    if (!isset($GroupNotificationElements[$key])) $GroupNotificationElements[$key]=array();
                    $GroupNotificationElements[$key][] = MakeNotification("success", $msg);
                }
            }
        }
    }
}

if (!isset($tutorID) && !isset($_POST['action']) && !isset($_GET['downloadCSV']))
    $tutorID = $uid;

// create URI for GetSite
$URI = $getSiteURI . "/markingtool/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";

if (isset($tutorID)) {
    $URI .= "/tutor/{$tutorID}";
}

if (isset($statusID)) {
    $URI .= "/status/{$statusID}";
}

// load MarkingTool data from GetSite
$markingTool_data = http_get($URI, true);
$markingTool_data = json_decode($markingTool_data, true);

// sort users by given sort-mode
if (isset($_GET['sortUsers'])) $_POST['sortUsers'] = cleanInput($_GET['sortUsers']);
$dataList = array();
foreach ($markingTool_data['groups'] as $key => $group)
    $dataList[] = array('pos' => $key,'userName'=>$group['leader']['userName'],'lastName'=>$group['leader']['lastName'],'firstName'=>$group['leader']['firstName']);
$sortTypes = array('lastName','firstName','userName');
if (!isset($_POST['sortUsers'])) $_POST['sortUsers'] = null;
$_POST['sortUsers'] = (in_array($_POST['sortUsers'],$sortTypes) ? $_POST['sortUsers'] : $sortTypes[0]);
$sortTypes = array('lastName','firstName','userName');
$dataList=LArraySorter::orderby($dataList, $_POST['sortUsers'], SORT_ASC, $sortTypes[(array_search($_POST['sortUsers'],$sortTypes)+1)%count($sortTypes)], SORT_ASC);
$tempData = array();
foreach($dataList as $data)
    $tempData[] = $markingTool_data['groups'][$data['pos']];
$markingTool_data['groups'] = $tempData;

// download csv-archive
if (isset($_GET['downloadCSV'])) {
    $markings = array();
    $newMarkings=-1;
    foreach ($markingTool_data['groups'] as $key => $group){
        if (isset($group['exercises'])){
            foreach ($group['exercises'] as $key2 => $exercise){
                if (!isset($exercise['submission']['marking']) && (isset($tutorID) && $tutorID!='all') && (!isset($statusID) || ($statusID!=-1 && $statusID!=0))) continue;
                if (!isset($exercise['submission']) && ((isset($statusID) && $statusID!=0) || (isset($tutorID) && $tutorID!='all'))) continue;
        
                if (isset($exercise['submission'])){
                    if (isset($exercise['submission']['marking'])){
                        // submission + marking
                        $tempMarking = Marking::decodeMarking(Marking::encodeMarking($exercise['submission']['marking']));
                        $tempSubmission = Submission::decodeSubmission(Submission::encodeSubmission($exercise['submission']));
                        $tempMarking->setSubmission($tempSubmission);
                        $markings[] = $tempMarking;
                    } else {
                        // no marking
                        $tempMarking = Marking::createMarking($newMarkings,$uid,null,$exercise['submission']['id'],null,null,1,null,$timestamp,null);
                        $newMarkings--;
                        $tempSubmission = Submission::decodeSubmission(Submission::encodeSubmission($exercise['submission']));
                        $tempMarking->setSubmission($tempSubmission);
                        $markings[] = $tempMarking;
                    }
                } else {
                    // no submission
                    $tempMarking = Marking::createMarking($newMarkings,$uid,null,null,null,null,1,null,$timestamp,null);
                    $tempSubmission = Submission::createSubmission($newMarkings ,$group['leader']['id'],null,$exercise['id'],null,null,$timestamp,null,$group['leader']['id'],null);
                    $tempSubmission->setSelectedForGroup(1);
                    $newMarkings--;
                    $tempMarking->setSubmission($tempSubmission);
                    $markings[] = $tempMarking;
                }
            }
        }
    }

    $URI = $logicURI . '/tutor/archive/user/' . $uid . '/exercisesheet/' . $sid.'/withnames';
    $csvFile = http_post_data($URI, Marking::encodeMarking($markings), true);
    echo $csvFile;
    exit(0);
}

$markingTool_data['filesystemURI'] = $filesystemURI;

// adds the selected sheetID, tutorID and statusID
$markingTool_data['sheetID'] = $sid;
if (isset($tutorID)) {
    $markingTool_data['tutorID'] = $tutorID;
}

if (isset($statusID)) {
    $markingTool_data['statusID'] = $statusID;
}

if (isset($_POST['sortUsers'])) {
    $markingTool_data['sortUsers'] = $_POST['sortUsers'];
}

$markingTool_data['URI'] = $URI;
$markingTool_data['cid'] = $cid;

$user_course_data = $markingTool_data['user'];
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $user_course_data);
$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR,true);
                              
// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "navigationElement" => $menu));


$searchSettings = Template::WithTemplateFile('include/MarkingTool/MarkingToolSettings.template.html');
$searchSettings->bind($markingTool_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $searchSettings);

if (!empty($markingTool_data['groups'])) {
$allOutputs = 0;
$groups = $markingTool_data['groups'];
//unset($markingTool_data['groups']);
    $allOutputs=0;
    foreach ($groups as $group) {
        $anz=0;
        foreach ($group['exercises'] as $key => $exercise){
            $eid = $exercise['id'];
            if (!isset($exercise['submission']['marking']) && (isset($tutorID) && $tutorID!='all') && (!isset($statusID) || ($statusID!=-1 && $statusID!=0))) continue;
            if (!isset($exercise['submission']) && ((isset($statusID) && $statusID!=0) || (isset($tutorID) && $tutorID!='all'))) continue;
            $anz++;
            $allOutputs++;
        }
        if ($anz==0)continue;
    
        $markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingTool.template.html');
        $markingElement->bind($markingTool_data);
        $markingElement->bind(array('group'=>$group));
        if (isset($GroupNotificationElements[$group['leader']['id']])){
            $markingElement->bind(array('GroupNotificationElements'=>$GroupNotificationElements[$group['leader']['id']]));
            unset($GroupNotificationElements[$group['leader']['id']]);
        }
            
        $w->insert($markingElement);
        $w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $markingElement);
    }
    if ($allOutputs==0){
        $markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingToolEmpty.template.html');
        $markingElement->bind($markingTool_data);
        $w->insert($markingElement);
    }
} else {
        $markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingToolEmpty.template.html');
        $markingElement->bind($markingTool_data);
        $w->insert($markingElement);
}

if (!empty($GroupNotificationElements)){
    foreach ($GroupNotificationElements as $key => $notifs)
        $notifications = array_merge($notifications,$notifs);
}

$h->bind(array("notificationElements" => $notifications));

$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $searchSettings);
$w->set_config_file('include/configs/config_marking_tool.json');
$w->show();

?>