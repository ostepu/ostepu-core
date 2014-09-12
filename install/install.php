<?php

/**
 * @file install.php contains the Installer class
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once dirname(__FILE__) . '/../Assistants/Slim/Slim.php';
require_once dirname(__FILE__) . '/../Assistants/Request.php';
require_once dirname(__FILE__) . '/../Assistants/DBRequest.php';
require_once dirname(__FILE__) . '/../Assistants/DBJson.php';
require_once dirname(__FILE__) . '/../Assistants/Structures.php';
require_once dirname(__FILE__) . '/include/Design.php';
require_once dirname(__FILE__) . '/include/Installation.php';
require_once dirname(__FILE__) . '/include/Sprachen.php';
require_once dirname(__FILE__) . '/include/Einstellungen.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the Installer-Component
 */
class Installer
{
    /**
     * @var Slim $_app the slim object
     */
    private $app = null;
    
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct()
    {
        // initialize slim    
        $this->app = new \Slim\Slim(array( 'debug' => true ));

        // POST,GET showInstall
        $this->app->map('(/)',
                        array($this, 'CallInstall'))->via('POST', 'GET');
                        
        // POST,GET check
        $this->app->map('/check(/)',
                        array($this, 'CallCheckRequirements'))->via('POST', 'GET');
                        
        // POST,GET SimpleInstall
        $this->app->map('/simple(/)',
                        array($this, 'CallSimpleInstall'))->via('POST', 'GET');

        // run Slim
        $this->app->run();
    }
    
    public static function apache_module_exists($module)
    {
        return in_array($module, apache_get_modules());
    }

    public static function apache_extension_exists($extension)
    {
        return extension_loaded($extension);
    }
    
    public static function checkModules()
    {
        $result = array();
        
        // check if apache modules are existing
        $result['mod_php5'] = Installer::apache_module_exists('mod_php5');
        $result['mod_rewrite'] = Installer::apache_module_exists('mod_rewrite');
        $result['mod_deflate'] = Installer::apache_module_exists('mod_deflate');  
        
        return $result;
    }
    
    public static function checkExtensions()
    {
        $result = array();
        // check if php extensions are existing
        $result['curl'] = Installer::apache_extension_exists('curl');
        $result['mysql'] = Installer::apache_extension_exists('mysql');
        $result['mysqli'] = Installer::apache_extension_exists('mysqli');
        $result['json'] = Installer::apache_extension_exists('json');
        $result['mbstring'] = Installer::apache_extension_exists('mbstring');
        $result['openssl'] = Installer::apache_extension_exists('openssl');
        $result['fileinfo'] = Installer::apache_extension_exists('fileinfo');
        $result['sockets'] = Installer::apache_extension_exists('sockets');
        return $result;
    }
    
    public static function checkRequirements()
    {
        $result = array();

        $result = array_merge($result,Installer::checkModules());
        $result = array_merge($result,Installer::checkExtensions());
        
        return $result;
    }
        
    public function CallCheckRequirements()
    {
        $requirements = Installer::checkRequirements();
        $returnStatus = 200;
        foreach($requirements as $requirement => $status){
            if (!$status){
                $returnStatus = 409;
                break;
            }
        }
        
        $this->app->response->setBody( json_encode($requirements) );
        $this->app->response->setStatus( $returnStatus );
    }
    
