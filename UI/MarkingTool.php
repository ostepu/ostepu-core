<?php
/**
 * @file MarkingTool.php
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once 'include/Boilerplate.php';
include_once '../Assistants/Structures.php';
include_once 'include/FormEvaluator.php';


/**
 * Creates a 'dummy file' in the database.
 *
 * @return Returns the file on success, NULL otherwise 
 */
function createDummyFile()
{
    global $databaseURI;
    global $filesystemURI;

    /**
     * @todo Improve dummy file content.
     */

    // creates the dummy file
    $data = 'KeineEinsendung.txt';
    $handle = fopen($data, 'w');

    $data = base64_encode($data);
    $displayName = "Keine Einsendung";
    $timestamp = time();

    $file = array('timeStamp' => time(),
                  'displayName' => $displayName,
                  'body' => $data);

    // uploads the file to the filesystem
    $URL = $filesystemURI . '/file';
    $jsonFile = http_post_data($URL,
                               json_encode($file),
                               true,
                               $message);

    if ($message != "201") {
        return NULL;
    }

    // saves a reference to the file in the database.
    $fileObj = json_decode($jsonFile, true);

    $jsonFile = saveFileInDatabase($databaseURI,
                                   $fileObj,
                                   $message);

    if ($message != "201" && $message != "200") {
        return NULL;
    }

    return $jsonFile;
}

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

    $jsonFile = createDummyFile();

    if (!empty($jsonFile)) {
        $jsonFile = json_decode($jsonFile, true);
        $fileID = $jsonFile['fileId'];

        // creates the new submission including the dummy file
        $newSubmission = Submission::createSubmission(null,
                                                      $leaderID,
                                                      $fileID,
                                                      $eID,
                                                      null,
                                                      1,
                                                      time(),
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

    } else {
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

    $jsonFile = createDummyFile();

    if (!empty($jsonFile)) {
        $jsonFile = json_decode($jsonFile, true);
        $fileID = $jsonFile['fileId'];

        // creates the new marking including the dummy file
        $newMarking = Marking::createMarking(null,
                                             $tutorID,
                                             $fileID,
                                             $submissionID,
                                             $tutorComment,
                                             null,
                                             $status,
                                             $points,
                                             time(),
                                             true);

        $newMarking = Marking::encodeMarking($newMarking);
        $URI = $databaseURI . "/marking";
        $marking = http_post_data($URI, $newMarking, true, $message);

        if ($message != "201") {
            return NULL;
        }
    } else {
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

    // submission and marking already exist and don't 
    // need to be created before adding the marking data
    if ($submissionID != -1 && $markingID != -1) {
        $newMarking = Marking::createMarking($markingID, 
                                             null, 
                                             null, 
                                             null,
                                             $tutorComment,
                                             null,
                                             $status,
                                             $points,
                                             time());

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
    } elseif ($submissionID == -1 && $markingID == -1) {
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
    if (isset($_POST['sheetID']) && isset($_POST['tutorID']) && isset($_POST['statusID'])) {
        $sid = cleanInput($_POST['sheetID']);

        if ($_POST['tutorID'] != "all") {
            $tutorID = cleanInput($_POST['tutorID']);
        }

        if ($_POST['statusID'] != "all") {
            $statusID = cleanInput($_POST['statusID']);
        }
    }
}

// saves marking changes of a group
if (isset($_POST['MarkingTool'])) {
    $leaderID = cleanInput($_POST['MarkingTool']);
    $maxMarkingStatus = cleanInput($_POST['maxMarkingStatus']);

    foreach ($_POST['exercises'] as $key => $exercises) {
        if ($key == $leaderID) {

            // bool which is true if any error occured
            $RequestError = false;

            foreach ($exercises as $exerciseId => $exercise) {
                $maxPoints = cleanInput($exercise['maxPoints']);
                $submissionID = cleanInput($exercise['submissionID']);
                $markingID = cleanInput($exercise['markingID']);

                $f = new FormEvaluator($exercise);

                $f->checkIntegerForKey('points',
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

                    $points = (isset($foundValues['points']) ? $foundValues['points'] : null);
                    $tutorComment = (isset($foundValues['tutorComment']) ? $foundValues['tutorComment'] : null);
                    $status = (isset($foundValues['status']) ? $foundValues['status'] : null);

                    if (!saveMarking($points, 
                                     $tutorComment, 
                                     $status, 
                                     $submissionID, 
                                     $markingID, 
                                     $leaderID, 
                                     $uid, 
                                     $exerciseId)) {
                        $RequestError = true;
                    }

                } else {
                    $notifications = $notifications + $f->notifications;
                    $RequestError = true;
                }
            }

            if ($RequestError) {
                $msg = "Beim Speichern ist ein Fehler aufgetreten.";
                $notifications[] = MakeNotification("error", $msg);
            } else {
                $msg = "Die Korrektur wurde erfolgreich gespeichert.";
                $notifications[] = MakeNotification("success", $msg);
            }
        }
    }
}


// create URI for GetSite
$URI = $getSiteURI . "/markingtool/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";

if (isset($tutorID)) {
    $URI .= "/tutor/{$tutorID}";
}

if (isset($statusID)) {
    $URI .= "/status/{$statusID}";
}
//echo "$URI";return;

// load MarkingTool data from GetSite
$markingTool_data2 = http_get($URI, true);
$markingTool_data = json_decode($markingTool_data2, true);

// download csv-archive
if (isset($_POST['downloadCSV'])) {
    ///echo $markingTool_data2;return;
    $markings = array();
    $newMarkings=-1;
    foreach ($markingTool_data['groups'] as $key => $group){
        if (isset($group['exercises'])){
            foreach ($group['exercises'] as $key2 => $exercise){
                if (isset($exercise['submission'])){
                    if (isset($exercise['submission']['marking'])){
                    ///echo "found2";
                        // submission + marking
                        $tempMarking = Marking::decodeMarking(Marking::encodeMarking($exercise['submission']['marking']));
                        $tempSubmission = Submission::decodeSubmission(Submission::encodeSubmission($exercise['submission']));
                        $tempMarking->setSubmission($tempSubmission);
                        $markings[] = $tempMarking;
                    } else {
                    ///echo "found";
                        // no marking
                        $tempMarking = Marking::createMarking($newMarkings,$uid,null,$exercise['submission']['id'],null,null,1,null,time(),null);
                        $newMarkings--;
                        $tempSubmission = Submission::decodeSubmission(Submission::encodeSubmission($exercise['submission']));
                        $tempMarking->setSubmission($tempSubmission);
                        $markings[] = $tempMarking;
                    }
                } else {
                    // no submission
                        $tempMarking = Marking::createMarking($newMarkings,$uid,null,null,null,null,1,null,time(),null);
                        $tempSubmission = Submission::createSubmission( $newMarkings,$group['leader']['id'],null,$exercise['id'],null,null,time(),null,$group['leader']['id'],null);
                        $newMarkings--;
                        $tempMarking->setSubmission($tempSubmission);
                        $markings[] = $tempMarking;
                }
            }
        }
    }
    
    foreach ($markings as $marking){
        if ($marking->getFile()!==array() && $marking->getFile()!==null)
            $marking->setFile();
        if ($marking->getTutorComment() !== null)
            $marking->setTutorComment();
        if ($marking->getSubmission()!==array() && $marking->getSubmission()!==null && ($marking->getSubmission()->getFile()!==array() && $marking->getSubmission()->getFile()!==null))
            $marking->getSubmission()->setFile();
        if ($marking->getSubmission()!==array() && $marking->getSubmission()!==null && ($marking->getSubmission()->getComment()!==array() && $marking->getSubmission()->getComment()!==null))
            $marking->getSubmission()->setComment();
    }
    echo Marking::encodeMarking($markings);return;
}

$dataList = array();
foreach ($markingTool_data['groups'] as $key => $group)
    $dataList[] = array('pos' => $key,'userName'=>$group['leader']['userName'],'lastName'=>$group['leader']['lastName'],'firstName'=>$group['leader']['firstName']);
$sortTypes = array('lastName','firstName','userName');
if (!isset($_POST['sortUsers'])) $_POST['sortUsers'] = null;
$_POST['sortUsers'] = (in_array($_POST['sortUsers'],$sortTypes) ? $_POST['sortUsers'] : $sortTypes[0]);
$dataList=LArraySorter::orderby($dataList, $_POST['sortUsers'], SORT_ASC);
$tempData = array();
foreach($dataList as $data)
    $tempData[] = $markingTool_data['groups'][$data['pos']];
$markingTool_data['groups'] = $tempData;

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

$user_course_data = $markingTool_data['user'];


// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array("name" => $user_course_data['courses'][0]['course']['name'],
               "notificationElements" => $notifications));


$searchSettings = Template::WithTemplateFile('include/MarkingTool/MarkingToolSettings.template.html');
$searchSettings->bind($markingTool_data);

$markingElement = Template::WithTemplateFile('include/MarkingTool/MarkingTool.template.html');
$markingElement->bind($markingTool_data);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $searchSettings, $markingElement);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $searchSettings);
$w->defineForm(basename(__FILE__)."?cid=".$cid."&sid=".$sid, false, $markingElement);
$w->set_config_file('include/configs/config_default.json');
$w->show();

?>