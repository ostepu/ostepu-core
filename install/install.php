<?php

/**
 * @file install.php contains the Installer class
 *
 * @author Till Uhlig
 * @date 2014
 */

if (!isset($argv))
    require_once dirname(__FILE__) . '/../Assistants/Slim/Slim.php';

require_once dirname(__FILE__) . '/../Assistants/Request.php';
require_once dirname(__FILE__) . '/../Assistants/DBRequest.php';
require_once dirname(__FILE__) . '/../Assistants/DBJson.php';
require_once dirname(__FILE__) . '/../Assistants/Structures.php';
require_once dirname(__FILE__) . '/include/Design.php';
require_once dirname(__FILE__) . '/include/Installation.php';
require_once dirname(__FILE__) . '/include/Sprachen.php';
require_once dirname(__FILE__) . '/include/Einstellungen.php';
require_once dirname(__FILE__) . '/include/Variablen.php';
require_once dirname(__FILE__) . '/include/Zugang.php';

if (!isset($argv))
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
    private $argv = null;
    
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct($_argv)
    {
        $this->argv = $_argv;

        if ($this->argv!=null){
            array_shift($this->argv);
            foreach($this->argv as $arg)
                $_POST[$arg] = 'OK';
            
            $this->CallInstall(true);
            return;
        }

        // initialize slim    
        $this->app = new \Slim\Slim(array( 'debug' => true ));
        $this->app->contentType('text/html; charset=utf-8');

        // POST,GET showInstall
        $this->app->map('(/)',
                        array($this, 'CallInstall'))->via('POST', 'GET','INFO' );

        // run Slim
        $this->app->run();  
    }
    
    public static function apache_module_exists($module)
    {
        if (!function_exists('apache_get_modules')) return false;
        return in_array($module, apache_get_modules());
    }

    public static function apache_extension_exists($extension)
    {
       // if (!function_exists('extension_loaded')) return false;
        return extension_loaded($extension);
    }
    
    public static function checkModules($data, &$fail, &$errno, &$error)
    {
        $result = array();
        
        // check if apache modules are existing
        $result['mod_php5'] = Installer::apache_module_exists('mod_php5');
        $result['mod_rewrite'] = Installer::apache_module_exists('mod_rewrite');
        $result['mod_deflate'] = Installer::apache_module_exists('mod_deflate');  
        
        return $result;
    }
    
    public static function checkExtensions($data, &$fail, &$errno, &$error)
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
    
    public function CallInstall($simple = false)
    {
    
        $output = array();
        $installFail = false;
        if (isset($_POST['data']))
            $data = $_POST['data'];

        Variablen::Initialisieren($data);

        if (isset($_POST['actionInstall'])) $_POST['action'] = 'install';
        if (!isset($data['PL']['language']))
            $data['PL']['language'] = 'de';
            
        if (!isset($data['PL']['init']))
            $data['PL']['init'] = 'DB/CControl';
            
        if (isset($data['PL']['url'])) $data['PL']['url'] = rtrim($data['PL']['url'], '/');
        if (isset($data['PL']['urlExtern'])) $data['PL']['urlExtern'] = rtrim($data['PL']['urlExtern'], '/');
        if (isset($data['PL']['temp'])) $data['PL']['temp'] = rtrim($data['PL']['temp'], '/');
        if (isset($data['PL']['files'])) $data['PL']['files'] = rtrim($data['PL']['files'], '/');
        if (isset($data['PL']['init'])) $data['PL']['init'] = rtrim($data['PL']['init'], '/');
            
        // check which server is selected
        $server = isset($_POST['server']) ? $_POST['server'] : null;
        $selected_server = isset($_POST['selected_server']) ? $_POST['selected_server'] : null;
        if ($data['SV']['name']!==null && $selected_server!==null)
            if ($data['SV']['name']!=$selected_server)
                Einstellungen::umbenennenEinstellungen($selected_server,$data['SV']['name']);

        // check which menu is selected
        $menuItems = array(5,0,1,6,2,7,3,4);
        $menuTypes = array(0,0,0,0,0,0,1,1);
        $selected_menu = intval(isset($_POST['selected_menu']) ? $_POST['selected_menu'] : $menuItems[0]);
        
        // check server configs
        $serverFiles = Installation::GibServerDateien();
          
        // add Server
        $addServer = false;
        $addServerResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionAddServer']) || count($serverFiles)==0) && !$installFail){
            $addServer = true;
            Variablen::Zuruecksetzen($data);
            $server = Einstellungen::NeuenServerAnlegen();
            $serverFiles[] = $server;
            $server = pathinfo($server)['filename'];
            Einstellungen::ladeEinstellungen($server);
            Einstellungen::speichereEinstellungen($server);
        }
                    
        // save data on switching between server-confs
        if ($selected_server!==null && $server!=null){
            if ($server!=$selected_server){
                Einstellungen::ladeEinstellungen($selected_server);
                Variablen::Einsetzen($data);
                Einstellungen::speichereEinstellungen($selected_server);
                Variablen::Zuruecksetzen($data);
            }
        }

        // select first if no server is selected
        if ($selected_server==null && $server==null){
            $selected_server = pathinfo($serverFiles[0])['filename'];
            $server = $selected_server;
        }
        
        if ($server!=null)
            $selected_server=$server;
        
        $server=$selected_server;
        
        Einstellungen::ladeEinstellungen($server);
        Variablen::Einsetzen($data);
        
        if ($simple)
            $data['ZV']['zv_type'] = 'local';

        $fail = false;
        $errno = null;
        $error = null;
        
        $modules = array();
        if ($selected_menu === 0 || ($simple && isset($_POST['actionCheckModules']))){
            // check if apache modules are existing
            $modules = Zugang::Ermitteln('actionCheckModules','Installer::checkModules',$data, $fail, $errno, $error);
            
            if ($simple)
                $output['actionCheckModules'] = $modules;
        }
        
        $extensions = array();
        if ($selected_menu === 0 || ($simple && isset($_POST['actionCheckExtensions']))){
            // check if php extensions are existing
            $extensions = Zugang::Ermitteln('actionCheckExtensions','Installer::checkExtensions',$data, $fail, $errno, $error);
            
            if ($simple)
                $output['actionCheckExtensions'] = $extensions;
        }

        // install init
        $installInit = false;
        $installInitResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallInit'])) && !$installFail){
            $installInit = true;
            $installInitResult = Zugang::Ermitteln('actionInstallInit','Installation::installiereInit',$data, $fail, $errno, $error);
            
            if ($simple){
                $installInitResult['fail'] = $fail;
                $installInitResult['errno'] = $errno;
                $installInitResult['error'] = $error;
                $output['actionInstallInit'] = $installInitResult;
            }
        }
        
        // install platform
        $installPlatform = false;
        $installPlatformResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallPlatform'])) && !$installFail){
            $installPlatform = true;
            $installPlatformResult = Zugang::Ermitteln('actionInstallPlatform','Installation::installierePlattform',$data, $fail, $errno, $error);
            
            if ($simple){
                $installPlatformResult['fail'] = $fail;
                $installPlatformResult['errno'] = $errno;
                $installPlatformResult['error'] = $error;
                $output['actionInstallPlatform'] = $installPlatformResult;
            }
        }

        // install components file
        $installComponentFile = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallComponents'])) && !$installFail){
            $installComponentFile = true;
            Zugang::Ermitteln('actionInstallComponents','Installation::installiereKomponentendatei',$data, $fail, $errno, $error);

            if ($simple){
                $result = array();
                $result['fail'] = $fail;
                $result['errno'] = $errno;
                $result['error'] = $error;
                $output['actionInstallComponents'] = $result;
            }
        }
        
        // install component definitions
        $installComponentDefs = false;
        $installComponentDefsResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallComponentDefs'])) && !$installFail){
            $installComponentDefs = true;
            
            if ($simple){
                $installComponentDefsResult = Zugang::Ermitteln('actionInstallComponentDefs','Installation::installiereKomponentenDefinitionen',$data, $fail, $errno, $error);
                $result['fail'] = $fail;
                $result['errno'] = $errno;
                $result['error'] = $error;
                $output['actionInstallComponentDefs'] = $installComponentDefsResult;
            } else {
            
                $serverFiles = Installation::GibServerDateien();
                
                $installComponentDefsResult['components']=array();
                foreach($serverFiles as $sf){
                    $sf = pathinfo($sf)['filename'];
                    $tempData = Einstellungen::ladeEinstellungenDirekt($sf);
                    $componentList = Zugang::Ermitteln('actionInstallComponentDefs','Installation::installiereKomponentenDefinitionen',$tempData, $fail, $errno, $error); 
                   
                    if (isset($componentList['components']))
                        $installComponentDefsResult['components'] = array_merge($installComponentDefsResult['components'],$componentList['components']);
                }
                
                // Komponenten erzeugen
                $comList = array();
                $setDBNames = array();
                $ComponentList = array();
                
                // zunächst die Komponentenliste nach Namen sortieren
                $ComponentListInput = array();
                foreach ($installComponentDefsResult['components'] as $key => $input){
                    if (!isset($input['name'])) continue;
                    if (!isset($ComponentListInput[$input['name']]))$ComponentListInput[$input['name']]=array();
                    $ComponentListInput[$input['name']][$key] = $input;
                }
                
                for($zz=0;$zz<2;$zz++){
                $tempList = array();
                foreach ($ComponentListInput as $key2 => $ComNames){
                    foreach ($ComNames as $key => $input){
                        if (!isset($input['name'])) continue;
                        
                        if (!isset($input['type']) || $input['type']=='normal'){
                            // normale Komponente
                            
                            if (!isset($input['registered'])){
                                $comList[] = "('{$input['name']}', '{$input['urlExtern']}/{$input['path']}', '".(isset($input['option']) ? $input['option'] : '')."')"; 
                                // Verknüpfungen erstellen
                                $setDBNames[] = " SET @{$key}_{$input['name']} = (select CO_id from Component where CO_address='{$input['urlExtern']}/{$input['path']}' limit 1); ";
                                $input['dbName'] = $key.'_'.$input['name'];
                                $input['registered'] = '1';
                            }   
                            if (!isset($tempList[$key2])) $tempList[$key2] = array();
                                $tempList[$key2][$key] = $input;
                                    
                        } elseif (isset($input['type']) && $input['type']=='clone') {
                            // Komponente basiert auf einer bestehenden
                            if (!isset($input['base'])) continue;
                             
                            if (isset($ComponentListInput[$input['base']]))
                                foreach ($ComponentListInput[$input['base']] as $key3 => $input2){
                                    if (!isset($input2['name'])) continue;

                                    // pruefe, dass die Eintraege nicht doppelt erstellt werden
                                    $found=false;
                                    foreach ($ComponentListInput[$input['name']] as $input3){
                                        if ("{$input3['urlExtern']}/{$input3['path']}" == "{$input2['urlExtern']}/{$input2['path']}{$input['baseURI']}"){
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if ($found){ continue;}
                                    
                                    $input2['path'] = "{$input2['path']}{$input['baseURI']}";
                                    
                                    if (isset($input2['links']) && isset($input['links']))
                                        $input2['links'] = array_merge($input['links']);
                                        
                                    if (isset($input2['connector']) && isset($input['connector']))
                                        $input2['connector'] = array_merge($input['connector']);

                                    $input2['name'] = $input['name'];
                                    if (!isset($tempList[$key2])) $tempList[$key2] = array();
                                        $tempList[$key2][$key] = $input2;
                                }
                        }
                    }
                }
                    $ComponentListInput = $tempList;
                }
                
                $sql = "START TRANSACTION;SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;TRUNCATE TABLE `ComponentLinkage`;ALTER TABLE `ComponentLinkage` AUTO_INCREMENT = 1;TRUNCATE TABLE `Component`;ALTER TABLE `Component` AUTO_INCREMENT = 1;INSERT INTO `Component` (`CO_name`, `CO_address`, `CO_option`) VALUES ";
                $installComponentDefsResult['componentsCount'] = count($comList);
                $sql.=implode(',',$comList);
                unset($comList);
                $sql .= " ON DUPLICATE KEY UPDATE CO_address=VALUES(CO_address), CO_option=VALUES(CO_option);SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;COMMIT;";
                DBRequest::request2($sql, false, $data);
                
                $sql = "START TRANSACTION;SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;";
                $sql .=implode('',$setDBNames);
                unset($setDBNames);
                $sql .= " TRUNCATE TABLE `ComponentLinkage`;INSERT INTO `ComponentLinkage` (`CO_id_owner`, `CL_name`, `CL_relevanz`, `CO_id_target`) VALUES ";
                $links = array();
                
                foreach ($ComponentListInput as $key2 => $ComNames){
                    foreach ($ComNames as $key => $input){
                        if (isset($input['type']) && $input['type']!='normal') continue;
                        if (isset($input['dbName'])){
                        
                            // prüfe nun alle Verknüpfungen dieser Komponente und erstelle diese
                            if (isset($input['links']))
                                foreach ($input['links'] as $link){
                                    if (!is_array($link['target'])) $link['target'] = array($link['target']);
                                    
                                    foreach ($link['target'] as $tar){// $tar -> der Name der Zielkomponente
                                        if (!isset($ComponentListInput[$tar])) continue;
                                        foreach ($ComponentListInput[$tar] as $target){
                                            // $target -> das Objekt der Zielkomponente
                                            if (!isset($target['dbName'])) continue;
                                            if ($input['link_type']=='local'){
                                                if ($input['urlExtern'] == $target['urlExtern']){
                                                    $l = "(@{$input['dbName']}, '{$link['name']}', '".(isset($input['relevanz']) ? $input['relevanz'] : '')."', @{$target['dbName']})\n";
                                                   // echo $l.'<br>';
                                                    $links[] = $l;
                                                }
                                            } elseif ($input['link_type']=='full'){
                                                if ($input['urlExtern'] == $target['urlExtern'] || (isset($target['link_availability']) && $target['link_availability']=='full')){
                                                
                                                    $l = "(@{$input['dbName']}, '{$link['name']}', '".(isset($input['relevanz']) ? $input['relevanz'] : '')."', @{$target['dbName']})\n";
                                                   // echo $l.'<br>';
                                                    $links[] = $l;
                                                }
                                            }
                                        }
                                    }
                                }
                                
                            if (isset($input['connector']))
                                foreach ($input['connector'] as $link){
                                    if (!is_array($link['target'])) $link['target'] = array($link['target']);
                                    
                                    foreach ($link['target'] as $tar){// $tar -> der Name der Zielkomponente
                                        if (!isset($ComponentListInput[$tar])) continue;
                                        foreach ($ComponentListInput[$tar] as $target){
                                            // $target -> das Objekt der Zielkomponente
                                            if (!isset($target['dbName'])) continue;
                                            if ($input['link_type']=='local'){
                                                if ($input['urlExtern'] == $target['urlExtern']){
                                                    $l = "(@{$target['dbName']}, '{$link['name']}', '".(isset($input['relevanz']) ? $input['relevanz'] : '')."', @{$input['dbName']})\n";
                                                   // echo $l.'<br>';
                                                    $links[] = $l;
                                                }
                                            } elseif ($input['link_type']=='full'){
                                                if ($input['urlExtern'] == $target['urlExtern'] || (isset($input['link_availability']) && $input['link_availability']=='full')){
                                                
                                                    $l = "(@{$target['dbName']}, '{$link['name']}', '".(isset($input['relevanz']) ? $input['relevanz'] : '')."', @{$input['dbName']})\n";
                                                   // echo $l.'<br>';
                                                    $links[] = $l;
                                                }
                                            }
                                        }
                                    }
                                }
                        }
                    }
                }
                
                $installComponentDefsResult['linksCount'] = count($links);
                $sql.=implode(',',$links);
                unset($links);
                $sql .= "; COMMIT;";
                DBRequest::request2($sql, false, $data);
                $installComponentDefsResult['components'] = $ComponentListInput;
            }
        }
        
        // install UI conf file
        $installUiFile = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallUIConf'])) && !$installFail && isset($data['UI']['conf']) && $data['UI']['conf']!==''){
            $installUiFile = true;
            Zugang::Ermitteln('actionInstallUIConf','Installation::installiereUIKonfigurationsdatei',$data, $fail, $errno, $error);
            
            if ($simple){
                $result = array();
                $result['fail'] = $fail;
                $result['errno'] = $errno;
                $result['error'] = $error;
                $output['actionInstallUIConf'] = $result;
            }
        }
        
        // install DB operator
        $installDBOperator = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDBOperator'])) && !$installFail){
            $installDBOperator = true;
            Zugang::Ermitteln('actionInstallDBOperator','Installation::installiereDBOperator',$data, $fail, $errno, $error);
            
            if ($simple){
                $result = array();
                $result['fail'] = $fail;
                $result['errno'] = $errno;
                $result['error'] = $error;
                $output['actionInstallDBOperator'] = $result;
            }
        }
        
        // install plugins
        $installPlugins = false;
        $installPluginsResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallPlugins'])) && !$installFail){
            $installPlugins = true;
            $installPluginsResult = Installation::initialisierePlugins($data, $fail, $errno, $error);
        }
        
        // uninstall plugins
        $uninstallPlugins = false;
        $uninstallPluginsResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionUninstallPlugins'])) && !$installFail){
            $uninstallPlugins = true;
            $uninstallPluginsResult = Installation::deinitialisierePlugins($data, $fail, $errno, $error);
        }
        
        // init components
        $initComponents = false;
        $components = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInitComponents'])) && !$installFail && isset($data['PL']['init']) && $data['PL']['init']!==''){
            $initComponents = true;
            $components = Zugang::Ermitteln('actionInitComponents','Installation::initialisiereKomponenten',$data, $fail, $errno, $error);
            
            if ($simple){
                $components['fail'] = $fail;
                $components['errno'] = $errno;
                $components['error'] = $error;
                $output['actionInitComponents'] = $components;
            }
        }
        
        // install super admin
        $installSuperAdmin = false;
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallSuperAdmin'])) && !$installFail){
            $installSuperAdmin = true;
            Zugang::Ermitteln('actionInstallSuperAdmin','Installation::installiereSuperAdmin',$data, $fail, $errno, $error);
            
            if ($simple){
                $result = array();
                $result['fail'] = $fail;
                $result['errno'] = $errno;
                $result['error'] = $error;
                $output['actionInstallSuperAdmin'] = $result;
            }
        }
        
        $installedPlugins = array();
        if ($selected_menu === 6 || ($simple && isset($_POST['actionCheckPlugins']))){
            // check installed plugins
            $installedPlugins = Zugang::Ermitteln('actionCheckPlugins','Installation::checkPlugins',$data, $fail, $errno, $error);
            
            if ($simple)
                $output['actionCheckPlugins'] = $installedPlugins;
        }
        
        if (!$simple){
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
            
            echo "<html><head>";
            echo "<link rel='stylesheet' type='text/css' href='css/format.css'>";
            echo "</head><body><div class='center'><h1>".Sprachen::Get('main','title'.$selected_menu)."</h1></br>";

            echo "<form action='' method='post' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false'>";
            echo "<table border='0'><tr>";
            echo "<th valign='top'>";
            
            // Serverliste ausgeben
            echo "<div style='width:150px;word-break: break-all;'>";
            echo "<table border='0'>";
            echo "<tr><td class='e'>Serverliste</td></tr>";
            foreach($serverFiles as $serverFile){
                $file = pathinfo($serverFile)['filename'];
                echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('server',$file,($server == $file ? '<font color="maroon">'.$file.'</font>' : $file))."</td></tr>";
            }
            
            echo "<tr><th height='10'></th></tr>";
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('actionAddServer','OK','Server hinzufügen')."</td></tr>";
            echo Design::erstelleVersteckteEingabezeile($simple, $selected_server, 'selected_server', null);
           
            echo "</table>";
            echo "</div";
            
            echo "</th>";
            echo "<th width='2'></th>";
            
            echo "</th>";

            echo "<th width='600'><hr />";        
            $text='';
            $text .= "<table border='0' cellpadding='4' width='600'>";
            $text .= "<input type='hidden' name='selected_menu' value='{$selected_menu}'>";
            
            $text .= "<tr>";
            for ($i=0;$i<count($menuItems);$i++){
                if ($i%5==0 && $i>0) $text .= "<tr>";
                $item = $menuItems[$i];
                $type = $menuTypes[$i];
                $text .= "<td class='".($type==0?'h':'k')."'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu',$item,($selected_menu == $item ? '<font color="maroon">'.Sprachen::Get('main','title'.$item).'</font>' : Sprachen::Get('main','title'.$item)))."</div></td>";
            }
            $text .= "</tr></table>";
            echo $text;
            echo "<hr />";
        }
        
        #region Sprachwahl
        if (!$simple){
            echo "<input type='hidden' name='data[PL][language]' value='{$data['PL']['language']}'>";
            echo "<div align='center'>".Design::erstelleSubmitButtonGrafisch('actionSelectGerman', './images/de.gif', 32 , 22).Design::erstelleSubmitButtonGrafisch('actionSelectEnglish', './images/en.gif', 32 , 22)."</div>";
        }
        #endregion Sprachwahl

        if (file_exists(dirname(__FILE__) . '/segments/Zugang_ausgeben.php'))
            require_once dirname(__FILE__) . '/segments/Zugang_ausgeben.php';
         
        if (file_exists(dirname(__FILE__) . '/segments/Modulpruefung_ausgeben.php'))
        require_once dirname(__FILE__) . '/segments/Modulpruefung_ausgeben.php';
         
        if (file_exists(dirname(__FILE__) . '/segments/Pruefung_der_Erweiterungen_ausgeben.php'))
        require_once dirname(__FILE__) . '/segments/Pruefung_der_Erweiterungen_ausgeben.php';
         
        if (file_exists(dirname(__FILE__) . '/segments/Plattform_Datenbanknutzer.php'))
        require_once dirname(__FILE__) . '/segments/Plattform_Datenbanknutzer.php';
         
        if (file_exists(dirname(__FILE__) . '/segments/Grundinformationen.php'))
        require_once dirname(__FILE__) . '/segments/Grundinformationen.php';
         
        if (file_exists(dirname(__FILE__) . '/segments/Grundeinstellungen_ausgeben.php'))
            require_once dirname(__FILE__) . '/segments/Grundeinstellungen_ausgeben.php';
            
        if (file_exists(dirname(__FILE__) . '/segments/PlugInsInstallieren.php'))
            require_once dirname(__FILE__) . '/segments/PlugInsInstallieren.php';
            
        if (file_exists(dirname(__FILE__) . '/segments/Datenbank_informationen.php'))
            require_once dirname(__FILE__) . '/segments/Datenbank_informationen.php';

        if (file_exists(dirname(__FILE__) . '/segments/Datenbank_einrichten.php'))
            require_once dirname(__FILE__) . '/segments/Datenbank_einrichten.php';
            
        if (file_exists(dirname(__FILE__) . '/segments/Komponenten_erstellen.php'))
            require_once dirname(__FILE__) . '/segments/Komponenten_erstellen.php';
                     
        if (file_exists(dirname(__FILE__) . '/segments/Benutzerschnittstelle_einrichten.php'))
            require_once dirname(__FILE__) . '/segments/Benutzerschnittstelle_einrichten.php';
            
        if (file_exists(dirname(__FILE__) . '/segments/Komponenten.php'))
            require_once dirname(__FILE__) . '/segments/Komponenten.php';
                        
        if (file_exists(dirname(__FILE__) . '/segments/PlattformEinrichten.php'))
            require_once dirname(__FILE__) . '/segments/PlattformEinrichten.php';
         
        if (file_exists(dirname(__FILE__) . '/segments/Benutzer_erstellen.php'))
            require_once dirname(__FILE__) . '/segments/Benutzer_erstellen.php';
            

        if (!$simple){
            if (($selected_menu === 2 || $selected_menu === 3 || $selected_menu === 4) && false){
                echo "<table border='0' cellpadding='3' width='600'>";
                echo "<tr><td class='h'><div align='center'><input type='submit' name='actionInstall' value=' ".Sprachen::Get('main','installAll')." '></div></td></tr>";
                echo "</table><br />";
            }
                        
            #region zurück_weiter_buttons
            $text = '';
            $a='';$b='';
            if (array_search($selected_menu,$menuItems)>0){
                $item = $menuItems[array_search($selected_menu,$menuItems)-1];
                $a = Design::erstelleSubmitButtonFlach('selected_menu',$item, '<< zurueck').'<br><font size=1>('.Sprachen::Get('main','title'.$item).')</font>';
            }
            
            if (array_search($selected_menu,$menuItems)<count($menuItems)-1){
                $item = $menuItems[array_search($selected_menu,$menuItems)+1];
                $b = Design::erstelleSubmitButtonFlach('selected_menu',$item, 'weiter >>').'<br><font size=1>('.Sprachen::Get('main','title'.$item).')</font>';
            }
            
            echo "<table border='0' cellpadding='3' width='600'>";
            echo "<thead><tr><th align='left' width='50%'>{$a}</th><th align='right' width='50%'>{$b}</th></tr></thead>";
            if ($selected_menu==0){
                if (!isset($_POST['actionShowPhpInfo'])){
                    echo "<tr><th colspan='2'>".Design::erstelleSubmitButton("actionShowPhpInfo", 'PHPInfo')."</th></tr>";
                }
            }
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
            echo "<tr><td class='e'>".Sprachen::Get('general_informations','temp')."</td></tr>";
            echo "<tr><td>".$data['PL']['temp']."</td></tr>";
            echo "<tr><td class='e'>".Sprachen::Get('general_informations','files')."</td></tr>";
            echo "<tr><td>".$data['PL']['files']."</td></tr>";
            echo "</table>";
            echo "</div";
            
            echo "</th></tr></form></table>";

            echo "</div></body></html>";
        }

        if (isset($_POST['actionShowPhpInfo'])){
            ob_start();
            phpinfo();
            $phpinfo = array('phpinfo' => array());

            if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
                foreach($matches as $match)
                    if(strlen($match[1]))
                        $phpinfo[$match[1]] = array();
                    elseif(isset($match[3])){
                        $arr=array_keys($phpinfo);
                        $phpinfo[end($arr)][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
                    }else{
                        $arr = array_keys($phpinfo);
                        $phpinfo[end($arr)][] = $match[2]; 
                    }
                        
            echo "<br><br><br><br><div align='center'>";
            foreach($phpinfo as $name => $section) {
                echo "<h3>$name</h3>\n<table>\n";
                foreach($section as $key => $val) {
                    if(is_array($val))
                        echo "<tr><td>$key</td><td>$val[0]</td><td>$val[1]</td></tr>\n";
                    elseif(is_string($key))
                        echo "<tr><td>$key</td><td>$val</td></tr>\n";
                    else
                        echo "<tr><td>$val</td></tr>\n";
                }
                echo "</table>\n";
            }
            echo "</div>";
        }
        
        if ($simple)
            echo json_encode($output);
        
        if (!$simple)
            Einstellungen::speichereEinstellungen($server);
    }
}

// create a new instance of Installer class 
new Installer((isset($argv) ? $argv : null));
?>