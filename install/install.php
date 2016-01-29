<?php
set_time_limit(0);

/**
 * @file install.php contains the Installer class
 *
 * @author Till Uhlig
 * @date 2014
 */
define('ISCLI', PHP_SAPI === 'cli');

if (file_exists(dirname(__FILE__) . '/../Assistants/vendor/Slim/Slim/Route.php') && file_exists(dirname(__FILE__) . '/../Assistants/vendor/Slim/Slim/Slim.php')){
    // wenn der Installationsassitent über die Konsole aufgerufen wird, dürfen wird
    // Slim nicht verwenden (stürzt ab)
    if (!constant('ISCLI'))
        include_once dirname(__FILE__) . '/../Assistants/vendor/Slim/Slim/Slim.php';

    include_once dirname(__FILE__) . '/../Assistants/vendor/Slim/Slim/Route.php';
}

include_once dirname(__FILE__) . '/../Assistants/Request.php';
include_once dirname(__FILE__) . '/../Assistants/DBRequest.php';
include_once dirname(__FILE__) . '/../Assistants/Logger.php';
include_once dirname(__FILE__) . '/../Assistants/DBJson.php';
include_once dirname(__FILE__) . '/../Assistants/Structures.php';
include_once dirname(__FILE__) . '/include/Einstellungen.php';
include_once dirname(__FILE__) . '/include/Design.php';
include_once dirname(__FILE__) . '/include/Installation.php';
include_once dirname(__FILE__) . '/../Assistants/Language.php';
include_once dirname(__FILE__) . '/include/Zugang.php';

if (!constant('ISCLI') && in_array('Slim\Slim', get_declared_classes())){
    \Slim\Slim::registerAutoloader();
}



/**
 * A class, to handle requests to the Installer-Component
 */
class Installer
{
    /**
     * @var int[] $menuItems Enthält die Reihenfolge der Seiten (anhand der IDs),
     * diese stehen in den Segmente unter $page=Seitennummer
     */
    public static $menuItems = array(0,1,6,2,3,4,8); // 5, // ausgeblendet

