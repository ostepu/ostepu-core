<?php
/**
 * @file Error.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2014
 */

ob_start();

include_once 'include/Boilerplate.php';
include_once dirname(__FILE__) . '/../Assistants/Language.php';
include_once dirname(__FILE__) . '/../Assistants/vendor/Validation/Validation.php';

$langTemplate='Error_Controller';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/');

$getValidation = Validation::open($_GET, array('preRules'=>array('sanitize')))
  ->addSet('msg',
           array('set_default'=>null,
                 'on_error'=>array('type'=>'error',
                                   'text'=>Language::Get('main','invalidMessageType', $langTemplate))));
$postResults = $getValidation->validate();
$notifications = array_merge($notifications, $getValidation->getPrintableNotifications('MakeNotification'));
$getValidation->resetNotifications()->resetErrors();

if ($getValidation->isValid() && isset($postResults['msg'])) {
  $msg = $postResults['msg'];
}

if (isset($msg) && $msg == '403') {
    header('HTTP/1.0 403 Access Forbidden');
    $notifications[] = MakeNotification('error', '403: Access Forbidden');
}elseif (isset($msg) && $msg == '404') {
    header('HTTP/1.0 404 Not Found');
    $notifications[] = MakeNotification('error', '404: Not Found!');
}elseif (isset($msg) && $msg == '409') {
    header('HTTP/1.0 404 Not Found');
    $notifications[] = MakeNotification('error', '409: Conflict!');
}else{
    header('HTTP/1.0 403 Not Found');
    if (isset($msg)){
        $notifications[] = MakeNotification('error', '403: '.$msg);
    } else {
        $notifications[] = MakeNotification('error', '403: Access Forbidden');
    }
}

$h = Template::WithTemplateFile('include/Header/Header.template.html');
$h->bind(array('name' => 'Übungsplattform',
               'backTitle' => 'Startseite',
               'backURL' => 'index.php',
               'hideLogoutLink' => 'true',
               'notificationElements' => $notifications));

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h);
$w->set_config_file('include/configs/config_default.json');
if (isset($maintenanceMode) && $maintenanceMode === '1'){
    $w->add_config_file('include/configs/config_maintenanceMode.json');
}

$w->show();

ob_end_flush();