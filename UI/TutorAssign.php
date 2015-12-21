<?php
/**
 * @file TutorAssign.php
 * Constructs the page for managing tutor assignments.
 *
 * @author Felix Schmidt
 * @author Florian Lücke
 * @author Ralf Busch
 */

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/include/FormEvaluator.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/LArraySorter.php';
include_once dirname(__FILE__) . '/../Assistants/Validation/Validation.php';

global $globalUserData;
Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $globalUserData);

$langTemplate='TutorAssign_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('sortUsers',
           ['satisfy_in_list'=>['lastName','firstName','userName'],
            'set_default'=>'lastName',
            'on_error'=>['type'=>'error',
                         'text'=>'invalidSortUsers']])
  ->addSet('actionSortUsers',
           ['set_default'=>'noAction',
            'satisfy_in_list'=>['noAction', 'sort'],
            'on_error'=>['type'=>'error',
                         'text'=>'invalidActionSortUser']]);
$valResults = $postValidation->validate();
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && $valResults['actionSortUsers'] === 'noAction'){
    $postActionValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
      ->addSet('action',
               ['set_default'=>'noAction',
                'satisfy_in_list'=>['noAction', 'AssignMakeWarning', 'AssignMake', 'AssignManually', 'AssignRemoveWarning', 'AssignRemove', 'AssignAutomatically'],
                'on_error'=>['type'=>'error',
                             'text'=>Language::Get('main','invalidAction', $langTemplate)]]);
    $postActionResult = $postActionValidation->validate();
    $notifications = array_merge($notifications,$postActionValidation->getPrintableNotifications('MakeNotification'));
    $postActionValidation->resetNotifications()->resetErrors();

    if ($postActionValidation->isValid()){
        $assignManuallyNotifications = array();
        $assignAutomaticallyNotifications = array();
        $assignRemoveNotifications = array();
        $assignMakeNotifications = array();

        if ($postActionResult['action'] === 'AssignManually'){
            include_once dirname(__FILE__) . '/include/TutorAssign/controller/AssignManually.php';
        }

        if ($postActionResult['action'] === 'AssignAutomatically'){
            include_once dirname(__FILE__) . '/include/TutorAssign/controller/AssignAutomatically.php';
        }

        if ($postActionResult['action'] === 'AssignRemoveWarning'){
            $assignRemoveNotifications[] = MakeNotification('warning', Language::Get('main','askUnassign', $langTemplate));
        }

        if ($postActionResult['action'] === 'AssignRemove'){
            include_once dirname(__FILE__) . '/include/TutorAssign/controller/AssignRemove.php';
        }

        if ($postActionResult['action'] === 'AssignMakeWarning'){
            $assignMakeNotifications[] = MakeNotification('warning', Language::Get('main','askMake', $langTemplate));
        }

        if ($postActionResult['action'] === 'AssignMake'){
            include_once dirname(__FILE__) . '/include/TutorAssign/controller/AssignMake.php';
        }
    }
}

// load user data from the database
$URL = $getSiteURI . "/tutorassign/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
$tutorAssign_data = http_get($URL, true);
$tutorAssign_data = json_decode($tutorAssign_data, true);

foreach ($tutorAssign_data['tutorAssignments'] as $key2 => $tutorAssignment){
    if (count($tutorAssign_data['tutorAssignments'][$key2]['submissions'])>0){
        $assignments = $tutorAssignment['submissions'];
        $dataList = array();
        $sortUsersValue = 'lastName';
        if ($postValidation->isValid()){
            $sortUsersValue = $valResults['sortUsers'];
        }

        foreach ($assignments as $key => $submission)
            $dataList[] = array('pos' => $key,'userName'=>$submission['user']['userName'],'lastName'=>$submission['user']['lastName'],'firstName'=>$submission['user']['firstName']);
        $sortTypes = array('lastName','firstName','userName');
        $dataList=LArraySorter::orderby($dataList, $sortUsersValue, SORT_ASC,$sortTypes[(array_search($sortUsersValue,$sortTypes)+1)%count($sortTypes)], SORT_ASC);
        $tempData = array();
        foreach($dataList as $data)
            $tempData[] = $tutorAssign_data['tutorAssignments'][$key2]['submissions'][$data['pos']];

        $tutorAssign_data['tutorAssignments'][$key2]['submissions'] = $tempData;
    }

    if (!isset($tutorAssign_data['tutorAssignments'][$key2]['proposalSubmissions'])) continue;
    $assignments = $tutorAssignment['proposalSubmissions'];
    $dataList = array();
    $sortUsersValue = 'lastName';
    if ($postValidation->isValid()){
        $sortUsersValue = $valResults['sortUsers'];
    }

    foreach ($assignments as $key => $submission)
        $dataList[] = array('pos' => $key,'userName'=>$submission['user']['userName'],'lastName'=>$submission['user']['lastName'],'firstName'=>$submission['user']['firstName']);
    $sortTypes = array('lastName','firstName','userName');
    $dataList=LArraySorter::orderby($dataList, $sortUsersValue, SORT_ASC,$sortTypes[(array_search($sortUsersValue,$sortTypes)+1)%count($sortTypes)], SORT_ASC);
    $tempData = array();
    foreach($dataList as $data)
        $tempData[] = $tutorAssign_data['tutorAssignments'][$key2]['proposalSubmissions'][$data['pos']];

    $tutorAssign_data['tutorAssignments'][$key2]['proposalSubmissions'] = $tempData;

}

