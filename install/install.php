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
        $menuItems = array(5, 0,1,2,3,4);
        $selected_menu = intval(isset($_POST['selected_menu']) ? $_POST['selected_menu'] : $menuItems[0]);
        
        // check server configs
        $serverFiles = array();
        if ($handle = opendir(dirname(__FILE__) . '/config')) {
            while (false !== ($file = readdir($handle))) {
                if ($file=='.' || $file=='..') continue;
                $serverFiles[] = $file;
            }
            closedir($handle);
        }
          
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
        
        $fail = false;
        $errno = null;
        $error = null;
        
        if ($selected_menu === 0 || ($simple && isset($_POST['actionCheckModules']))){
            // check if apache modules are existing
            $modules = Zugang::Ermitteln('actionCheckModules','Installer::checkModules',$data, $fail, $errno, $error);
            
            if ($simple)
                $output['actionCheckModules'] = $modules;
        }
        
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
            echo "<th width='2'></th>";
            
            echo "</th>";

            echo "<th width='600'><hr />";        
            $text='';
            $text .= "<table border='0' cellpadding='4' width='600'>";
            $text .= "<tr>";
            $text .= "<input type='hidden' name='selected_menu' value='{$selected_menu}'>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu','5',($selected_menu == 5 ? '<font color="maroon">Zugangsdaten</font>' : 'Zugangsdaten'))."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu','0',($selected_menu == 0 ? '<font color="maroon">Informationen</font>' : 'Informationen'))."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu','1',($selected_menu == 1 ? '<font color="maroon">Einstellungen</font>' : 'Einstellungen'))."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu','2',($selected_menu == 2 ? '<font color="maroon">Datenbank</font>' : 'Datenbank'))."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu','3',($selected_menu == 3 ? '<font color="maroon">Komponenten</font>' : 'Komponenten'))."</div></td>";
            $text .= "<td class='h'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu','4',($selected_menu == 4 ? '<font color="maroon">Plattform</font>' : 'Plattform'))."</div></td>";
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

        require_once dirname(__FILE__) . '/segments/Zugang_ausgeben.php';
        require_once dirname(__FILE__) . '/segments/Modulpruefung_ausgeben.php';
        require_once dirname(__FILE__) . '/segments/Pruefung_der_Erweiterungen_ausgeben.php';
        require_once dirname(__FILE__) . '/segments/Plattform_Datenbanknutzer.php';
        require_once dirname(__FILE__) . '/segments/Grundinformationen.php';
        require_once dirname(__FILE__) . '/segments/Grundeinstellungen_ausgeben.php';
        require_once dirname(__FILE__) . '/segments/Datenbank_informationen.php';
        require_once dirname(__FILE__) . '/segments/Datenbank_einrichten.php';
        require_once dirname(__FILE__) . '/segments/Benutzerschnittstelle_einrichten.php';
        require_once dirname(__FILE__) . '/segments/PlugInsInstallieren.php';
        require_once dirname(__FILE__) . '/segments/Komponenten.php';
        require_once dirname(__FILE__) . '/segments/PlattformEinrichten.php';
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
            if (array_search($selected_menu,$menuItems)>0)
                $a = Design::erstelleSubmitButtonFlach('selected_menu',$menuItems[array_search($selected_menu,$menuItems)-1], '<< zurueck');
                
            if (array_search($selected_menu,$menuItems)<count($menuItems)-1)
                $b = Design::erstelleSubmitButtonFlach('selected_menu',$menuItems[array_search($selected_menu,$menuItems)+1], 'weiter >>');
            
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