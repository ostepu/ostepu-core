<?php
#region SyntaxPruefen
class SyntaxPruefen
{
    private static $initialized=false;
    public static $name = 'validateFiles';
    public static $installed = false;
    public static $page = 8;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate='SyntaxPruefen';

    public static $onEvents = array(
                                    'validateFiles'=>array(
                                        'name'=>'validateFiles',
                                        'event'=>array('actionValidateFiles'),
                                        'procedure'=>'installValidateFiles',
                                        'enabledInstall'=>true
                                        )
                                    );

    public static function getDefaults()
    {
        $res = array();
        $pluginFiles = self::getPluginFiles();
        foreach($pluginFiles as $plug){
            $filePath = dirname(__FILE__) . '/../../Plugins/'.$plug;
            if (is_readable($filePath)){
                $input = file_get_contents($filePath);
                $input = json_decode($input,true);
                if ($input == null){
                    // Fehler beim dekodieren
                } else  {
                    if (isset($input['name'])){
                        $name = $input['name'];
                        $res['plug_install_'.$name] = array('data[PLUG][plug_install_'.$name.']', $name);
                    }
                }
            }
        }
        return $res;
    }

    public static function checkExecutability($data)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array(['name'=>'php','exec'=>'php -v','desc'=>'php -v']);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        $def = self::getDefaults();

        $text = '';
        foreach($def as $defName => $defVar){
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG'][$defName], $defVar[0], $defVar[1], true);
        }
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    private static $pluginFiles=null;
    private static function getPluginFiles()
    {
        if(self::$pluginFiles !== null){
            return self::$pluginFiles;
        }

        self::$pluginFiles = array();
        if ($handle = @opendir(dirname(__FILE__) . '/../../Plugins')) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file,-5)!='.json' || $file=='.' || $file=='..') continue;
                if (is_dir(dirname(__FILE__) . '/../../Plugins/'.$file)) continue;
                self::$pluginFiles[] = $file;
            }
            closedir($handle);
        }

        return self::$pluginFiles;
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $pluginFiles = self::getPluginFiles();
        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('validation','description',self::$langTemplate));

        if (self::$onEvents['validateFiles']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Installation::Get('validation','validateFilesDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['validateFiles']['event'][0],Installation::Get('validation','validateFiles',self::$langTemplate)), 'h');

        $validateFiles=false;
        if (isset($result[self::$onEvents['validateFiles']['name']])){
            $validateFiles=true;
        }

        if ($validateFiles){
            $res = $result[self::$onEvents['validateFiles']['name']]['content'];
            foreach($res['plugins'] as $plug){
                $text .= Design::erstelleZeile($console, $plug['nameText'], 'e');
                $text .= Design::erstelleZeile($console, Installation::Get('validation','files',self::$langTemplate), 'e', $plug['filesAmount'] ,'v');
                foreach($plug['results'] as $file){
                    if ($file[1] === 'json'){
                        $text .= Design::erstelleZeile($console, $file[0] , 'break v', Installation::Get('validation','jsonInvalid',self::$langTemplate), 'v error_light break');
                    } elseif ($file[1] === 'php'){
                        $text .= Design::erstelleZeile($console, $file[0], 'break v', implode('<br>',$file[2]), 'v error_light break');
                    } elseif ($file[1] === 'ini'){
                        $text .= Design::erstelleZeile($console, $file[0] , 'break v', Installation::Get('validation','iniInvalid',self::$langTemplate), 'v error_light break');
                    }
                }
            }
        }

        echo Design::erstelleBlock($console, Installation::Get('validation','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }


    public static function gibPluginDateien($input, &$fileList, &$fileListAddress, &$componentFiles)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $mainPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../..');
        $mainPath = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $mainPath);

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
                    $path = realpath($mainPath . DIRECTORY_SEPARATOR . $file['path']);
                    $sizePath = $path;
                }

                if (isset($file['type'])){
                    $type = $file['type'];
                }

                if (isset($file['params'])){
                    $params = $file['params'];
                }

                if ($type === 'git'){
                    $params['path'] = rtrim($params['path'],"\\/");
                    $location = $mainPath . DIRECTORY_SEPARATOR . $params['path'];
                    Einstellungen::generatepath($location);
                    $location = realpath($location);
                    //$sizePath = $location;
                    $repo = $params['URL'];
                    $branch = $params['branch'];
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
                    if ($location . DIRECTORY_SEPARATOR === $path){
                        // kein Verschieben notwendig
                    } else {
                        // verschiebe die Dateien von $location nach $path
                        foreach ($found['files'] as $temp){
                            $file = substr($temp,strlen($location)+1);
                            $file = $path . DIRECTORY_SEPARATOR . $file;
                            Einstellungen::generatepath(dirname($file));
                            $res = @copy($temp, $file);
                        }
                    }

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
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();
        $pluginFiles = self::getPluginFiles();
        $res['plugins'] = array();

        // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen
        foreach ($pluginFiles as $plug){
            $res['plugins'][$plug] = array();
            $res['plugins'][$plug]['results'] = array();

            $dat = file_get_contents(dirname(__FILE__) . '/../../Plugins/'.$plug);
            $dat = json_decode($dat,true);
            $name = isset($dat['name']) ? $dat['name'] : '???';
            $version = isset($dat['version']) ? $dat['version'] : null;

            $versionText = isset($version) ? ' v'.$version : '';
            $res['plugins'][$plug]['nameText'] = $name.$versionText;

            $file = dirname(__FILE__) . '/../../Plugins/'.$plug;
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
                self::gibPluginDateien($input, $fileList, $fileListAddress, $componentFiles);
                unset($fileListAddress);
                unset($componentFiles);
                $res['plugins'][$plug]['filesAmount'] = count($fileList);

                foreach($fileList as $f){
                    if (is_readable($f)){
                        $fileSize = filesize($f);
                        if ($fileSize>0 && strtolower(substr($f,-5))==='.json'){
                            // validiere die json Datei
                            $cont = file_get_contents($f);
                            if (trim($cont) != ''){
                                $val = @json_decode(file_get_contents($f));
                                if ($val===null){
                                    $res['plugins'][$plug]['results'][] = array(realpath($f),'json');
                                }
                            }
                        }

                        if ($fileSize>0 && strtolower(substr($f,-4))==='.php'){
                            // validiere die php Datei
                            $output=null;
                            $result=null;
                            exec('(php -l -d error_reporting=E_ALL -d display_errors=on -d log_errors=off -f '.realpath($f).') 2>&1',$output,$result);
                            if ($result!=0){
                                    $res['plugins'][$plug]['results'][] = array(realpath($f),'php',$output);
                            }
                        }

                        if ($fileSize>0 && strtolower(substr($f,-4))==='.ini'){
                            // validiere die ini Datei
                            $cont = file_get_contents($f);
                            $val = @parse_ini_file($f);
                            if ($val===false){
                                    $res['plugins'][$plug]['results'][] = array(realpath($f),'ini');
                            }
                        }
                    }
                }
            }
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion SyntaxPruefen