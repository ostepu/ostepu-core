<?php
/**
 * @file Paketverwaltung.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */

#region Paketverwaltung
class Paketverwaltung
{
    private static $initialized=false;
    public static $name = 'initPackages';
    public static $installed = false;
    public static $page = 6;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate='Paketverwaltung';
    private static $packagePath = 'packages';

    public static $onEvents = array(
                                    'check'=>array(
                                        'name'=>'checkPlugins',
                                        'event'=>array('page'),
                                        'procedure'=>'installCheckPackages'
                                        ),
                                    'install'=>array(
                                        'name'=>'installPlugins',
                                        'event'=>array('actionInstallPlugins','update'),
                                        'procedure'=>'installInstallPackages',
                                        'enabledInstall'=>true
                                        ),
                                    'uninstall'=>array(
                                        'name'=>'uninstallPlugins',
                                        'event'=>array('actionUninstallPlugins'),
                                        'procedure'=>'installUninstallPackages',
                                        'enabledInstall'=>false
                                        )
                                    );

    public static function getPackagePath($data)
    {
        return $mainPath = $data['PL']['localPath'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), self::$packagePath);
    }

    public static function getDefaults($data)
    {
        $defaultSelectedPlugins = array('CORE','INSTALL','PHP-MARKDOWN','PHPFASTCACHE','SLIM','VALIDATION','FORMS');

        $res = array();
        $pluginFiles = self::getPackageDefinitions($data);
        foreach($pluginFiles as $plug){
            $filePath = self::getPackagePath($data) . DIRECTORY_SEPARATOR .$plug;
            if (is_readable($filePath)){
                $input = file_get_contents($filePath);
                $input = json_decode($input,true);
                if ($input == null){
                    // Fehler beim dekodieren
                } else  {
                    if (isset($input['name'])){
                        $name = $input['name'];
                        $res['plug_install_'.$name] = array('data[PLUG][plug_install_'.$name.']', (in_array($name,$defaultSelectedPlugins)?$name:'_'));
                    }
                }
            }
        }
        $res['details'] = array('data[PLUG][details]', null);
        return $res;
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        $def = self::getDefaults($data);

        $text = '';
        foreach($def as $defName => $defVar){
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG'][$defName], $defVar[0], $defVar[1], true);
        }
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG']['details'], 'data[PLUG][details]', $def['details'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    private static $pluginFiles=null;
    public static function getPackageDefinitions($data)
    {
        if(self::$pluginFiles !== null){
            return self::$pluginFiles;
        }

        self::$pluginFiles = array();
        try {
            $handle = opendir(self::getPackagePath($data));
        } catch (Exception $e) {
            // der Ordner konnte nicht zugegriffen werden
            Installation::log(array('text'=>self::getPackagePath($data).' existiert nicht oder es fehlt die Zugriffsberechtigung.','logLevel'=>LogLevel::ERROR));
            Installer::$messages[] = array('text'=>self::getPackagePath($data).' existiert nicht oder es fehlt die Zugriffsberechtigung.','type'=>'error');
            return self::$pluginFiles;
        }
        
        if ($handle !== false) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file,-5)!='.json' || $file=='.' || $file=='..') continue;
                if (is_dir(self::getPackagePath($data). DIRECTORY_SEPARATOR .$file)) continue;
                self::$pluginFiles[] = $file;
            }
            closedir($handle);
        }

        return self::$pluginFiles;
    }

    private static $selectedPluginFiles=null;
    public static function getSelectedPackageDefinitions($data)
    {
        if(self::$selectedPluginFiles !== null){
            return self::$selectedPluginFiles;
        }

        self::$selectedPluginFiles = array();
        try {
            $handle = opendir(self::getPackagePath($data));
        } catch (Exception $e) {
            // der Ordner konnte nicht zugegriffen werden
            Installation::log(array('text'=>self::getPackagePath($data).' existiert nicht oder es fehlt die Zugriffsberechtigung.','logLevel'=>LogLevel::ERROR));
            Installer::$messages[] = array('text'=>self::getPackagePath($data).' existiert nicht oder es fehlt die Zugriffsberechtigung.','type'=>'error');
            return self::$selectedPluginFiles;
        }
        
        if ($handle !== false) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file,-5)!='.json' || $file=='.' || $file=='..') continue;
                if (is_dir(self::getPackagePath($data) . DIRECTORY_SEPARATOR . $file)) continue;
                $dat = file_get_contents(self::getPackagePath($data) . DIRECTORY_SEPARATOR .$file);
                $dat = json_decode($dat,true);
                $name = isset($dat['name']) ? $dat['name'] : '???';
                if (!isset($data['PLUG']['plug_install_'.$name]) || $data['PLUG']['plug_install_'.$name] !== $name) continue;
                self::$selectedPluginFiles[] = $file;
            }
            closedir($handle);
        }

        return self::$selectedPluginFiles;
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $isUpdate = (isset($data['action']) && $data['action']=='update') ? true : false;
        $pluginFiles = self::getPackageDefinitions($data);
        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('packages','description',self::$langTemplate));
        $text .= Design::erstelleZeile($console, Installation::Get('packages','packageDetails',self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['PLUG']['details'], 'data[PLUG][details]', 'details', null, true), 'v_c');

        if (self::$onEvents['install']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Installation::Get('packages','installSelected',self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0],Installation::Get('packages','install',self::$langTemplate)), 'h');
        if (self::$onEvents['uninstall']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Installation::Get('packages','uninstallSelected',self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['uninstall']['event'][0],Installation::Get('main','uninstall')), 'h');

        if (isset($result[self::$onEvents['check']['name']]) && $result[self::$onEvents['check']['name']]!=null){
           $result =  $result[self::$onEvents['check']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $installedPlugins = $result['content'];

        // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen
        foreach ($pluginFiles as $plug){
            $dat = file_get_contents(self::getPackagePath($data) . DIRECTORY_SEPARATOR .$plug);
            $dat = json_decode($dat,true);
            $name = isset($dat['name']) ? $dat['name'] : '???';
            $version = isset($dat['version']) ? $dat['version'] : null;
            $voraussetzungen = isset($dat['requirements']) ? $dat['requirements'] : array();
            if (!is_array($voraussetzungen)) $voraussetzungen = array($voraussetzungen);

            $versionText = isset($dat['version']) ? ' v'.$dat['version'] : '';
            $text .= Design::erstelleZeile($console, $name.$versionText, 'e', ((self::$onEvents['install']['enabledInstall'] || self::$onEvents['uninstall']['enabledInstall']) ? Design::erstelleAuswahl($console, $data['PLUG']['plug_install_'.$name], 'data[PLUG][plug_install_'.$name.']', $name, null, true) : ''), 'v_c');

            $isInstalled=false;
            if (isset($installedPlugins)){
                foreach($installedPlugins as $instPlug){
                    if ($name == $instPlug['name']){
                        if (isset($instPlug['version'])){
                            $text .= Design::erstelleZeile($console, Installation::Get('packages','currentVersion',self::$langTemplate) , 'v', 'v'.$instPlug['version'] , 'v');
                        }
                        $isInstalled=true;
                        break;
                    }
                }
            }

            if (!$isInstalled)
                $text .= Design::erstelleZeile($console, Installation::Get('packages','currentVersion',self::$langTemplate) , 'v', '---' , 'v');

            $vorText = '';
            foreach ($voraussetzungen as $vor){
                $vorText .= "{$vor['name']} v{$vor['version']}, ";
            }
            if ($vorText==''){

            } else {
                $vorText = substr($vorText,0,-2);
                $text .= Design::erstelleZeile($console, Installation::Get('packages','requirements',self::$langTemplate) , 'v', $vorText , 'v');
            }


            if (!$isUpdate && isset($data['PLUG']['details']) && $data['PLUG']['details'] === 'details'){
                $file = self::getPackagePath($data) . DIRECTORY_SEPARATOR .$plug;
                $fileCount=0;
                $fileSize=0;
                $componentCount=0;
                if (file_exists($file) && is_readable($file)){
                    $input = file_get_contents($file);
                    $input = json_decode($input,true);
                    if ($input == null){
                        $fail = true;
                        break;
                    }
                    $fileList = array();
                    $fileListAddress = array();
                    $componentFiles = array();
                    self::gibPaketDateien($data, $input, $fileList, $fileListAddress, $componentFiles);
                    $fileCount=count($fileList);
                    foreach($fileList as $f){
                        if (is_readable($f)){
                            $fileSize += filesize($f);
                        }
                    }
                    $componentCount = count($componentFiles);
                }

                if ($componentCount>0){
                    $text .= Design::erstelleZeile($console, Installation::Get('packages','numberComponents',self::$langTemplate) , 'v', $componentCount , 'v');
                }
                if ($fileCount>0){
                    $text .= Design::erstelleZeile($console, Installation::Get('packages','numberFiles',self::$langTemplate) , 'v', $fileCount , 'v');
                }
                if ($fileSize>0){
                    $text .= Design::erstelleZeile($console, Installation::Get('packages','size',self::$langTemplate) , 'v', Design::formatBytes($fileSize) , 'v');
                }
            }
        }

        /*if ($installPlugins){
            if ($installPluginsResult !=null)
                foreach ($installPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Installation::Get('main','ok') : "<font color='red'>".Installation::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        if ($uninstallPlugins){
            if ($uninstallPluginsResult !=null)
                foreach ($uninstallPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Installation::Get('main','ok') : "<font color='red'>".Installation::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }

            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }*/

        echo Design::erstelleBlock($console, Installation::Get('packages','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function installCheckPackages($data, &$fail, &$errno, &$error, $checkPluginIsSelected = true)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();

        if (!$fail){
            $pluginFiles = array();
            try {
                $handle = opendir(self::getPackagePath($data));
            } catch (Exception $e) {
                // der Ordner konnte nicht zugegriffen werden
                Installation::log(array('text'=>self::getPackagePath($data).' existiert nicht oder es fehlt die Zugriffsberechtigung.','logLevel'=>LogLevel::ERROR));
                Installer::$messages[] = array('text'=>self::getPackagePath($data).' existiert nicht oder es fehlt die Zugriffsberechtigung.','type'=>'error');
                return $pluginFiles;
            }
        
            if ($handle !== false) {
                while (false !== ($file = readdir($handle))) {
                    if ($file=='.' || $file=='..') continue;
                    if (is_dir(self::getPackagePath($data) . DIRECTORY_SEPARATOR .$file)) continue;
                    $filePath = self::getPackagePath($data) . DIRECTORY_SEPARATOR .$file;
                    if (substr($filePath,-5)=='.json' && file_exists($filePath) && is_readable($filePath)){
                        $input = file_get_contents($filePath);
                        $input = json_decode($input,true);
                        if ($input == null){
                            //$fail = true;
                            //break;
                        }
                        if (isset($input['name']) && isset($data['PLUG']['plug_install_'.$input['name']]) && $data['PLUG']['plug_install_'.$input['name']] == $input['name']){
                            $res[] = $input;
                        } else {
                            // Name ist nicht gesetzt
                        }
                    }
                }
                closedir($handle);
            }
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function evaluierePlugin($data, $input)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $mainPath = $data['PL']['localPath'];
        $mainPath = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $mainPath);
        Installation::log(array('text'=>Installation::Get('packages','mainPath',self::$langTemplate,array('path'=>$mainPath))));

        if (isset($input['files'])){
            $files = $input['files'];
            if (!is_array($files)) $files = array($files);

            foreach ($files as $file){
                $type = 'local';
                $params = array();
                $exclude = array();
                $path = null;
                $sizePath = null;

                if (isset($file['path'])){
                    $path = $mainPath . DIRECTORY_SEPARATOR . $file['path'];
                    $path = rtrim($path,"\\/");
                    Installation::log(array('text'=>Installation::Get('packages','path',self::$langTemplate,array('path'=>$path))));
                    $sizePath = $path;
                }

                if (isset($file['type'])){
                    $type = $file['type'];
                }

                if (isset($file['params'])){
                    $params = $file['params'];
                }

                $virtual = (isset($file['virtual'])?$file['virtual']:false);

                if ($virtual){
                    // soll nicht ausgeführt werden, dieser Eintrag wird anderst verwendet
                }elseif ($type === 'git'){
                    // es handelt sich um ein git-Repository
                    Installation::log(array('text'=>Installation::Get('packages','typeGit',self::$langTemplate)));
                    $params['path'] = rtrim($params['path'],"\\/");
                    $location = $mainPath . DIRECTORY_SEPARATOR . $params['path'];
                    Einstellungen::generatepath($location);
                    $location = realpath($location);
                    Installation::log(array('text'=>Installation::Get('packages','location',self::$langTemplate,array('path'=>$location))));

                    if (!file_exists($location)){
                        Installation::log(array('text'=>Installation::Get('packages','noDirLocation',self::$langTemplate), 'logLevel'=>LogLevel::ERROR));
                    }

                    //$sizePath = $location;
                    $repo = (isset($params['URL'])?$params['URL']:null);
                    $branch = (isset($params['branch'])?$params['branch']:null);
                    $exclude[] = $location . DIRECTORY_SEPARATOR . '.git';


                    if (isset($file['exclude'])){
                        $tempExclude = $file['exclude'];
                        if (!is_array($exclude)) $exclude = array($exclude);
                        foreach($tempExclude as &$ex){
                            $ex = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $ex);
                            $ex = $location . DIRECTORY_SEPARATOR . $ex;
                        }
                        $exclude = array_merge($exclude, $tempExclude);
                    }

                    if (!file_exists($location."/.git")){
                        // es wurde lokal kein Repository gefunden, also muss es
                        // heruntergeladen werden
                        Installation::log(array('text'=>Installation::Get('packages','noLocalRepo',self::$langTemplate)));

                        // initialisieren
                        $pathOld = getcwd();
                        Installation::log(array('text'=>Installation::Get('packages','execClone',self::$langTemplate,array('cmd'=>'(git clone --single-branch --branch '.$branch.' '.$repo.' .) 2>&1'))));
                        if (@chdir($location)){
                            // klont das Repo
                            exec('(git clone --single-branch --branch '.$branch.' '.$repo.' .) 2>&1', $output, $return);
                            @chdir($pathOld);
                        } else {
                            $return = 1;
                            $output = Installation::Get('packages','errorOnChangeDir',self::$langTemplate, array('path'=>$location));
                        }

                        if ($return !== 0){
                            Installation::log(array('text'=>Installation::Get('packages','errorGitClone',self::$langTemplate, array('result'=>$output)), 'logLevel'=>LogLevel::ERROR));
                        }
                    } else {
                        // wenn das Repo bereits lokal existiert, soll sichergestellt werden,
                        // dass die URL korrekt ist
                        $pathOld = getcwd();
                        Installation::log(array('text'=>Installation::Get('packages','execRemote',self::$langTemplate,array('cmd'=>'(git remote set-url origin '.$repo.' ) 2>&1'))));
                        if (@chdir($location)){
                            // klont das Repo
                            exec('git remote set-url origin '.$repo.' ) 2>&1', $output, $return);
                            @chdir($pathOld);
                        } else {
                            $return = 1;
                            $output = Installation::Get('packages','errorOnChangeDir',self::$langTemplate, array('path'=>$location));
                        }

                        if ($return !== 0){
                            Installation::log(array('text'=>Installation::Get('packages','errorGitRemote',self::$langTemplate, array('result'=>$output)), 'logLevel'=>LogLevel::ERROR));
                        }
                    }

                    $pathOld = getcwd();
                    if (@chdir($location)){
                        // aktualisiert das Repo
                        exec('(git fetch) 2>&1', $output, $return);
                        exec('(git pull) 2>&1', $output2, $return2);
                        @chdir($pathOld);
                    } else {
                        $return = 1;
                        $output = Installation::Get('packages','errorOnChangeDir',self::$langTemplate, array('path'=>$location));
                    }

                    if ($return === 0){
                        if ($return2 === 0){
                            $found = Installation::read_all_files($location, $exclude);
                            if (realpath($location . DIRECTORY_SEPARATOR) === realpath($path)){
                                // kein Verschieben notwendig
                                Installation::log(array('text'=>Installation::Get('packages','noMovingRequired',self::$langTemplate)));
                            } else {
                                // verschiebe die Dateien von $location nach $path
                                Installation::log(array('text'=>Installation::Get('packages','copyFiles',self::$langTemplate)));
                                foreach ($found['files'] as $temp){
                                    $file = substr($temp,strlen($location)+1);
                                    $file = $path . DIRECTORY_SEPARATOR . $file;
                                    Einstellungen::generatepath(dirname($file));

                                    if (!file_exists(dirname($file))){
                                        Installation::log(array('text'=>Installation::Get('packages','noDirFile',self::$langTemplate,array('path'=>dirname($file))), 'logLevel'=>LogLevel::ERROR));
                                    }

                                    Installation::log(array('text'=>Installation::Get('packages','copyFile',self::$langTemplate,array('from'=>$temp,'to'=>$file))));
                                    $res = @copy($temp, $file);
                                }
                            }
                        } else {
                            Installation::log(array('text'=>Installation::Get('packages','errorGitPull',self::$langTemplate, array('result'=>$output2)), 'logLevel'=>LogLevel::ERROR));
                        }
                    } else {
                        Installation::log(array('text'=>Installation::Get('packages','errorGitFetch',self::$langTemplate, array('result'=>$output)), 'logLevel'=>LogLevel::ERROR));
                    }


                } elseif ($type === 'local'){
                    $sizePath = realpath($sizePath);

                    Installation::log(array('text'=>Installation::Get('packages','typeLocal',self::$langTemplate)));
                    if (isset($path) && isset($params['relLocalResource'])){
                        file_put_contents($sizePath, file_get_contents($mainPath . DIRECTORY_SEPARATOR . $params['relLocalResource']));
                    }

                    if (isset($path) && isset($params['urlResource'])){
                        file_put_contents($sizePath, file_get_contents($params['urlResource']));
                    }
                }
            }
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function gibPaketInhalt($data, $file)
    {
        $file = self::getPackagePath($data) . DIRECTORY_SEPARATOR .$file;
        if (file_exists($file) && is_readable($file)){
            $input = file_get_contents($file);
            $input = json_decode($input,true);
            return $input;
        }
        return null;
    }

    public static function gibPaketEintraegeNachTyp($input, $type='local', &$list)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $list = array();
        if (isset($input['files'])){
            $files = $input['files'];
            if (!is_array($files)) $files = array($files);

            foreach ($files as $file){
                if (isset($file['type']) && $file['type'] === $type){
                    $list[] = $file;
                }
            }
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }


    public static function gibPaketDateien($data, $input, &$fileList, &$fileListAddress, &$componentFiles)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $mainPath = $data['PL']['localPath'];
        $mainPath = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $mainPath);
        Installation::log(array('text'=>Installation::Get('packages','mainPath',self::$langTemplate,array('path'=>$mainPath))));

        if (isset($input['files'])){
            $files = $input['files'];
            if (!is_array($files)) $files = array($files);

            foreach ($files as $file){
                $type = 'local';
                $params = array();
                $exclude = array();
                $path = null;
                $sizePath = null;

                if (isset($file['path'])){
                    $path = $mainPath . DIRECTORY_SEPARATOR . $file['path'];
                    $path = rtrim($path,"\\/");
                    Installation::log(array('text'=>Installation::Get('packages','path',self::$langTemplate,array('path'=>$path))));
                    $sizePath = $path;
                }

                if (isset($file['type'])){
                    $type = $file['type'];
                }

                if (isset($file['params'])){
                    $params = $file['params'];
                }

                $virtual = (isset($file['virtual'])?$file['virtual']:false);

                if ($virtual){
                    // soll nicht ausgeführt werden
                }elseif ($type === 'git'){
                    Installation::log(array('text'=>Installation::Get('packages','typeGit',self::$langTemplate)));
                    $params['path'] = rtrim($params['path'],"\\/");
                    $location = $mainPath . DIRECTORY_SEPARATOR . $params['path'];
                    Einstellungen::generatepath($location);
                    $location = realpath($location);
                    Installation::log(array('text'=>Installation::Get('packages','location',self::$langTemplate,array('path'=>$location))));

                    if (!file_exists($location)){
                        Installation::log(array('text'=>Installation::Get('packages','noDirLocation',self::$langTemplate), 'logLevel'=>LogLevel::ERROR));
                    }

                    //$sizePath = $location;
                    $exclude[] = $location . DIRECTORY_SEPARATOR . '.git';

                    if (isset($file['exclude'])){
                        $tempExclude = $file['exclude'];
                        if (!is_array($exclude)) $exclude = array($exclude);
                        foreach($tempExclude as &$ex){
                            $ex = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $ex);
                            $ex = $location . DIRECTORY_SEPARATOR . $ex;
                        }
                        $exclude = array_merge($exclude, $tempExclude);
                    }

                    if (!file_exists($location."/.git")){
                        // initialisieren
                        continue;
                    }

                    $found = Installation::read_all_files($location, $exclude);

                    foreach($found['files'] as $temp){
                        $file = substr($temp,strlen($location)+1);
                        $file = $path . DIRECTORY_SEPARATOR . $file;
                        $fileList[] = $file;
                        $fileListAddress[] = substr($file,strlen($mainPath)+1);
                    }

                } elseif ($type === 'local'){
                    if (isset($path) && isset($file['exclude'])){
                        $exclude = $file['exclude'];
                        if (!is_array($exclude)) $exclude = array($exclude);
                        foreach($exclude as &$ex){
                            $ex = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $ex);
                            $ex = $path . DIRECTORY_SEPARATOR . $ex;
                        }
                    }


                    if (isset($sizePath)){
                        $sizePath = realpath($sizePath);

                        if (is_dir($sizePath)){
                            $found = Installation::read_all_files($sizePath, $exclude);
                            foreach ($found['files'] as $temp){
                                $fileList[] = $temp;
                                $fileListAddress[] = substr($temp,strlen($mainPath)+1);
                            }
                        } else {
                            $fileList[] = $sizePath;
                            $fileListAddress[] = substr($sizePath,strlen($mainPath)+1);
                        }
                    }
                }
            }
        }

        if (isset($input['components'])){
            $files = $input['components'];
            if (!is_array($files)) $files = array($files);

            foreach ($files as $file){
                if (isset($file['conf'])){
                    $file['conf'] = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $file['conf']);

                    if (!file_exists($mainPath . DIRECTORY_SEPARATOR . $file['conf']) || !is_readable($mainPath . DIRECTORY_SEPARATOR . $file['conf'])) continue;
                    $componentFiles[] = $mainPath . DIRECTORY_SEPARATOR . $file['conf'];
                    $definition = file_get_contents($mainPath . DIRECTORY_SEPARATOR . $file['conf']);
                    $definition = json_decode($definition,true);
                    $comPath = dirname($mainPath . DIRECTORY_SEPARATOR . $file['conf']);

                    $fileList[] = $mainPath . DIRECTORY_SEPARATOR . $file['conf'];
                    $fileListAddress[] = $file['conf'];

                    if (isset($definition['files'])){
                        if (!is_array($definition['files'])) $definition['files'] = array($definition['files']);

                        foreach ($definition['files'] as $paths){
                            if (!isset($paths['path'])) continue;

                            $paths['path'] = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $paths['path']);
                            if (is_dir($comPath . DIRECTORY_SEPARATOR . $paths['path'])){
                                $found = Installation::read_all_files($comPath . DIRECTORY_SEPARATOR . $paths['path']);
                                foreach ($found['files'] as $temp){
                                    $fileList[] = $temp;
                                    $fileListAddress[] = substr($temp,strlen($mainPath)+1);
                                }
                            } else {
                                $fileList[] = $comPath . DIRECTORY_SEPARATOR . $paths['path'];
                                $fileListAddress[] = dirname($file['conf']) . DIRECTORY_SEPARATOR . $paths['path'];
                            }
                        }
                    }
                }
            }
        }

        $newFileListAddress = array();
        foreach ($fileListAddress as $key => $a){
            if (!isset($newFileListAddress[$a])){
                $newFileListAddress[$a] = $fileList[$key];
            }
        }

        $fileListAddress = array();
        $fileList = array();
        foreach ($newFileListAddress as $key => $a){
            $fileListAddress[] = $key;
            $fileList[] = $a;
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function installValidateFiles($data, &$fail, &$errno, &$error)
    {
        return array("content"=>'');
    }

    public static function installInstallPackages($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $pluginFiles = self::getSelectedPackageDefinitions($data);
        $res = array();

        if (!$fail){
            $fileList = array();
            $fileListAddress = array();
            $componentFiles = array();

            foreach ($pluginFiles as $plug){
                $file = self::getPackagePath($data) . DIRECTORY_SEPARATOR .$plug;
                if (substr($file,-5)=='.json' && file_exists($file) && is_readable($file)){
                    $dat = file_get_contents($file);
                    $dat = json_decode($dat,true);
                    if (!isset($dat['name'])) continue;
                    self::evaluierePlugin($data, $dat);
                    self::gibPaketDateien($data, $dat, $fileList, $fileListAddress, $componentFiles);
                }
            }

            // Dateien übertragen
            //Zugang::SendeDateien($fileList,$fileListAddress,$data);
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function installUninstallPackages($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();

        if (!$fail){
            // Ausfüllen
            // Ausfüllen
            // Ausfüllen
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion Paketverwaltung