    /**
     * @var int[] $menuTypes Die Art der Menüelemente
     * 0 = auf diesen Server angewendet, 1 = auf alle Server angewendet
     */
    public static $menuTypes = array(0,0,0,0,0,1,1);

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param string[] $_argv Konsolenparameter, null = leer
     */
    public function __construct($_argv)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));

        if ($_argv!=null){
            Installation::log(array('text'=>'Konsolenparameter gefunden'));

            // es gibt Konsolenparameter, diese werden nun $_POST zugewiesen,
            // sodass der Installationsassistent sie verwenden kann
            array_shift($_argv);
            foreach($_argv as $arg){
                $_POST[$arg] = 'OK';
                Installation::log(array('text'=>'setze $_POST['.$arg.'] = \'OK\''));
            }

            $this->CallInstall(true);
            return;
        }

        Installation::log(array('text'=>Installation::Get('main','intializeSlim')));
        if (in_array("Slim\\Slim", get_declared_classes())){
            // initialize slim
            $app = new \Slim\Slim(array( 'debug' => true ));
            $app->contentType('text/html; charset=utf-8');

            // POST,GET showInstall
            $app->map('(/)',
                            array($this, 'CallInstall'))->via('POST', 'GET','INFO' );

            // POST,GET showInstall
            $app->map('/checkModulesExtern(/)',
                            array($this, 'checkModulesExtern'))->via('POST', 'GET','INFO' );

            // run Slim
            Installation::log(array('text'=>Installation::Get('main','callSlim')));
            $app->run();
        } else {
            Installation::log(array('text'=>Installation::Get('main','noSlimFound')));
            $this->CallInstall();
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    /**
     * Prüft das Vorhandensein der benötigten Apache Module
     *
     * @param string[][] $data Die Serverdaten
     * @param bool $fail true = Fehler, false = sonst
     * @param int $errno Die Fehlernummer
     * @param string $error Der Fehlertext
     * @return string/string[] Die Json Darstellung (wenn constant('ISCLI')) oder das Array mit den Resultaten
     */
    public static function callCheckModules($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        if (constant('ISCLI')){
            Installation::log(array('text'=>Installation::Get('main','ISCLIEnabled')));
            return json_decode(Request::get($data['PL']['url'].'/install/install.php/checkModulesExtern',array(),'')['content'],true);
        } else {
            Installation::log(array('text'=>Installation::Get('main','ISCLIDisabled')));
            return ModulpruefungAusgeben::checkModules($data,$fail,$errno,$error);
        }
    }

    /**
     * Ruft die Funktion der Modulprüfung auf und gibt das Ergebnis aus
     */
    public function checkModulesExtern()
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $this->loadSegments();
        $dat = null;
        echo json_encode(ModulpruefungAusgeben::install(null,$dat,$dat,$dat));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    /**
     * Sucht die vorhandenen Segmentdateien
     *
     * @return string[] Die Pfade der Segmente
     */
    public function loadSegments()
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $p = dirname(__FILE__) . '/segments';
        Einstellungen::generatepath($p);
        if ($handle = opendir($p)) {
            $segs = array();
            while (false !== ($file = @readdir($handle))) {
                if ($file=='.' || $file=='..') continue;
                $segs[] = $file;
            }
            foreach($segs as $seg){
                if (is_dir(dirname(__FILE__) . '/segments/'.$seg)) continue;
                include_once dirname(__FILE__) . '/segments/'.$seg;
                Einstellungen::$segments[] = substr($seg,0,count($seg)-5);
            }
            @closedir($handle);
        }

        // sort segments by page and rank
        function cmp($a, $b)
        {
            $posA = 0;
            $posB = 0;

            if (isset($a::$page)) $posA = array_search($a::$page,Installer::$menuItems);
            if (isset($b::$page)) $posB = array_search($b::$page,Installer::$menuItems);

            $rankA = 100;
            $rankB = 100;

            if (isset($a::$rank)) $rankA = $a::$rank;
            if (isset($b::$rank)) $rankB = $b::$rank;

            if ($posA === false) return -1;
            if ($posB === false) return 1;

            if ($posA == $posB) {
                if ($rankA == $rankB)
                    return 0;
                return ($rankA < $rankB) ? -1 : 1;
            }
            return ($posA < $posB) ? -1 : 1;
        }

        Installation::log(array('text'=>Installation::Get('main','sortSegments')));
        usort(Einstellungen::$segments, "cmp");
        Installation::log(array('text'=>Installation::Get('main','existingSegments','default',array('segments'=>implode(',',Einstellungen::$segments)))));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    /**
     * Die Hauptfunktion des Installationsassistenten
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     */
    public function CallInstall($console = false)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));

        $output = array();
        $installFail = false;
        $simple = false;
        $data = array();
        $tmp = array();

        if (isset($_POST['data']))
            $data = $_POST['data'];

        if (isset($_POST['tmp']))
            $tmp = $_POST['tmp'];

        if (isset($_POST['simple']))
            $simple=true;

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

        // URLs und Pfade sollen keinen / am Ende haben (damit es einheitlich ist)
        if (isset($data['PL']['url'])) {Installation::log(array('text'=>Installation::Get('main','data_PL_url_Changed')));$data['PL']['url'] = rtrim($data['PL']['url'], '/');}
        if (isset($data['PL']['urlExtern'])) {Installation::log(array('text'=>Installation::Get('main','data_PL_urlExtern_Changed')));$data['PL']['urlExtern'] = rtrim($data['PL']['urlExtern'], '/');}
        if (isset($data['PL']['temp'])) {Installation::log(array('text'=>Installation::Get('main','data_PL_temp_Changed')));$data['PL']['temp'] = rtrim($data['PL']['temp'], '/');}
        if (isset($data['PL']['files'])) {Installation::log(array('text'=>Installation::Get('main','data_PL_files_Changed')));$data['PL']['files'] = rtrim($data['PL']['files'], '/');}
        if (isset($data['PL']['init'])) {Installation::log(array('text'=>Installation::Get('main','data_PL_init_Changed')));$data['PL']['init'] = rtrim($data['PL']['init'], '/');}

        // check which server is selected
        $server = isset($_POST['server']) ? $_POST['server'] : null;
        Einstellungen::$selected_server = isset($_POST['selected_server']) ? $_POST['selected_server'] : null;

        // behandle das MasterPasswort
        $serverHash = md5(Einstellungen::$selected_server);
        $tmp[$serverHash]['newMasterPassword'] = (isset($tmp[$serverHash]['masterPassword']) ? $tmp[$serverHash]['masterPassword'] : null);
        if (isset($_POST['changeMasterPassword'])){
            Einstellungen::$masterPassword[$serverHash] = (isset($tmp[$serverHash]['oldMasterPassword']) ? $tmp[$serverHash]['oldMasterPassword'] : null);
            $tmp[$serverHash]['masterPassword'] = Einstellungen::$masterPassword[$serverHash];
        } else {
            Einstellungen::$masterPassword[$serverHash] = $tmp[$serverHash]['newMasterPassword'];
            $tmp[$serverHash]['masterPassword'] = Einstellungen::$masterPassword[$serverHash];
        }

        foreach($tmp as $key => $tm){
            if (!isset($tm['masterPassword']) && isset($tm['oldMasterPassword']) && trim($tm['oldMasterPassword']) != ''){
                $tmp[$key]['masterPassword'] = $tm['oldMasterPassword'];
                $tmp[$key]['newMasterPassword'] = (isset($tmp[$key]['oldMasterPassword']) ? $tmp[$key]['oldMasterPassword'] : null);
                Einstellungen::$masterPassword[$key] = $tm['oldMasterPassword'];
            }
            if (!isset(Einstellungen::$masterPassword[$key]) && isset($tmp[$key]['masterPassword']) && trim($tmp[$key]['masterPassword']) != ''){
                $tmp[$key]['newMasterPassword'] = (isset($tmp[$key]['masterPassword']) ? $tmp[$key]['masterPassword'] : null);
                Einstellungen::$masterPassword[$key] = $tm['masterPassword'];
            }

        }

        // prüfe ob der Servername geändert wurde
        if (isset($data['SV']['name']) && $data['SV']['name']!==null && Einstellungen::$selected_server!==null){
            if ($data['SV']['name']!=Einstellungen::$selected_server){
                Installation::log(array('text'=>'Servername geändert von '.$data['SV']['name'].' nach '.Einstellungen::$selected_server));
                $oldServerHash = md5($data['SV']['name']);
                $newServerHash = md5(Einstellungen::$selected_server);

                if (isset(Einstellungen::$masterPassword[$oldServerHash])){
                    Installation::log(array('text'=>Installation::Get('main','moveMasterPassword')));
                    Einstellungen::$masterPassword[$newServerHash] = Einstellungen::$masterPassword[$oldServerHash];
                    unset(Einstellungen::$masterPassword[$oldServerHash]);
                }

                Einstellungen::umbenennenEinstellungen(Einstellungen::$selected_server,$data['SV']['name']);
            }
        }

        // check which menu is selected
        $selected_menu = intval(isset($_POST['selected_menu']) ? $_POST['selected_menu'] : self::$menuItems[0]);
        Installation::log(array('text'=>'gewähltes Menü = '.$selected_menu));

        if (isset($_POST['action']) && $_POST['action']=='update'){
            Installation::log(array('text'=>'Update erkannt'));
            $selected_menu = -1;
        }

        // check server configs
        Einstellungen::$serverFiles = Installation::GibServerDateien();

        // add Server
        $addServer = false;
        $addServerResult = array();
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionAddServer']) || count(Einstellungen::$serverFiles)==0) && !$installFail){
            Installation::log(array('text'=>Installation::Get('main','createConf')));
            $addServer = true;
            $server = Einstellungen::NeuenServerAnlegen();
            Einstellungen::$serverFiles[] = $server;
            $server = pathinfo($server)['filename'];
            Einstellungen::ladeEinstellungen($server,$data);
            Einstellungen::speichereEinstellungen($server,$data);
        }

        // save data on switching between server-confs
        if (Einstellungen::$selected_server!==null && $server!=null){
            if ($server!=Einstellungen::$selected_server){
                Installation::log(array('text'=>Installation::Get('main','changeConf','default',array('newConf'=>Einstellungen::$selected_server))));
                Einstellungen::ladeEinstellungen(Einstellungen::$selected_server,$data);
                //Einstellungen::speichereEinstellungen(Einstellungen::$selected_server,$data);
                Einstellungen::resetConf();
            }
        }

        // select first if no server is selected
        if (Einstellungen::$selected_server==null && $server==null){
            Installation::log(array('text'=>Installation::Get('main','chooseConf','default',array('conf'=>pathinfo(Einstellungen::$serverFiles[0])['filename']))));
            Einstellungen::$selected_server = pathinfo(Einstellungen::$serverFiles[0])['filename'];
            $server = Einstellungen::$selected_server;
        }

        if ($server!=null)
            Einstellungen::$selected_server=$server;

        $server=Einstellungen::$selected_server;
        $data['SV']['name'] = Einstellungen::$selected_server;

        $serverHash = md5(Einstellungen::$selected_server);

        if (!isset($tmp[$serverHash]['masterPassword'])){
            Installation::log(array('text'=>Installation::Get('main','missingMasterPassword')));
            $tmp[$serverHash]['masterPassword'] = null;
        }

        // nun kann die Konfiguration des gewählten Servers geladen werden (selected_server)
        Einstellungen::ladeEinstellungen(Einstellungen::$selected_server,$data);

        Einstellungen::$masterPassword[$serverHash] = (isset($tmp[$serverHash]['newMasterPassword']) ? $tmp[$serverHash]['newMasterPassword'] : '');
        if (isset(Einstellungen::$masterPassword[$serverHash])){
            $tmp[$serverHash]['masterPassword'] = Einstellungen::$masterPassword[$serverHash];
        }


        // load language
        Language::loadLanguage($data['PL']['language'], 'default', 'ini');

        // ermittle alle Segmente
        $this->loadSegments();

        if (Einstellungen::$accessAllowed){
            if ($console)
                $data['ZV']['zv_type'] = 'local';

            if ($simple)
                $data['ZV']['zv_type'] = 'local';

            if (isset($_POST['action']))
                $data['action'] = $_POST['action'];

            $fail = false;
            $errno = null;
            $error = null;

            if ($simple)
                $selected_menu = -1;

        }

        if (!$console && !$simple){
            // select language - german
            if (isset($_POST['actionSelectGerman']) || isset($_POST['actionSelectGerman_x'])){
                Installation::log(array('text'=>Installation::Get('main','languageGermanSelected')));
                $data['PL']['language'] = 'de';
            }

            // select language - english
            if (isset($_POST['actionSelectEnglish']) || isset($_POST['actionSelectEnglish_x'])){
                Installation::log(array('text'=>Installation::Get('main','languageEnglishSelected')));
                $data['PL']['language'] = 'en';
            }

            echo "<html><head>";
            echo "<link rel='stylesheet' type='text/css' href='css/format.css'>";

            if ($selected_menu==-1){
                if (isset($_POST['action'])){
                    $titleText=Installation::Get('main','title'.$_POST['action']);
                }
            } else {
                $titleText=Installation::Get('main','title'.$selected_menu);
            }

            echo "</head><body><div class='center'>";

            if (Einstellungen::$accessAllowed && $titleText!=='???'){
                Installation::log(array('text'=>Installation::Get('main','pageTitle','default',array('titleText'=>$titleText))));
                echo "<h1>".$titleText."</h1></br>";
            } elseif($titleText === '???') {
                Installation::log(array('text'=>Installation::Get('main','unknownTitle'), 'logLevel'=>LogLevel::ERROR));
            }

            echo "<form action='' method='post' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false'>";
        }

        if (Einstellungen::$accessAllowed){

            // führe die Initialisierungsfunktionen der Segmente aus
            Installation::log(array('text'=>Installation::Get('main','startsSegmentInitization'), 'logLevel'=>LogLevel::INFO));
            foreach(Einstellungen::$segments as $segs){
                if (!is_callable("{$segs}::init")) continue;
                ///if (!isset($segs::$initialized)) continue;
                ///if ($segs::$initialized) continue;
                $segs::init($console, $data, $fail, $errno, $error);
            }
            Installation::log(array('text'=>Installation::Get('main','endSegmentInitization'), 'logLevel'=>LogLevel::INFO));

            // installiere die Segmente
            Installation::log(array('text'=>Installation::Get('main','beginSegmentInstallation'), 'logLevel'=>LogLevel::INFO));
            $segmentResults = array();
            foreach(Einstellungen::$segments as $segs){
                if (!isset($segs::$onEvents)) {
                    Installation::log(array('text'=>Installation::Get('main','segmentHasNotEvents','default',array('segs'=>$segs))));
                    continue;
                }

                foreach ($segs::$onEvents as $event){
                    if (isset($event['enabledInstall']) && !$event['enabledInstall']){
                        Installation::log(array('text'=>Installation::Get('main','segmentEventDisabled','default',array('segs'=>$segs))));
                        continue;
                    }

                    $isSetEvent = false;
                    if (isset($_POST['action']) && in_array($_POST['action'],$event['event'] )){
                        Installation::log(array('text'=>Installation::Get('main','segmentEventFound','default',array('segs'=>$segs,'action'=>$_POST['action']))));
                        $isSetEvent = true;
                    }

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
                        if (isset($event['procedure'])){
                            Installation::log(array('text'=>Installation::Get('main','segmentProcedureRequired','default',array('segs'=>$segs, 'procedure'=>$event['procedure']))));
                            $procedure = $event['procedure'];
                        }

                        $result['content'] = Zugang::Ermitteln($event['name'],$segs.'::'.$procedure,$data, $fail, $errno, $error);
                        $segs::$installed=true;

                        $installFail = $fail;
                        $result['fail'] = $fail;
                        $result['errno'] = $errno;
                        $result['error'] = $error;
                        if ($console && !$simple){
                            Installation::log(array('text'=>Installation::Get('main','segmentResult','default',array('segs'=>$segs))));
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
            Installation::log(array('text'=>Installation::Get('main','endSegmentInstallation'), 'logLevel'=>LogLevel::INFO));
        }


        if (!$console && !$simple){
            echo "<table border='0'><tr>";
            echo "<th valign='top'>";

            echo "<div style='width:150px;word-break: break-all;'>";

            echo "<table border='0'>";

            // ab hier wird die linke Infoleiste erzeugt
            Installation::log(array('text'=>Installation::Get('main','beginInfoBar')));
            foreach(Einstellungen::$segments as $segs){
                if (isset($segs::$enabledShow) && !$segs::$enabledShow) continue;
                if (!is_callable("{$segs}::showInfoBar")) continue;
                $segs::showInfoBar($data);
                echo "<tr><th height='10'></th></tr>";
            }
            Installation::log(array('text'=>Installation::Get('main','endInfoBar')));

            echo "</table>";

            echo "</div";

            echo "</th>";
            echo "<th width='2'></th>";

            echo "</th>";

            echo "<th width='600'><hr />";
            if (Einstellungen::$accessAllowed){
                echo "<table border='0' cellpadding='4' width='600'>";
                echo "<input type='hidden' name='selected_menu' value='{$selected_menu}'>";

                echo "<tr>";
                $text = '';
                for ($i=0;$i<count(self::$menuItems);$i++){
                    if ($i%5==0 && $i>0) echo "</tr><tr>";
                    $item = self::$menuItems[$i];
                    $type = self::$menuTypes[$i];
                    echo "<td class='".($type==0?'h':($type==1?'k':'g'))."'><div align='center'>".Design::erstelleSubmitButtonFlach('selected_menu',$item,($selected_menu == $item ? '<font color="maroon">'.Installation::Get('main','title'.$item).'</font>' : Installation::Get('main','title'.$item)))."</div></td>";
                }
                echo "</tr>";
                echo "</table>";
            }

            echo "<hr />";
        }

        #region Sprachwahl
        if (!$console && !$simple){
            Installation::log(array('text'=>Installation::Get('main','drawLanguageSelection')));
            echo "<input type='hidden' name='data[PL][language]' value='{$data['PL']['language']}'>";
            echo "<div align='center'>".Design::erstelleSubmitButtonGrafisch('actionSelectGerman', './images/de.gif', 32 , 22).Design::erstelleSubmitButtonGrafisch('actionSelectEnglish', './images/en.gif', 32 , 22)."</div>";
        }
        #endregion Sprachwahl


        if (true){
            // show segments
            Installation::log(array('text'=>Installation::Get('main','beginShowSegments')));
            foreach(Einstellungen::$segments as $segs){
                if (isset($segs::$enabledShow) && !$segs::$enabledShow) continue;

                if (!isset($segs::$page) || $segs::$page===$selected_menu || (isset($segs::$installed) && $segs::$installed)){
                    if (!is_callable("{$segs}::show")) continue;

                    $result = (isset($segmentResults[$segs::$name]) ? $segmentResults[$segs::$name] : array());
                    $segs::show($console, $result, $data);
                }
            }
            Installation::log(array('text'=>Installation::Get('main','endShowSegments')));
        }

        if (Einstellungen::$accessAllowed){
            if ($simple){
                 if ($installFail){
                     echo "0";
                 } else
                     echo "1";
            }


            if (!$console && !$simple){
                if (($selected_menu === 2 || $selected_menu === 3 || $selected_menu === 4) && false){
                    echo "<table border='0' cellpadding='3' width='600'>";
                    echo "<tr><td class='h'><div align='center'><input type='submit' name='actionInstall' value=' ".Installation::Get('main','installAll')." '></div></td></tr>";
                    echo "</table><br />";
                }

                #region zurück_weiter_buttons
                $a='';$b='';
                if (array_search($selected_menu,self::$menuItems)>0){
                    $item = self::$menuItems[array_search($selected_menu,self::$menuItems)-1];
                    $a = Design::erstelleSubmitButtonFlach('selected_menu',$item, Installation::Get('main','back')).'<br><font size=1>('.Installation::Get('main','title'.$item).')</font>';
                }

                if ($selected_menu>=0 && array_search($selected_menu,self::$menuItems)<count(self::$menuItems)-1){
                    $item = self::$menuItems[array_search($selected_menu,self::$menuItems)+1];
                    $b = Design::erstelleSubmitButtonFlach('selected_menu',$item, Installation::Get('main','next')).'<br><font size=1>('.Installation::Get('main','title'.$item).')</font>';
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

                // ab hier wird die Infoleiste mit der aktuellen Konfiguration erstellt
                // (die Leiste rechts am Rand)
                echo "<div style='width:150px;word-break: break-all;'>";
                echo "<table border='0'>";
                foreach(Einstellungen::$segments as $segs){
                    if (!is_callable("{$segs}::getSettingsBar")) continue;

                    $settings = $segs::getSettingsBar($data);
                    if (count($settings)>0){
                        foreach ($settings as $key => $values){
                            // values entspricht
                            // [0] = Name/Überschift
                            // [1] = Wert/Zustand
                            // [2] = Standardwert mit dem verglichen werden soll (Gleichheit wird zum Warnhinweis) (null = Nein)
                            // [3] = Fehler soll angezeigt werden (true = Fehler, false = sonst)

                            if (!isset($values[0]) || !isset($values[1])) continue;
                            $add='';
                            $addText='';
                            if (isset($values[2])){
                                // Standardwert soll geprüft werden
                                if ($values[1] == $values[2]){
                                    $add = 'class="warning"';
                                    $addText = '&lt;&lt;'.Installation::Get('main','warnDefault')."&gt;&gt;<br/>";
                                }
                            } elseif (isset($values[3]) && $values[3] == true) {
                                $add = 'class="error"';
                                $addText = '&lt;&lt;'.Installation::Get('main','errorValue')."&gt;&gt;<br/>";
                            }

                            echo "<tr><td class='e'>".$values[0]."</td></tr>";
                            echo "<tr><td><span {$add}>".$addText."</span>".$values[1]."</td></tr>";
                            echo "<tr><th></th></tr>";
                        }
                    }
                }
                echo "</table>";
                echo "</div>";

                echo "<input type='hidden' name='data[LOGGER][logLevel]' value='".Installation::$logLevel."'>";

                echo "</th></tr></form></table>";

                echo "</div></body></html>";
            }

            if (isset($_POST['actionShowPhpInfo'])){
                Installation::log(array('text'=>Installation::Get('main','beginShowPhPInfo')));

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
                Installation::log(array('text'=>Installation::Get('main','endShowPhPInfo')));
            }

        }

        if ($console && !$simple)
            echo json_encode($output);

        if (!$console && !$simple)
            Einstellungen::speichereEinstellungen($server,$data);
    }
}

// create a new instance of Installer class
if (isset($_POST['data']['LOGGER']['logLevel'])){
    Installation::$logLevel = $_POST['data']['LOGGER']['logLevel'];
}
if (isset($_POST['data']['PL']['language'])){
    Language::loadLanguage($_POST['data']['PL']['language'], 'default', 'ini');
} else {
    Language::loadLanguage('de', 'default', 'ini');  
}

Installation::log(array('text'=>Installation::Get('main','beginInstance')));
new Installer((isset($argv) ? $argv : null));
Installation::log(array('text'=>Installation::Get('main','endInstance')));