    public function CallInstall($simple = false)
    {
        $installFail = false;
        
        if (isset($_POST['data']))
            $data = $_POST['data'];
            
        if (isset($_POST['actionInstall'])) $_POST['action'] = 'install';
        if (!isset($data['PL']['language']))
            $data['PL']['language'] = 'de';
            
        if (!isset($data['PL']['init']))
            $data['PL']['init'] = 'DB/CControl';
        
        if ($simple)
            $this->app->response->headers->set('Content-Type', 'application/json');

        // check which menu is selected
        $menuItems = array('menu_information','menu_settings','menu_database','menu_extensions','menu_platform');
        $selected_menu = isset($_POST['selected_menu']) ? intval($_POST['selected_menu']) : 0;
        if (isset($_POST['menu_information']) || isset($_POST['menu_information_x']))
            $selected_menu = 0;
        if (isset($_POST['menu_settings']) || isset($_POST['menu_settings_x']))
            $selected_menu = 1;
        if (isset($_POST['menu_database']) || isset($_POST['menu_database_x']))
            $selected_menu = 2;
        if (isset($_POST['menu_extensions']) || isset($_POST['menu_extensions_x']))
            $selected_menu = 3;
        if (isset($_POST['menu_platform']) || isset($_POST['menu_platform_x']))
            $selected_menu = 4;
        
        // check if apache modules are existing
        $modules = Installer::checkModules();
        
        // check if php extensions are existing
        $extensions = Installer::checkExtensions();
        
        $fail = false;
        $errno = null;
        $error = null;
        if (isset($data['PL']['url'])) $data['PL']['url'] = rtrim($data['PL']['url'], '/');
        if (isset($data['PL']['temp'])) $data['PL']['temp'] = rtrim($data['PL']['temp'], '/');
        if (isset($data['PL']['files'])) $data['PL']['files'] = rtrim($data['PL']['files'], '/');
        if (isset($data['PL']['init'])) $data['PL']['init'] = rtrim($data['PL']['init'], '/');
                        
       /* // install database file
        $installDatabaseFile = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabase'])) && !$installFail){
            $installDatabaseFile = true;
            Installation::installiereDatenbankdatei($data, $fail, $errno, $error);
        }*/
        
        // install init
        $installInit = false;
        $installInitResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallInit'])) && !$installFail){
            $installInit = true;
            $installInitResult = Installation::installiereInit($data, $fail, $errno, $error);
        }
        
