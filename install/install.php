<?php
set_time_limit(0);

/**
 * @file install.php contains the Installer class
 *
 * @author Till Uhlig
 * @date 2014
 */
define('ISCLI', PHP_SAPI === 'cli'); 

if (!constant('ISCLI'))
    require_once dirname(__FILE__) . '/../Assistants/Slim/Slim.php';
require_once dirname(__FILE__) . '/../Assistants/Slim/Route.php';

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

if (!constant('ISCLI'))
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
    private $segments = array();
    
    public static $menuItems = array(0,1,6,2,3,4);// 5, // ausgeblendet
    public static $menuTypes = array(0,0,0,0,0,1,1);
    
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
                        
        // POST,GET showInstall
        $this->app->map('/checkModulesExtern(/)',
                        array($this, 'checkModulesExtern'))->via('POST', 'GET','INFO' );

        // run Slim
        $this->app->run();  
    }
    
    public static function callCheckModules($data, &$fail, &$errno, &$error)
    {
        if (constant('ISCLI')){
            return json_decode(Request::get($data['PL']['url'].'/install/install.php/checkModulesExtern',array(),'')['content'],true);
        } else {
            return ModulpruefungAusgeben::checkModules($data,$fail,$errno,$error);
        }
    }
    
    public function checkModulesExtern()
    {
        $this->loadSegments();
        $dat = null;
        echo json_encode(ModulpruefungAusgeben::install(null,$dat,$dat,$dat));
    }
    
    public function loadSegments()
    {
        $p = dirname(__FILE__) . '/segments';
        Einstellungen::generatepath($p);
        if ($handle = opendir($p)) {
            $segs = array();
            while (false !== ($file = @readdir($handle))) {
                if ($file=='.' || $file=='..') continue;
                $segs[] = $file;
            }
            foreach($segs as $seg){
                include_once dirname(__FILE__) . '/segments/'.$seg;
                $this->segments[] = substr($seg,0,count($seg)-5);
            }
            @closedir($handle);
        }   
        
        // sort segments by page and rank
        function cmp($a, $b)
        {
            $posA = array_search($a::$page,Installer::$menuItems);
            $posB = array_search($b::$page,Installer::$menuItems);
            $rankA = $a::$rank;
            $rankB = $b::$rank;
            
            if ($posA === false) return -1;
            if ($posB === false) return 1;
            
            if ($posA == $posB) {
                if ($rankA == $rankB)
                    return 0;
                return ($rankA < $rankB) ? -1 : 1;
            }
            return ($posA < $posB) ? -1 : 1;
        }

        usort($this->segments, "cmp");
    }
    
    public function CallInstall($console = false)
    {
    
        $output = array();
        $installFail = false;
        $simple=false;
        
        if (isset($_POST['data']))
            $data = $_POST['data'];
        
        if (isset($_POST['simple']))
            $simple=true;

        Variablen::Initialisieren($data);
        $data['P']['masterPassword'] = (isset($data['P']['masterPassword']) ? $data['P']['masterPassword'] : '');
        
        if (isset($_POST['update'])) 
            $_POST['action'] = 'update';
        
        if (isset($_POST['actionInstall'])) 
            $_POST['action'] = 'install';
        
        if (isset($_POST['actionUpdate'])) 
            $_POST['action'] = 'update';
        
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
        $selected_menu = intval(isset($_POST['selected_menu']) ? $_POST['selected_menu'] : self::$menuItems[0]);
        
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
            Einstellungen::ladeEinstellungen($server,$data);
            ///$data['SV']['name'] = $server;
            Einstellungen::speichereEinstellungen($server,$data);
        }
                    
        // save data on switching between server-confs
        if ($selected_server!==null && $server!=null){
            if ($server!=$selected_server){
                Einstellungen::ladeEinstellungen($selected_server,$data);
                Variablen::Einsetzen($data);
                Einstellungen::speichereEinstellungen($selected_server,$data);
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
        ///$data['SV']['name'] = $server;
        
        Einstellungen::ladeEinstellungen($server,$data);
        Variablen::Einsetzen($data);
        
        if ($console)
            $data['ZV']['zv_type'] = 'local';
        
        if ($simple)
            $data['ZV']['zv_type'] = 'local';
        
        if (isset($_POST['action']))
            $data['action'] = $_POST['action'];
        
        $this->loadSegments();           
        foreach($this->segments as $segs){
            $segs::init($console, $data, $fail, $errno, $error);
        }

        
        $fail = false;
        $errno = null;
        $error = null;
        
        if ($simple)
            $selected_menu = -1;
        
        $segmentResults = array();
        
        // install segments
        foreach($this->segments as $segs){
            foreach ($segs::$onEvents as $event){
                if (isset($event['enabledInstall']) && !$event['enabledInstall']) 
                    continue;
                
                $isSetEvent = false;
                if (isset($_POST['action']) && in_array($_POST['action'],$event['event'] ))
                    $isSetEvent = true;
                
                foreach ($event['event'] as $ev){
                    if (isset($_POST[$ev])){
                        $isSetEvent = true;
                        break;
                    }
                }
                
                if (!$installFail && ( 
                    ($segs::$page===$selected_menu && in_array('page',$event['event'] )) || $isSetEvent
                    )){
                    $result = array();
                    $procedure = 'install';
                    if (isset($event['procedure']))
                        $procedure = $event['procedure'];

                    $result['content'] = Zugang::Ermitteln($event['name'],$segs.'::'.$procedure,$data, $fail, $errno, $error);
                    $segs::$installed=true;
                                    
                    $installFail = $fail;
                    $result['fail'] = $fail;
                    $result['errno'] = $errno;
                    $result['error'] = $error;
                    if ($console && !$simple){
                        $output[$segs::$name] = $result;
                    }
                    $fail = false;
                    $errno = null;
                    $error = null;
                    
                    if (!isset($segmentResults[$segs::$name]))
                        $segmentResults[$segs::$name] = array();
                    
                    $segmentResults[$segs::$name][$event['name']] = $result;
                }
            }
        }

        
        if (!$console && !$simple){
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
            // Serverliste ausgeben
            echo "<table border='0'>";
            echo "<tr><td class='e'>".Sprachen::Get('main','serverList')."</td></tr>";
            foreach($serverFiles as $serverFile){
                $file = pathinfo($serverFile)['filename'];
                echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('server',$file,($server == $file ? '<font color="maroon">'.$file.'</font>' : $file))."</td></tr>";
            }
            
            echo "<tr><th height='10'></th></tr>";
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('actionAddServer','OK',Sprachen::Get('main','addServer'))."</td></tr>";
            echo Design::erstelleVersteckteEingabezeile($console, $selected_server, 'selected_server', null);
            
            // master-Passwort abfragen
            echo "<tr><th height='10'></th></tr>";


            echo "<tr><td class='e'>".Sprachen::Get('main','masterPassword')."</td></tr>";
            echo "<tr><td class='v'>".Design::erstellePasswortzeile($console, $data['P']['masterPassword'], 'data[P][masterPassword]', $data['P']['masterPassword'])."</td></tr>";
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
            for ($i=0;$i<count(self::$menuItems);$i++){
                if ($i%5==0 && $i>0) $text .= "<tr>";
                $item = self::$menuItems[$i];
                $type = self::$menuTypes[$i];
                $text .= "<td class='".($type==0?'h':'k')."'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu',$item,($selected_menu == $item ? '<font color="maroon">'.Sprachen::Get('main','title'.$item).'</font>' : Sprachen::Get('main','title'.$item)))."</div></td>";
            }
            $text .= "</tr></table>";
            echo $text;
            echo "<hr />";
        }
        
        #region Sprachwahl
        if (!$console && !$simple){
            echo "<input type='hidden' name='data[PL][language]' value='{$data['PL']['language']}'>";
            echo "<div align='center'>".Design::erstelleSubmitButtonGrafisch('actionSelectGerman', './images/de.gif', 32 , 22).Design::erstelleSubmitButtonGrafisch('actionSelectEnglish', './images/en.gif', 32 , 22)."</div>";
        }
        #endregion Sprachwahl

        
        // show segments
        foreach($this->segments as $segs){
            if (!$segs::$enabledShow) 
                continue;
            
            if ($segs::$page===$selected_menu || $segs::$installed){
                $result = (isset($segmentResults[$segs::$name]) ? $segmentResults[$segs::$name] : array());     
                $segs::show($console, $result, $data);
            }
        }
        
        if ($simple){
             if ($installFail){
                 echo "0";
             } else
                 echo "1";
        }
            

        if (!$console && !$simple){
            if (($selected_menu === 2 || $selected_menu === 3 || $selected_menu === 4) && false){
                echo "<table border='0' cellpadding='3' width='600'>";
                echo "<tr><td class='h'><div align='center'><input type='submit' name='actionInstall' value=' ".Sprachen::Get('main','installAll')." '></div></td></tr>";
                echo "</table><br />";
            }
                        
            #region zurück_weiter_buttons
            $text = '';
            $a='';$b='';
            if (array_search($selected_menu,self::$menuItems)>0){
                $item = self::$menuItems[array_search($selected_menu,self::$menuItems)-1];
                $a = Design::erstelleSubmitButtonFlach('selected_menu',$item, Sprachen::Get('main','back')).'<br><font size=1>('.Sprachen::Get('main','title'.$item).')</font>';
            }
            
            if (array_search($selected_menu,self::$menuItems)<count(self::$menuItems)-1){
                $item = self::$menuItems[array_search($selected_menu,self::$menuItems)+1];
                $b = Design::erstelleSubmitButtonFlach('selected_menu',$item, Sprachen::Get('main','next')).'<br><font size=1>('.Sprachen::Get('main','title'.$item).')</font>';
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
            echo "<tr><td class='e'>".Sprachen::Get('general_informations','urlExtern')."</td></tr>";
            echo "<tr><td>".$data['PL']['urlExtern']."</td></tr>";
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
        
        if ($console && !$simple)
            echo json_encode($output);
        
        if (!$console && !$simple)
            Einstellungen::speichereEinstellungen($server,$data);
    }
}

// create a new instance of Installer class 
new Installer((isset($argv) ? $argv : null));
