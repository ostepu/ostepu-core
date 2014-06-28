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
            $data['PL']['language'] = 'DE';
        
        if ($simple)
            $this->app->response->headers->set('Content-Type', 'application/json');
        
        // check if apache modules are existing
        $modules = Installer::checkModules();
        
        // check if php extensions are existing
        $extensions = Installer::checkExtensions();
        
        $fail = false;
        $errno = null;
        $error = null;
        if (isset($data['PL']['url'])) $data['PL']['url'] = rtrim($data['PL']['url'], '/');
        if (isset($data['PL']['init'])) $data['PL']['init'] = rtrim($data['PL']['init'], '/');
                        
        // install database file
        $installDatabaseFile = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabase'])) && !$installFail){
            $installDatabaseFile = true;
            Installation::installiereDatenbankdatei($data, $fail, $errno, $error);
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
        
        // install DB conf files
        $installDBFiles = array(false,false,false);
        for($confCount=0;$confCount<=2;$confCount++){
            if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabaseConf'.$confCount])) && !$installFail && isset($data['DB']['config'][$confCount]) && $data['DB']['config'][$confCount]!==''){
                $installDBFiles[$confCount] = true;
                Installation::installiereDBKonfigurationsdatei($data, $confCount, $fail, $errno, $error);
            }
        }
        
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
            $data['PL']['language'] = 'DE';
        }
        
        // select language - english
        if (isset($_POST['actionSelectEnglish']) || isset($_POST['actionSelectEnglish_x'])){
            $data['PL']['language'] = 'EN';
        }
        
        if (!$simple){
            echo "
                <html><head><style type='text/css'>
                body {background-color: #ffffff; color: #000000;}
                body, td, th, h1, h2 {font-family: sans-serif;}
                pre {margin: 0px; font-family: monospace;}
                a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
                a:hover {text-decoration: underline;}
                table {border-collapse: collapse;}
                .center {text-align: center;}
                .center table { margin-left: auto; margin-right: auto; text-align: left;}
                .center th { text-align: center !important; }
                td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
                h1 {font-size: 150%;}
                h2 {font-size: 125%;}
                .p {text-align: left;}
                .e {background-color: #ccccff; font-weight: bold; color: #000000;}
                .h {background-color: #9999cc; font-weight: bold; color: #000000;text-align: right;}
                .v {background-color: #cccccc; color: #000000;}
                .vr {background-color: #cccccc; text-align: right; color: #000000;}
                img {float: right; border: 0px;}
                hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
                </style></head><body>
                <div class='center'>
                <h1>Installation</h1></br><hr />
            ";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='data[PL][language]' value='{$data['PL']['language']}'>";
        }
        
        #region Sprachwahl
        if (!$simple){
            echo "<div align='center'>".Design::erstelleSubmitButtonGrafisch('actionSelectGerman', './images/DE.gif', 32 , 22).Design::erstelleSubmitButtonGrafisch('actionSelectEnglish', './images/EN.gif', 32 , 22)."</div>";
        }
        #endregion Sprachwahl

        #region Modulprüfung_ausgeben
        $text = '';
        foreach ($modules as $moduleName => $status){
            $text .= Design::erstelleZeile($simple, $moduleName, 'e', ($status ? "OK" : "<font color='red'>Fehler</font>"), 'v');
        }
        
        if (!$simple)
            echo Design::erstelleBlock($simple, 'Apache Module', $text);
        #endregion Modulprüfung_ausgeben
        
        #region Prüfung_der_Erweiterungen_ausgeben
        $text = '';
        foreach ($extensions as $extensionName => $status){
            $text .= Design::erstelleZeile($simple, $extensionName, 'e', ($status ? "OK" : "<font color='red'>Fehler</font>"), 'v');
        }
        
        if (!$simple)
            echo Design::erstelleBlock($simple, 'PHP Erweiterungen', $text);
        #endregion Prüfung_der_Erweiterungen_ausgeben
        
        #region Grundeinstellungen_ausgeben
        $text=Design::erstelleZeile($simple, 'URL', 'e', Design::erstelleEingabezeile($simple, (isset($data['PL']['url']) ? $data['PL']['url'] : null), 'data[PL][url]', 'http://localhost/uebungsplattform'), 'v');
        
        if (!$simple)
            echo Design::erstelleBlock($simple, 'Grundeinstellungen', $text);
        #endregion Grundeinstellungen_ausgeben
 
        #region Datenbank_einrichten
        $text='';
        $text .= Design::erstelleZeile($simple, 'Adresse', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['db_path']) ? $data['DB']['db_path'] : null), 'data[DB][db_path]', 'localhost'), 'v');
        $text .= Design::erstelleZeile($simple, 'Datenbank', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['db_name']) ? $data['DB']['db_name'] : null), 'data[DB][db_name]', 'uebungsplattform'), 'v');
        $text .= Design::erstelleZeile($simple, 'Datenbank ueberschreiben', 'e', Design::erstelleAuswahl($simple, (isset($data['DB']['db_override']) ? $data['DB']['db_override'] : null), 'data[DB][db_override]', 'override', null), 'v');
        $text .= Design::erstelleZeile($simple, 'Benutzername', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['db_user']) ? $data['DB']['db_user'] : null), 'data[DB][db_user]', 'root'), 'v');
        $text .= Design::erstelleZeile($simple, 'Passwort', 'e', Design::erstellePasswortzeile($simple, (isset($data['DB']['db_passwd']) ? $data['DB']['db_passwd'] : null), 'data[DB][db_passwd]', ''), 'v');
        
        $text .= Design::erstelleZeile($simple, 'Datenbankdatei', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['databaseSql']) ? $data['DB']['databaseSql'] : null), 'data[DB][databaseSql]', '../DB/Database2.sql'), 'v', Design::erstelleSubmitButton('actionInstallDatabase'), 'h');
        if ($installDatabaseFile)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
        
        $text .= Design::erstelleZeile($simple, 'Komponentendefinition', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['componentsSql']) ? $data['DB']['componentsSql'] : null), 'data[DB][componentsSql]', '../DB/Components2.sql'), 'v', Design::erstelleSubmitButton('actionInstallComponents'), 'h');
        if ($installComponentFile)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        if (!$simple)
            echo Design::erstelleBlock($simple, 'Datenbank einrichten', $text);
        #endregion Datenbank_einrichten
        
        #region Benutzerschnittstelle_einrichten
        $text='';
        $text .= Design::erstelleZeile($simple, 'Konfigurationsdatei (mit Schreibrechten)', 'e', Design::erstelleEingabezeile($simple, (isset($data['UI']['conf']) ? $data['UI']['conf'] : null), 'data[UI][conf]', '../UI/include/Config.php'), 'v', Design::erstelleSubmitButton('actionInstallUIConf'), 'h');

        if ($installUiFile) 
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        
        if (!$simple)
            echo Design::erstelleBlock($simple, 'Benutzerschnittstelle einrichten', $text);
        #endregion Benutzerschnittstelle_einrichten

        #region Datenbankschnittstelle_einrichten
        $text='';
        $text .= Design::erstelleZeile($simple, 'Benutzername', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['db_user_operator']) ? $data['DB']['db_user_operator'] : null), 'data[DB][db_user_operator]', 'DBOperator'), 'v');
        $text .= Design::erstelleZeile($simple, 'Benutzer ueberschreiben', 'e', Design::erstelleAuswahl($simple, (isset($data['DB']['db_user_override_operator']) ? $data['DB']['db_user_override_operator'] : null), 'data[DB][db_user_override_operator]', 'override', null), 'v');
        $text .= Design::erstelleZeile($simple, 'Passwort', 'e', Design::erstellePasswortzeile($simple, (isset($data['DB']['db_passwd_operator']) ? $data['DB']['db_passwd_operator'] : null), 'data[DB][db_passwd_operator]', ''), 'v', Design::erstelleSubmitButton("actionInstallDBOperator", 'Erstellen'), 'h');
        if ($installDBOperator)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        
        $defaultFiles = array('../DB/CControl/config.ini','../DB/DBQuery/config.ini','../DB/DBQuery2/config.ini');
        if (!isset($data['DB']['config'])) $data['DB']['config']=array(null,null,null);
        for ($confCount = 0; $confCount <= 2 ; $confCount++){
            $text .= Design::erstelleZeile($simple, 'Konfigurationsdatei (mit Schreibrechten)', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['config'][$confCount]) ? $data['DB']['config'][$confCount] : null), 'data[DB][config][]', $defaultFiles[$confCount]), 'v', Design::erstelleSubmitButton("actionInstallDatabaseConf{$confCount}"), 'h');

            if ($installDBFiles[$confCount])
               $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        }

        if (!$simple)
            echo Design::erstelleBlock($simple, 'Datenbankschnittstelle einrichten', $text);
        #endregion Datenbankschnittstelle_einrichten
        
        #region Benutzer_erstellen
        $text='';
        $text .= Design::erstelleZeile($simple, 'Benutzername', 'e', Design::erstelleEingabezeile($simple, (isset($data['DB']['db_user_insert']) ? $data['DB']['db_user_insert'] : null), 'data[DB][db_user_insert]', 'root'), 'v');
        $text .= Design::erstelleZeile($simple, 'Passwort', 'e', Design::erstellePasswortzeile($simple, (isset($data['DB']['db_passwd_insert']) ? $data['DB']['db_passwd_insert'] : null), 'data[DB][db_passwd_insert]', ''), 'v');
        $text .= Design::erstelleZeile($simple, 'Vorname (optional)', 'e', Design::erstellePasswortzeile($simple, (isset($data['DB']['db_first_name_insert']) ? $data['DB']['db_first_name_insert'] : null), 'data[DB][db_first_name_insert]', ''), 'v');
        $text .= Design::erstelleZeile($simple, 'Nachname (optional)', 'e', Design::erstellePasswortzeile($simple, (isset($data['DB']['db_last_name_insert']) ? $data['DB']['db_last_name_insert'] : null), 'data[DB][db_last_name_insert]', ''), 'v');
        $text .= Design::erstelleZeile($simple, 'E-Mail (optional)', 'e', Design::erstellePasswortzeile($simple, (isset($data['DB']['db_email_insert']) ? $data['DB']['db_email_insert'] : null), 'data[DB][db_email_insert]', ''), 'v', Design::erstelleSubmitButton("actionInstallSuperAdmin", 'Erstellen'), 'h');

        if ($installSuperAdmin)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        
        if (!$simple)
            echo Design::erstelleBlock($simple, 'Plattform - Systemadministrator anlegen', $text);
        #endregion Benutzer_erstellen
        
        #region Komponenten
        $text='';
        $text .= "<tr><td colspan='2'>Zur Initialisierung der Komponenten werden in deren Ordnern Schreibrechte benoetigt. (zum Schreiben der CConfig.json Dateien)</td></tr>";
        $text .= Design::erstelleZeile($simple, 'Initialisierung (Komponenten)', 'e', Design::erstelleEingabezeile($simple, (isset($data['PL']['init']) ? $data['PL']['init'] : null), 'data[PL][init]', 'DB/CControl'), 'v', Design::erstelleSubmitButton("actionInitComponents"), 'h');
        $text .= Design::erstelleZeile($simple, 'Details anzeigen', 'e', Design::erstelleAuswahl($simple, (isset($data['CO']['co_details']) ? $data['CO']['co_details'] : null), 'data[CO][co_details]', 'details', null), 'v');
        
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
                    $text .= "<tr><td class='e' rowspan='{$countLinks}'>{$componentName}</td><td class='v'>{$component['init']->getAddress()}</td><td class='e'><div align ='center'>".($component['init']->getStatus() === 201 ? "OK" : "<font color='red'>Fehler ({$component['init']->getStatus()})</font>")."</align></td></tr>";
                
                if (isset($component['init']) && $component['init']->getStatus() === 201){
                    $installedComponents++;
                    $installedLinks+=count(isset($component['links']) ? $component['links'] : array());
                    $installedCommands+=$countCommands;
                    
                    if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                        $text .= "<tr><td class='v' colspan='2'>installierte Befehle: {$countCommands}</td></tr>";
                
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
                                    $text .= "<tr><td class='v'>{$link->getName()}</td><td class='e'><div align ='center'>".(!$notRoutable ? 'OK' : '<font color="red">Fehler</font>')."</align></td></tr>";
                            }
                        }
                        
                        if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                            $text .= "<tr><td class='v'>{$link->getName()}".(!$linkFound ? " (<font color='red'>unbekannt</font>)" : '')."</td><td class='v'>{$link->getTargetName()}</td></tr>"; 
                    
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
                                    $text .= "<tr><td class='v'>{$callList['name']}</td><td class='e'><font color='red'>nicht belegt</font></td></tr>";
                            }
                        }
                    }
                }
            }
            
            if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                $text .= Design::erstelleZeile($simple, '', '', '', '', '' , '');
            
            $text .= Design::erstelleZeile($simple, 'installierte Komponenten', 'e', '', 'v', "<div align ='center'>".$installedComponents."</align", 'v');
            $text .= Design::erstelleZeile($simple, 'installierte Verbindungen', 'e', '', 'v', "<div align ='center'>".$installedLinks."</align", 'v');
            $text .= Design::erstelleZeile($simple, 'installierte Befehle', 'e', '', 'v', "<div align ='center'>".$installedCommands."</align", 'v');

            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        }
        
        if (!$simple)
            echo Design::erstelleBlock($simple, 'Komponenten', $text);
        #endregion Komponenten
        
        if (!$simple){
            echo "<table border='0' cellpadding='3' width='600'>";
            echo "<tr><td class='h'><div align='center'><input type='submit' name='actionInstall' value=' Alles Installieren '></div></td></tr>";
            echo "</table><br />";
            echo "</form>";

            echo "
                </div></body></html>
            ";
        }
        
    }
    
    public function CallSimpleInstall(){
          $this->CallInstall(true);
    }
}

// create a new instance of Installer class 
new Installer();
?>