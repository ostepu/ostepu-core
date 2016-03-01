<?php
/**
 * @file TutorUpload.php
 * Shows a form for tutors for uploading markings.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 */

ob_start();

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/Language.php';
include_once dirname(__FILE__) . '/../Assistants/MimeReader.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $globalUserData);

$langTemplate='TutorUpload_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('action',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'TutorUpload'],
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','invalidAction', $langTemplate)]]);
$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $postResults['action'] !== 'noAction') {
    if ($postResults['action'] === 'TutorUpload') {
        $filesTutorUploadValidation = Validation::open($_FILES, array('preRules'=>array()))
          ->addSet('MarkingFile',
                   ['satisfy_file_isset',
                    'satisfy_file_exists',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','missingMarkingFile', $langTemplate)]])
          ->addSet('MarkingFile',
                   ['satisfy_file_extension'=>'zip',
                    'satisfy_file_mime'=>'application/zip',
                    'on_error'=>['type'=>'error',
                                 'text'=>Language::Get('main','invalidFileType', $langTemplate)]]);

        $foundValues = $filesTutorUploadValidation->validate();
        $notifications = array_merge($notifications,$filesTutorUploadValidation->getPrintableNotifications('MakeNotification'));
        $filesTutorUploadValidation->resetNotifications()->resetErrors();

        if ($filesTutorUploadValidation->isValid()){
            $file = $foundValues['MarkingFile'];
            $error = $file['error'];

            $filePath = $file['tmp_name'];
            $displayName = $file['name'];

            // creates the JSON object containing the file
            $file = new File();
            $file->setBody( Reference::createReference($filePath) );
            $file->setTimeStamp(time());
            $file->setDisplayName($displayName);

            $file = File::encodeFile($file);

            // sends the JSON object to the logic
            $URI = $logicURI . "/tutor/user/{$uid}/course/{$cid}";

            $error = http_post_data($URI, $file, true, $message);

            if ($message === 201 || $message === 200) {
                $successmsg = Language::Get('main','sucessFileUpload', $langTemplate);
                $notifications[] = MakeNotification('success',
                                                    $successmsg);
            } else {
                $errors = @json_decode($error);
                if ($errors !== null){
                    foreach ($errors as $err){
                        $notifications[] = MakeNotification('error',
                                                        $err);
                    }
                } else {
                    $errormsg = Language::Get('main','unknownError', $langTemplate);
                    $notifications[] = MakeNotification('error',
                                                        $errormsg);
                }
            }
        }
    }
}

// load tutorUpload data from GetSite
$URL = $getSiteURI . "/tutorupload/user/{$uid}/course/{$cid}";
$tutorUpload_data = http_get($URL, true);
$tutorUpload_data = json_decode($tutorUpload_data, true);
$tutorUpload_data['filesystemURI'] = $filesystemURI;
$tutorUpload_data['cid'] = $cid;

$user_course_data = $tutorUpload_data['user'];

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR,
                              true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => (isset($user_course_data['courses'][0]['course']['name']) ? $user_course_data['courses'][0]['course']['name'] : null),
               'notificationElements' => $notifications,
               'navigationElement' => $menu));

// construct a content element for uploading markings
$tutorUpload = Template::WithTemplateFile('include/TutorUpload/TutorUpload.template.html');
$tutorUpload->bind($tutorUpload_data);

$w = new HTMLWrapper($h, $tutorUpload);
$w->set_config_file('include/configs/config_upload_exercise.json');
$w->show();

ob_end_flush();