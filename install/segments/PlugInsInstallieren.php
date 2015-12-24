<?php
#region PlugInsInstallieren
class PlugInsInstallieren
{
    private static $initialized=false;
    public static $name = 'initPlugins';
    public static $installed = false;
    public static $page = 6;
    public static $rank = 100;
    public static $enabledShow = true;

    public static $onEvents = array(
                                    'check'=>array(
                                        'name'=>'checkPlugins',
                                        'event'=>array('page'),
                                        'procedure'=>'installCheckPlugins'
                                        ),
                                    'install'=>array(
                                        'name'=>'installPlugins',
                                        'event'=>array('actionInstallPlugins'),
                                        'procedure'=>'installInstallPlugins',
                                        'enabledInstall'=>true
                                        ),
                                    'uninstall'=>array(
                                        'name'=>'uninstallPlugins',
                                        'event'=>array('actionUninstallPlugins'),
                                        'procedure'=>'installUninstallPlugins',
                                        'enabledInstall'=>true
                                        ),
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

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $def = self::getDefaults();

        $text = '';
        foreach($def as $defName => $defVar){
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG'][$defName], $defVar[0], $defVar[1], true);
        }
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }
    
    private static function getPluginFiles()
    {
        $pluginFiles = array();
        if ($handle = @opendir(dirname(__FILE__) . '/../../Plugins')) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file,-5)!='.json' || $file=='.' || $file=='..') continue;
                if (is_dir(dirname(__FILE__) . '/../../Plugins/'.$file)) continue;
                $pluginFiles[] = $file;
            }
            closedir($handle);
        }
        return $pluginFiles;
    }

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $pluginFiles = self::getPluginFiles();
        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('packages','description'));

        if (self::$onEvents['install']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Language::Get('packages','installSelected'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0],Language::Get('main','install')), 'h');
        if (self::$onEvents['uninstall']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Language::Get('packages','uninstallSelected'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['uninstall']['event'][0],Language::Get('main','uninstall')), 'h');

        if (self::$onEvents['validateFiles']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Language::Get('packages','validateFilesDesc'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['validateFiles']['event'][0],Language::Get('packages','validateFiles')), 'h');

        $validateFiles=false;
        if (isset($result[self::$onEvents['validateFiles']['name']])){
            $validateFiles=true;
        }

        if (isset($result[self::$onEvents['check']['name']]) && $result[self::$onEvents['check']['name']]!=null){
           $result =  $result[self::$onEvents['check']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $installedPlugins = $result['content'];

        // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen
        foreach ($pluginFiles as $plug){
            $dat = file_get_contents(dirname(__FILE__) . '/../../Plugins/'.$plug);
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
                            $text .= Design::erstelleZeile($console, Language::Get('packages','currentVersion') , 'v', 'v'.$instPlug['version'] , 'v');
                        }
                        $isInstalled=true;
                        break;
                    }
                }
            }

            if (!$isInstalled)
                $text .= Design::erstelleZeile($console, Language::Get('packages','currentVersion') , 'v', '---' , 'v');

            $vorText = '';
            foreach ($voraussetzungen as $vor){
                $vorText .= "{$vor['name']} v{$vor['version']}, ";
            }
            if ($vorText==''){
                
            } else {
                $vorText = substr($vorText,0,-2);
                $text .= Design::erstelleZeile($console, Language::Get('packages','requirements') , 'v', $vorText , 'v');
            }

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
                $fileCount=count($fileList);
                foreach($fileList as $f){
                    if (is_readable($f)){
                        $fileSize += filesize($f);
                        if ($validateFiles){
                            if ($fileSize>0 && strtolower(substr($f,-5))==='.json'){
                                // validiere die json Datei
                                $cont = file_get_contents($f);
                                if (trim($cont) != ''){
                                    $val = @json_decode(file_get_contents($f));
                                    if ($val===null){
                                        $text .= Design::erstelleZeileShort($console, realpath($f) , 'break v', Language::Get('packages','jsonInvalid'), 'v error_light break');
                                    }
                                }
                            }

                            if ($fileSize>0 && strtolower(substr($f,-4))==='.php'){
                                // validiere die php Datei
                                $output=null;
                                $result=null;
                                exec('(php -l -d error_reporting=E_ALL -d display_errors=on -d log_errors=off -f '.realpath($f).') 2>&1',$output,$result);
                                if ($result!=0){
                                    $text .= Design::erstelleZeileShort($console, realpath($f), 'break v', implode('<br>',$output), 'v error_light break');
                                }
                            }
                        }
                    }
                }
                $componentCount = count($componentFiles);
            }

            if ($componentCount>0){
                $text .= Design::erstelleZeile($console, Language::Get('packages','numberComponents') , 'v', $componentCount , 'v');
            }
            if ($fileCount>0){
                $text .= Design::erstelleZeile($console, Language::Get('packages','numberFiles') , 'v', $fileCount , 'v');
            }
            if ($fileSize>0){
                $text .= Design::erstelleZeile($console, Language::Get('packages','size') , 'v', Design::formatBytes($fileSize) , 'v');
            }
        }

        /*if ($installPlugins){
            if ($installPluginsResult !=null)
                foreach ($installPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Language::Get('main','ok') : "<font color='red'>".Language::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        if ($uninstallPlugins){
            if ($uninstallPluginsResult !=null)
                foreach ($uninstallPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Language::Get('main','ok') : "<font color='red'>".Language::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }

            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }*/

        echo Design::erstelleBlock($console, Language::Get('packages','title'), $text);

        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }

    public static function installCheckPlugins($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $res = array();

        if (!$fail){
            $mainPath = dirname(__FILE__) . '/../..';
            $pluginFiles = array();
            if ($handle = @opendir(dirname(__FILE__) . '/../../Plugins')) {
                while (false !== ($file = readdir($handle))) {
                    if ($file=='.' || $file=='..') continue;
                    if (is_dir(dirname(__FILE__) . '/../../Plugins/'.$file)) continue;
                    $filePath = dirname(__FILE__) . '/../../Plugins/'.$file;
                    if (substr($filePath,-5)=='.json' && file_exists($filePath) && is_readable($filePath)){
                        $input = file_get_contents($filePath);
                        $input = json_decode($input,true);
                        if ($input == null){
                            //$fail = true;
                            //break;
                        }
                        $res[] = $input;
                    }
                }
                closedir($handle);
            }
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $res;
    }

    public static function gibPluginDateien($input, &$fileList, &$fileListAddress, &$componentFiles)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $mainPath = dirname(__FILE__) . '/../..';
        if (isset($input['files'])){
            $files = $input['files'];
            if (!is_array($files)) $files = array($files);

            foreach ($files as $file){
                if (isset($file['path'])){
                    if (is_dir($mainPath . '/' . $file['path'])){
                        $found = Installation::read_all_files($mainPath . '/' . $file['path']);
                        foreach ($found['files'] as $temp){
                            $fileList[] = $temp;
                            $fileListAddress[] = substr($temp,strlen($mainPath)+1);
                        }
                    } else {
                        $fileList[] = $mainPath . '/' . $file['path'];
                        $fileListAddress[] = $file['path'];
                    }
                }
            }
        }

        if (isset($input['components'])){
            $files = $input['components'];
            if (!is_array($files)) $files = array($files);

            foreach ($files as $file){
                if (isset($file['conf'])){
                    if (!file_exists($mainPath . '/' . $file['conf']) || !is_readable($mainPath . '/' . $file['conf'])) continue;
                    $componentFiles[] = $mainPath . '/' . $file['conf'];
                    $definition = file_get_contents($mainPath . '/' . $file['conf']);
                    $definition = json_decode($definition,true);
                    $comPath = dirname($mainPath . '/' . $file['conf']);

                    $fileList[] = $mainPath . '/' . $file['conf'];
                    $fileListAddress[] = $file['conf'];

                    if (isset($definition['files'])){
                        if (!is_array($definition['files'])) $definition['files'] = array($definition['files']);

                        foreach ($definition['files'] as $paths){
                            if (!isset($paths['path'])) continue;

                            if (is_dir($comPath . '/' . $paths['path'])){
                                $found = Installation::read_all_files($comPath . '/' . $paths['path']);
                                foreach ($found['files'] as $temp){
                                    $fileList[] = $temp;
                                    $fileListAddress[] = substr($temp,strlen($mainPath)+1);
                                }
                            } else {
                                $fileList[] = $comPath . '/' . $paths['path'];
                                $fileListAddress[] = dirname($file['conf']) . '/' . $paths['path'];
                            }
                        }
                    }
                }
            }
        }

        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function installValidateFiles($data, &$fail, &$errno, &$error)
    {
        return array("content"=>'');
    }

    public static function installInstallPlugins($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $res = array();

        if (!$fail){
            $mainPath = dirname(__FILE__) . '/../..';
            $fileList = array();
            $fileListAddress = array();
            $componentFiles = array();

            foreach ($data['PLUG'] as $plugs){
                $file = dirname(__FILE__) . '/../../Plugins/'.$plugs;

                if (substr($file,-5)=='.json' && file_exists($file) && is_readable($file)){
                    $input = file_get_contents($file);
                    $input = json_decode($input,true);
                    if ($input == null){
                        $fail = true;
                        break;
                    }

                    // Dateiliste zusammentragen
                    Installation::gibPluginDateien($input, $fileList, $fileListAddress, $componentFiles);
                    $fileList[] = $mainPath.'/install/config/'.$data['SV']['name'].'.ini';
                    $fileListAddress[] = 'install/config/'.$data['SV']['name'].'.ini';
                }
            }

            // Dateien übertragen
            Zugang::SendeDateien($fileList,$fileListAddress,$data);
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $res;
    }

    public static function installUninstallPlugins($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $res = array();

        if (!$fail){
            $mainPath = dirname(__FILE__) . '/../..';
            foreach ($data['PLUG'] as $plugs){
                $file = dirname(__FILE__) . '/../../Plugins/'.$plugs;

                if (substr($file,-5)=='.json' && file_exists($file) && is_readable($file)){
                    $input = file_get_contents($file);
                    $input = json_decode($input,true);
                    if ($input == null){
                        $fail = true;
                        break;
                    }

                    // Dateiliste zusammentragen
                    $fileList = array();
                    $fileListAddress = array();
                    $componentFiles = array();
                    Installation::gibPluginDateien($input, $fileList, $fileListAddress, $componentFiles);
                    $fileList[] = $mainPath.'/install/config/'.$data['SV']['name'].'.ini';
                    $fileListAddress[] = 'install/config/'.$data['SV']['name'].'.ini';

                    // Dateien entfernen
                    Zugang::EntferneDateien($fileList,$fileListAddress,$data);
                }
            }
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $res;
    }
}
#endregion PlugInsInstallieren