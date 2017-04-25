<?php
ob_start();

include_once dirname(__FILE__) . '/include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';
include_once ( dirname(__FILE__) . '/../Assistants/vendor/Markdown/Michelf/MarkdownInterface.php' );
include_once ( dirname(__FILE__) . '/../Assistants/vendor/Markdown/Michelf/Markdown.php' );
include_once ( dirname(__FILE__) . '/../Assistants/vendor/Markdown/Michelf/MarkdownExtra.php' );

$langTemplate='InfoPage_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$getValidation = Validation::open($_GET, array('preRules'=>array('sanitize')))
  ->addSet('page',
           ['satisfy_regex'=>'%^([0-9a-zA-Z_]+)$%',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','noValidPage3', $langTemplate)]]);

// TODO: ist etwas unsauber und muss daher noch korrekt validiert werden
if (isset($_POST['sortby'])){
    $tmp = explode('|',$_POST['sortby']);
    $_POST['sortby'] = $tmp[0];
    if (count($tmp)>=2) $_POST['sortId'] = $tmp[1];
}

$postValidation = Validation::open($_POST, array('preRules'=>array('sanitize')))
  ->addSet('page',
           ['satisfy_regex'=>'%^([0-9a-zA-Z_]+)$%',
            'on_error'=>['type'=>'error',
                         'text'=>Language::Get('main','noValidPage4', $langTemplate)]]);

$getResults = $getValidation->validate();
$postResults = $postValidation->validate();
$notifications = array_merge($notifications,$getValidation->getPrintableNotifications('MakeNotification'));
$notifications = array_merge($notifications,$postValidation->getPrintableNotifications('MakeNotification'));
$getValidation->resetNotifications()->resetErrors();
$postValidation->resetNotifications()->resetErrors();

if ($postValidation->isValid() && isset($postResults['page'])) {
    $getResults['page'] = $postResults['page'];
} else {
    if (!$postValidation->isValid()){
        $notifications[] = MakeNotification('error', Language::Get('main','noValidPage', $langTemplate));
    }
}

if ($getValidation->isValid() && isset($getResults['page'])) {
    // alles ok    
} else {
    if (!$postValidation->isValid()){
        $notifications[] = MakeNotification('error', Language::Get('main','noValidPage2', $langTemplate));
    }
}

// load user data from the database
$databaseURI = $getSiteURI . "/accountsettings/user/{$uid}";
$accountSettings_data = http_get($databaseURI, true);
$accountSettings_data = json_decode($accountSettings_data, true);

$pageTitle = "";
$pageContent = null;
if (isset($getResults['page'])){
    $pageFile = dirname(__FILE__)."/pages/".$getResults['page'].".md";
    if (file_exists($pageFile)){    
        global $externalURI; // kommt aus der Config.php
        $pageContent = file_get_contents($pageFile);
        $pageTitle = $getResults['page'];
        $parser = new \Michelf\MarkdownExtra;
        $my_html = $parser->transform($pageContent);
        $pageContent = '<span class="markdown-body">'.$my_html.'</span>';
    } else {
        $notifications[] = MakeNotification('error', Language::Get('main','noValidContent', $langTemplate));
    }
}

// construct a new header
$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind($accountSettings_data);
$h->bind(array('name' => $pageTitle,
               'backTitle' => Language::Get('main','course', $langTemplate),
               'backURL' => 'index.php',
               'notificationElements' => $notifications));


// construct a content element for account information
$infoPage = Template::WithTemplateFile('include/InfoPage/InfoPage.template.html');
$infoPage->bind($accountSettings_data);
$infoPage->bind(array("pageContent"=>$pageContent));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $infoPage);
$w->defineForm(basename(__FILE__), false, $infoPage);
$w->set_config_file('include/configs/config_infoPage.json');
if (isset($maintenanceMode) && $maintenanceMode === '1'){
    $w->add_config_file('include/configs/config_maintenanceMode.json');
}

$w->show();

ob_end_flush();