function custom_sort($a,$b) {
    if (!isset($a['tutor']['courses']['0']['status'])) return 1;
    if (!isset($b['tutor']['courses']['0']['status'])) return 1;
    if ($a['tutor']['courses']['0']['status']==$b['tutor']['courses']['0']['status']){
        if (!isset($a['tutor']['lastName']) || !isset($a['tutor']['lastName'])) return 0;
        return $a['tutor']['lastName']>$b['tutor']['lastName'];
    } else
        return $a['tutor']['courses']['0']['status']>$b['tutor']['courses']['0']['status'];
}
usort($tutorAssign_data['tutorAssignments'], 'custom_sort');

$user_course_data = $tutorAssign_data['user'];

if (isset($sortUsersValue)) {
    $tutorAssign_data['sortUsers'] = $sortUsersValue;
}

foreach($tutorAssign_data['emptyGroups'] as $exercise => $emptyGroups){
    $dataList = array();
    foreach ($emptyGroups as $key => $group)
        $dataList[] = array('pos' => $key,'userName'=>(isset($group['userName'])?$group['userName']:null),'lastName'=>(isset($group['lastName'])?$group['lastName']:null),'firstName'=>(isset($group['firstName'])?$group['firstName']:null));
    $sortTypes = array('lastName','firstName','userName');
    $dataList=LArraySorter::orderby($dataList, $sortUsersValue, SORT_ASC, $sortTypes[(array_search($sortUsersValue,$sortTypes)+1)%count($sortTypes)], SORT_ASC);
    $tempData = array();
    foreach($dataList as $data)
        $tempData[] = $emptyGroups[$data['pos']];
    $tutorAssign_data['emptyGroups'][$exercise] = $tempData;
}

$menu = MakeNavigationElement($user_course_data,
                              PRIVILEGE_LEVEL::TUTOR,true);

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($user_course_data);
$h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
               'notificationElements' => $notifications,
               'navigationElement' => $menu));

// construct a content element for assigning tutors automatically
$assignAutomatically = Template::WithTemplateFile('include/TutorAssign/AssignAutomatically.template.html');
$assignAutomatically->bind($tutorAssign_data);
if (isset($assignAutomaticallyNotifications))
    $assignAutomatically->bind(array('AssignAutomaticallyNotificationElements' => $assignAutomaticallyNotifications));

// construct a content element for assigning tutors manually
$assignManually = Template::WithTemplateFile('include/TutorAssign/AssignManually.template.html');
$assignManually->bind($tutorAssign_data);
if (isset($assignManuallyNotifications))
    $assignManually->bind(array('AssignManuallyNotificationElements' => $assignManuallyNotifications));

// construct a content element for removing assignments from tutors
$assignRemove = Template::WithTemplateFile('include/TutorAssign/AssignRemove.template.html');
if (isset($assignRemoveNotifications))
    $assignRemove->bind(array('AssignRemoveNotificationElements' => $assignRemoveNotifications));

// construct a content element for creating submissions for unsubmitted users
$assignMake = Template::WithTemplateFile('include/TutorAssign/AssignMake.template.html');
$assignMake->bind($tutorAssign_data);
if (isset($assignMakeNotifications))
    $assignMake->bind(array('AssignMakeNotificationElements' => $assignMakeNotifications));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignAutomatically, $assignManually, $assignMake, $assignRemove);
$w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $assignAutomatically);
$w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $assignManually);
$w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $assignMake);
$w->defineForm(basename(__FILE__).'?cid='.$cid.'&sid='.$sid, false, $assignRemove);
$w->set_config_file('include/configs/config_tutor_assign.json');
//$w->set_config_file('include/configs/config_default.json');
$w->show();