        // install platform
        $installPlatform = false;
        $installPlatformResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallPlatform'])) && !$installFail){
            $installPlatform = true;
            $installPlatformResult = Installation::installierePlattform($data, $fail, $errno, $error);
        }
        
        // install components file
        $installComponentFile = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallComponents'])) && !$installFail){
            $installComponentFile = true;
            Installation::installiereKomponentendatei($data, $fail, $errno, $error);
        }
        
        // install UI conf file
        $installUiFile = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallUIConf'])) && !$installFail && isset($data['UI']['conf']) && $data['UI']['conf']!==''){
            $installUiFile = true;
            Installation::installiereUIKonfigurationsdatei($data, $fail, $errno, $error);
        }
        
        // install DB operator
        $installDBOperator = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDBOperator'])) && !$installFail){
            $installDBOperator = true;
            Installation::installiereDBOperator($data, $fail, $errno, $error);
        }
        
       /* // install DB conf files
        $installDBFiles = array(false,false,false);
        for($confCount=0;$confCount<=2;$confCount++){
            if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabaseConf'.$confCount])) && !$installFail && isset($data['DB']['config'][$confCount]) && $data['DB']['config'][$confCount]!==''){
                $installDBFiles[$confCount] = true;
                Installation::installiereDBKonfigurationsdatei($data, $confCount, $fail, $errno, $error);
            }
        }*/
        
        // init components
        $initComponents = false;
        $components = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInitComponents'])) && !$installFail && isset($data['PL']['init']) && $data['PL']['init']!==''){
            $initComponents = true;
            $components = Installation::initialisiereKomponenten($data, $fail, $errno, $error);
        }
        
        // install super admin
        $installSuperAdmin = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallSuperAdmin'])) && !$installFail){
            $installSuperAdmin = true;
            Installation::installiereSuperAdmin($data, $fail, $errno, $error);
        }
        
        // select language - german
        if (isset($_POST['actionSelectGerman']) || isset($_POST['actionSelectGerman_x'])){
            $data['PL']['language'] = 'de';
        }
        
        // select language - english
        if (isset($_POST['actionSelectEnglish']) || isset($_POST['actionSelectEnglish_x'])){
            $data['PL']['language'] = 'en';
        }
        
        // load language
        Sprachen::ladeSprache($data['PL']['language']);
        
        if (!$simple){
            echo "<html><head><style type='text/css'>
body {background-color: #ffffff; color: #000000;}
body, td, th, h1, h2 {font-family: sans-serif;}
pre {margin: 0px; font-family: monospace;}
a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse;}
.center {text-align: center;}
.center table { margin-left: auto; margin-right: auto; text-align: left;}
.center th { text-align: center !important; }
td { border: 1px solid #000000; font-size: 75%;}
th { font-size: 150%;}
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccccff; font-weight: bold; color: #000000;}
.h {background-color: #9999cc; font-weight: bold; color: #000000;text-align: right;}
.v {background-color: #cccccc; color: #000000;}
.vr {background-color: #cccccc; text-align: right; color: #000000;}
hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
</style></head><body>
<div class='center'>
<h1>".Sprachen::Get('main','title'.$selected_menu)."</h1></br>";

echo "<form action='' method='post' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false'>";
echo "<table border='0'><tr><th width='0'valign='top'>";
            if ($selected_menu==0){
                if (!isset($_POST['actionShowPhpInfo'])){
                    ///echo Design::erstelleSubmitButton("actionShowPhpInfo", 'PHPInfo');
                }
            }
echo "</th>"; 

echo "<th width='600'><hr />";        
            $text='';
            $text .= "<table border='0' cellpadding='4' width='600'>";
            $text .= "<tr><td class='h'><div align='center'>".Design::erstelleSubmitButtonGrafisch('menu_information',($selected_menu == 0 ? './images/1.png' : './images/1_2.png'),32,32)."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonGrafisch('menu_settings',($selected_menu == 1 ? './images/2.png' : './images/2_2.png'),32,32)."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonGrafisch('menu_database',($selected_menu == 2 ? './images/3.png' : './images/3_2.png'),32,32)."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonGrafisch('menu_extensions',($selected_menu == 3 ? './images/4.png' : './images/4_2.png'),32,32)."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonGrafisch('menu_platform',($selected_menu == 4 ? './images/5.png' : './images/5_2.png'),32,32)."</div></td></tr>";
            $text .="</table>";
            echo $text;
            echo "<hr />";
            echo "<input type='hidden' name='selected_menu' value='{$selected_menu}'>";
            echo "<input type='hidden' name='data[PL][language]' value='{$data['PL']['language']}'>";
        }
        
        #region Sprachwahl
        if (!$simple){
            echo "<div align='center'>".Design::erstelleSubmitButtonGrafisch('actionSelectGerman', './images/de.gif', 32 , 22).Design::erstelleSubmitButtonGrafisch('actionSelectEnglish', './images/en.gif', 32 , 22)."</div>";
        }
        #endregion Sprachwahl

        #region Modulprüfung_ausgeben
        if ($selected_menu === 0){
            $text = '';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('modules','description')."</td></tr>";
                        
            foreach ($modules as $moduleName => $status){
                $text .= Design::erstelleZeile($simple, $moduleName, 'e', ($status ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')."</font>"), 'v');
            }
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('modules','title'), $text);
        }
        #endregion Modulprüfung_ausgeben
        
        #region Prüfung_der_Erweiterungen_ausgeben
        if ($selected_menu === 0){
            $text = '';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('extensions','description')."</td></tr>";
                        
            foreach ($extensions as $extensionName => $status){
                $text .= Design::erstelleZeile($simple, $extensionName, 'e', ($status ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')."</font>"), 'v');
            }
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('extensions','title'), $text);
        }
        #endregion Prüfung_der_Erweiterungen_ausgeben

        #region Plattform_Datenbanknutzer
        $data['DB']['db_user_override_operator'] = isset($data['DB']['db_user_override_operator']) ? $data['DB']['db_user_override_operator'] : null;
        if ($selected_menu === 2){
            $text='';        
            $text .= "<tr><td colspan='2'>".Sprachen::Get('createDatabasePlatformUser','description')."</td></tr>";            

            $empty = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $empty, 'data[DB][db_user_override_operator]', null, true);
            $text .= Design::erstelleZeile($simple, Sprachen::Get('createDatabasePlatformUser','db_user_override_operator'), 'e', Design::erstelleAuswahl($simple, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', 'override', null, true), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('createDatabasePlatformUser','createUser'), 'e', '', 'v', Design::erstelleSubmitButton("actionInstallDBOperator", Sprachen::Get('main','create')), 'h');
            
            if ($installDBOperator)
                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
                            
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('createDatabasePlatformUser','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', null, true);
            echo $text;
        }
        #endregion
        
        #region Grundinformationen
        $data['PL']['url'] = isset($data['PL']['url']) ? $data['PL']['url'] : null;
        $data['PL']['temp'] = isset($data['PL']['temp']) ? $data['PL']['temp'] : null;
        $data['PL']['files'] = isset($data['PL']['files']) ? $data['PL']['files'] : null;
        if ($selected_menu === 1){
            $text = '';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('general_informations','description')."</td></tr>";
                        
            $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','url'), 'e', Design::erstelleEingabezeile($simple, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','temp'), 'e', Design::erstelleEingabezeile($simple, $data['PL']['temp'], 'data[PL][temp]', '/temp', true), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','files'), 'e', Design::erstelleEingabezeile($simple, $data['PL']['files'], 'data[PL][files]', '/var/www/uebungsplattform/files', true), 'v');
            
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('general_informations','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['temp'], 'data[PL][temp]', '/temp', true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['files'], 'data[PL][files]', '/var/www/uebungsplattform/files', true);
            echo $text;
        }
        #endregion Grundinformationen
        
        #region Grundeinstellungen_ausgeben
        $data['DB']['db_ignore'] = isset($data['DB']['db_ignore']) ? $data['DB']['db_ignore'] : null;
        $data['DB']['db_override'] = isset($data['DB']['db_override']) ? $data['DB']['db_override'] : null;
        $data['PL']['pl_main_details'] = isset($data['PL']['pl_main_details']) ? $data['PL']['pl_main_details'] : null;
        if ($selected_menu === 2){
            $text = '';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('general_settings','description')."</td></tr>";
                        
            $empty = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $empty, 'data[DB][db_ignore]', null, true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $empty, 'data[DB][db_override]', null, true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $empty, 'data[PL][pl_main_details]', null, true);
            $text .= Design::erstelleZeile($simple, Sprachen::Get('general_settings','init'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallInit'), 'h');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('database','db_override'), 'e', Design::erstelleAuswahl($simple, $data['DB']['db_override'], 'data[DB][db_override]', 'override', null, true), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('database','db_ignore'), 'e', Design::erstelleAuswahl($simple, $data['DB']['db_ignore'], 'data[DB][db_ignore]', 'ignore', null, true), 'v');

            $text .= Design::erstelleZeile($simple, Sprachen::Get('general_settings','details'), 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', 'details', null, true), 'v');
            
            if ($installInit){
                foreach ($installInitResult as $component => $dat){
                    $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
                
                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
            }
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('general_settings','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_ignore'], 'data[DB][db_ignore]', null, true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_override'], 'data[DB][db_override]', null, true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', null, true);
            echo $text;
        }
        #endregion Grundeinstellungen_ausgeben
        
        #region Datenbank_informationen
        $data['DB']['db_path'] = isset($data['DB']['db_path']) ? $data['DB']['db_path'] : null;
        $data['DB']['db_name'] = isset($data['DB']['db_name']) ? $data['DB']['db_name'] : null;
        $data['DB']['db_user'] = isset($data['DB']['db_user']) ? $data['DB']['db_user'] : null;
        $data['DB']['db_passwd'] = isset($data['DB']['db_passwd']) ? $data['DB']['db_passwd'] : null;
        $data['DB']['db_user_operator'] = isset($data['DB']['db_user_operator']) ? $data['DB']['db_user_operator'] : null;
        $data['DB']['db_passwd_operator'] = isset($data['DB']['db_passwd_operator']) ? $data['DB']['db_passwd_operator'] : null;
        if ($selected_menu === 1){
            $text = '';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('database_informations','description')."</td></tr>";
            
            $empty = null;
            $text .= Design::erstelleZeile($simple, Sprachen::Get('database_informations','db_path'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_path'], 'data[DB][db_path]', 'localhost', true), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('database_informations','db_name'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_name'], 'data[DB][db_name]', 'uebungsplattform', true), 'v');
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('database_informations','title'), $text);
                
            $text = '';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('databaseAdmin','description')."</td></tr>";
            
            $text .= Design::erstelleZeile($simple, Sprachen::Get('databaseAdmin','db_user'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_user'], 'data[DB][db_user]', 'root', true), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('databaseAdmin','db_passwd'), 'e', Design::erstellePasswortzeile($simple, $data['DB']['db_passwd'], 'data[DB][db_passwd]', ''), 'v');
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('databaseAdmin','title'), $text);
                
            $text = '';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('databasePlatformUser','description')."</td></tr>";
            
            $text .= Design::erstelleZeile($simple, Sprachen::Get('databasePlatformUser','db_user_operator'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', 'DBOperator',true), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('databasePlatformUser','db_passwd_operator'), 'e', Design::erstellePasswortzeile($simple, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', ''), 'v');
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('databasePlatformUser','title'), $text);
        } else {
            $text = '';
            $empty = null;
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_passwd'], 'data[DB][db_passwd]', null);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', null);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', 'DBOperator',true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user'], 'data[DB][db_user]', 'root' ,true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_name'], 'data[DB][db_name]', 'uebungsplattform',true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_path'], 'data[DB][db_path]', 'localhost',true);
            echo $text;
        }
        #endregion Datenbank_informationen
 
        #region Datenbank_einrichten
        $data['DB']['componentsSql'] = isset($data['DB']['componentsSql']) ? $data['DB']['componentsSql'] : null;
        if ($selected_menu === 3){
            $text='';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('componentDefinitions','description')."</td></tr>";
            
            $text .= Design::erstelleZeile($simple, Sprachen::Get('componentDefinitions','componentsSql'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['componentsSql'], 'data[DB][componentsSql]', '../DB/Components2.sql', true), 'v', Design::erstelleSubmitButton('actionInstallComponents'), 'h');
            if ($installComponentFile)
                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
                              
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('componentDefinitions','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['componentsSql'], 'data[DB][componentsSql]', '../DB/Components2.sql', true);
            echo $text;
        }
        #endregion Datenbank_einrichten
        
        #region Benutzerschnittstelle_einrichten
        $data['UI']['conf'] = isset($data['UI']['conf']) ? $data['UI']['conf'] : null;
        if ($selected_menu === 4){
            $text='';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('userInterface','description')."</td></tr>";
            
            $text .= Design::erstelleZeile($simple, Sprachen::Get('userInterface','conf'), 'e', Design::erstelleEingabezeile($simple, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true), 'v', Design::erstelleSubmitButton('actionInstallUIConf'), 'h');

            if ($installUiFile) 
                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('userInterface','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true);
            echo $text;
        }
        #endregion Benutzerschnittstelle_einrichten
        
        #region PlugInsInstallieren
        if ($selected_menu === 3 && false){
            $text='';
            $text .= "<tr><td colspan='2'>".""."</td></tr>";
            
            $text .= Design::erstelleZeile($simple, '<s>ausgewaehlte Installieren</s>', 'e', '', 'v', Design::erstelleSubmitButton('actionInstallPlugins',Sprachen::Get('main','install')), 'h');
            
            // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen
            $text .= Design::erstelleZeile($simple, '<s>Core v0.1</s>', 'e', Design::erstelleAuswahl($simple, (isset($data['PL']['pl_details']) ? $data['PL']['pl_details'] : null), 'data[PL][pl_details]', 'details', null), 'v');
            $text .= Design::erstelleZeile($simple, '<s>OSTEPU v0.1</s>', 'e', Design::erstelleAuswahl($simple, (isset($data['PL']['pl_details']) ? $data['PL']['pl_details'] : null), 'data[PL][pl_details]', 'details', null), 'v');
            $text .= Design::erstelleZeile($simple, '<s>Forms v1.0</s>', 'e', Design::erstelleAuswahl($simple, (isset($data['PL']['pl_details']) ? $data['PL']['pl_details'] : null), 'data[PL][pl_details]', 'details', null), 'v');
            
            if ($installPlatform){
                /*foreach ($installPlatformResult as $component => $dat){
                    $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }*/
                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
            }
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, '<s>Erweiterungen installieren</s>', $text);
        }
        #endregion PlugInsInstallieren
               
        #region Komponenten
        $data['CO']['co_details'] = isset($data['CO']['co_details']) ? $data['CO']['co_details'] : null;
        if ($selected_menu === 3){
            $text='';
            
            $empty = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $empty, 'data[CO][co_details]', null, true);
            $text .= "<tr><td colspan='2'>".Sprachen::Get('components','description')."</td></tr>";
            //Design::erstelleEingabezeile($simple, (isset($data['PL']['init']) ? $data['PL']['init'] : null), 'data[PL][init]', 'DB/CControl')
            $text .= Design::erstelleZeile($simple, Sprachen::Get('components','init'), 'e', '', 'v', Design::erstelleSubmitButton("actionInitComponents"), 'h');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('components','details'), 'e', Design::erstelleAuswahl($simple, $data['CO']['co_details'], 'data[CO][co_details]', 'details', null, true), 'v');
            
            if ($initComponents){
                // counts installed commands
                $installedCommands = 0;
                
                // counts installed components
                $installedComponents = 0;
                
                // counts installed links
                $installedLinks = 0;
                
                foreach($components as $componentName => $component)
                {
                    $linkNames = array();
                    $linkNamesUnique = array();
                    $callNames = array();
                    
                    $links = array();
                    if (isset($component['links']))
                        $links = $component['links'];
                    foreach($links as $link){
                        $linkNames[] = $link->getName();
                        $linkNamesUnique[$link->getName()] = $link->getName();
                    }
                    
                    $calls=null;
                    if (isset($component['call']))
                        $calls = $component['call'];
                    if ($calls!==null){
                        foreach($calls as $pos => $callList){
                            if (isset($callList['name']))
                                $callNames[$callList['name']] = $callList['name'];
                        }
                    }
                    
                            
                    $countLinks = 1;
                    if (isset($component['init']) && $component['init']->getStatus() === 201){
                        $countLinks+=count($linkNames) + count(array_diff($callNames,$linkNamesUnique)) + count($linkNamesUnique) - count(array_diff($linkNamesUnique,$callNames));
                        $countLinks++;
                    }
                    
                    $countCommands = count(isset($component['commands']) ? $component['commands'] : array());
                    if (isset($component['init']) && isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                        $text .= "<tr><td class='e' rowspan='{$countLinks}'>{$componentName}</td><td class='v'>{$component['init']->getAddress()}</td><td class='e'><div align ='center'>".($component['init']->getStatus() === 201 ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$component['init']->getStatus()})</font>")."</align></td></tr>";
                    
                    if (isset($component['init']) && $component['init']->getStatus() === 201){
                        $installedComponents++;
                        $installedLinks+=count(isset($component['links']) ? $component['links'] : array());
                        $installedCommands+=$countCommands;
                        
                        if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                            $text .= "<tr><td class='v' colspan='2'>".Sprachen::Get('components','installedCalls').": {$countCommands}</td></tr>";
                    
                        $links = array();
                        if (isset($component['links']))
                            $links = $component['links'];
                        $lastLink = null;
                        foreach($links as $link){
                            $calls = null;
                            if (isset($component['call']))
                                $calls = $component['call'];
                            $linkFound=false;
                            if ($calls!==null){
                                foreach($calls as $pos => $callList){
                                    if (isset($callList['name']) && $callList['name'] === $link->getName()){
                                        $linkFound=true;
                                        break;
                                    }
                                }
                            }

                            if ($lastLink!=$link->getName() && $linkFound){
                                $calls = null;
                                if (isset($component['call']))
                                    $calls = $component['call'];

                                $notRoutable = false;
                                if ($calls!==null){
                                    foreach($calls as $pos => $callList){                
                                        if ($link->getName() !== $callList['name']) continue;
                                        foreach($callList['links'] as $pos2 => $call){
                                            if (!isset($components[$link->getTargetName()]['router'])){
                                                $notRoutable=true;
                                                break;
                                            }
                                            if ($components[$link->getTargetName()]['router']==null) continue;
                                            if ($call===null) continue;
                                            if (!isset($call['method'])) continue;
                                            if (!isset($call['path'])) continue;
                                            
                                            $routes = count($components[$link->getTargetName()]['router']->getMatchedRoutes(strtoupper($call['method']), $call['path']),true);
                                            if ($routes===0){
                                                $notRoutable=true;
                                                break;
                                            }
                                        }
                                        if ($notRoutable) break;
                                    }
                                    
                                    if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                                        $text .= "<tr><td class='v'>{$link->getName()}</td><td class='e'><div align ='center'>".(!$notRoutable ? Sprachen::Get('main','ok') : '<font color="red">'.Sprachen::Get('components','notRoutable').'</font>')."</align></td></tr>";
                                }
                            }
                            
                            if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                                $text .= "<tr><td class='v'>{$link->getName()}".(!$linkFound ? " (<font color='red'>".Sprachen::Get('components','unknown')."</font>)" : '')."</td><td class='v'>{$link->getTargetName()}</td></tr>"; 
                        
                            $lastLink = $link->getName();
                        }
                        
                        // fehlende links
                        $calls = null;
                        if (isset($component['call']))
                            $calls = $component['call'];
                        if ($calls!==null){
                            foreach($calls as $pos => $callList){    
                                $found = false;
                                foreach($links as $link){                    
                                    if ($link->getName() == $callList['name']){
                                        $found=true;
                                        break;
                                    }
                                }
                                if (!$found){
                                    if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                                        $text .= "<tr><td class='v'>{$callList['name']}</td><td class='e'><font color='red'>".Sprachen::Get('components','unallocated')."</font></td></tr>";
                                }
                            }
                        }
                    }
                }
                
                if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                    $text .= Design::erstelleZeile($simple, '', '', '', '', '' , '');
                
                $text .= Design::erstelleZeile($simple, Sprachen::Get('components','installedComponents'), 'e', '', 'v', "<div align ='center'>".$installedComponents."</align", 'v');
                $text .= Design::erstelleZeile($simple, Sprachen::Get('components','installedLinks'), 'e', '', 'v', "<div align ='center'>".$installedLinks."</align", 'v');
                $text .= Design::erstelleZeile($simple, Sprachen::Get('components','installedCommands'), 'e', '', 'v', "<div align ='center'>".$installedCommands."</align", 'v');

                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
            }
            
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('components','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['CO']['co_details'], 'data[CO][co_details]', null,true);
            echo $text;
        }
        #endregion Komponenten
                
        #region PlattformEinrichten
        $data['PL']['pl_details'] = isset($data['PL']['pl_details']) ? $data['PL']['pl_details'] : null;
        if ($selected_menu === 4){
            $text='';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('platform','description')."</td></tr>";
                        
            $empty = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $empty, 'data[PL][pl_details]', null, true);
            $text .= Design::erstelleZeile($simple, Sprachen::Get('platform','createTables'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallPlatform'), 'h');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('platform','details'), 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
            
            if ($installPlatform){
                foreach ($installPlatformResult as $component => $dat){
                    $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
            }
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('platform','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', null,true);
            echo $text;
        }
        #endregion PlattformEinrichten
        
        #region Benutzer_erstellen
        $data['DB']['db_user_insert'] = isset($data['DB']['db_user_insert']) ? $data['DB']['db_user_insert'] : null;
        $data['DB']['db_first_name_insert'] = isset($data['DB']['db_first_name_insert']) ? $data['DB']['db_first_name_insert'] : null;
        $data['DB']['db_last_name_insert'] = isset($data['DB']['db_last_name_insert']) ? $data['DB']['db_last_name_insert'] : null;
        $data['DB']['db_email_insert'] = isset($data['DB']['db_email_insert']) ? $data['DB']['db_email_insert'] : null;
        $data['DB']['db_passwd_insert'] = isset($data['DB']['db_passwd_insert']) ? $data['DB']['db_passwd_insert'] : null;
        if ($selected_menu === 4){
            $text='';
            $text .= "<tr><td colspan='2'>".Sprachen::Get('createSuperAdmin','description')."</td></tr>";
            
            $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_user_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root'), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_passwd_insert'), 'e', Design::erstellePasswortzeile($simple, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', ''), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_first_name_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', ''), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_last_name_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', ''), 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_email_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', ''), 'v', Design::erstelleSubmitButton("actionInstallSuperAdmin", Sprachen::Get('main','create')), 'h');

            if ($installSuperAdmin)
                $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
                        
            if (!$simple)
                echo Design::erstelleBlock($simple, Sprachen::Get('createSuperAdmin','title'), $text);
        } else {
            $text = '';
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root',true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', '',true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', '',true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', '',true);
            $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', '');
            echo $text;
        }
        #endregion Benutzer_erstellen
               
        if (!$simple){
            if (($selected_menu === 2 || $selected_menu === 3 || $selected_menu === 4) && false){
                echo "<table border='0' cellpadding='3' width='600'>";
                echo "<tr><td class='h'><div align='center'><input type='submit' name='actionInstall' value=' ".Sprachen::Get('main','installAll')." '></div></td></tr>";
                echo "</table><br />";
            }
                        
            #region zurück_weiter_buttons
            $text = '';
            $a='';$b='';
            if ($selected_menu>0)
                $a = Design::erstelleSubmitButton($menuItems[$selected_menu-1], 'zurueck');
                
            if ($selected_menu<count($menuItems)-1)
                $b = Design::erstelleSubmitButton($menuItems[$selected_menu+1], 'weiter');
            
            echo "<table border='0' cellpadding='3' width='600'>";
            echo "<thead><tr><th align='left' width='50%'>{$a}</th><th align='right' width='50%'>{$b}</th></tr></thead>";
            echo "</table>";
            #endregion zurück_weiter_buttons
            
            echo "<div>";

            echo "</div>";
            
            echo "</th>";
            echo "<th width='2'></th>";
            echo "<th valign='top'>";
            $text='';
            
            echo "<div style='width:150px;word-break: break-all;'>";
            echo "<table border='0'>";
            echo "<tr><td class='e'>".Sprachen::Get('general_informations','url')."</td></tr>";
            echo "<tr><td>".$data['PL']['url']."</td></tr>";
            echo "<tr><th></th></tr>";
            echo "<tr><td class='e'>".Sprachen::Get('database_informations','db_name')."</td></tr>";
            echo "<tr><td>".$data['DB']['db_name']."</td></tr>";
            echo "<tr><th></th></tr>";
            echo "<tr><td class='e'>".Sprachen::Get('database_informations','db_path')."</td></tr>";
            echo "<tr><td>".$data['DB']['db_path']."</td></tr>";
            echo "<tr><th></th></tr>";
            echo "<tr><td class='e'>".Sprachen::Get('databaseAdmin','db_user')."</td></tr>";
            echo "<tr><td>".$data['DB']['db_user']."</td></tr>";
            echo "<tr><th></th></tr>";
            echo "<tr><td class='e'>".Sprachen::Get('databasePlatformUser','db_user_operator')."</td></tr>";
            echo "<tr><td>".$data['DB']['db_user_operator']."</td></tr>";
            echo "</table>";
            echo "</div";
            
            echo "</th></tr></form></table>";

            echo "
                </div></body></html>
            ";
            
            echo "<div width='600'>";
            if ($selected_menu==0){
                if (isset($_POST['actionShowPhpInfo'])){
                    ///phpinfo();
                }
            }
            echo "</div>";
        }
                
        Einstellungen::speichereEinstellungen();
        
    }
    
    public function CallSimpleInstall(){
          $this->CallInstall(true);
    }
}

// create a new instance of Installer class 
new Installer();
?>