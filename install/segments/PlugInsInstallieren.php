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
                                        'event'=>array('actionInstallPlugins','page'),
                                        'procedure'=>'installInstallPlugins',
                                        'enabledInstall'=>false
                                        ),
                                    'uninstall'=>array(
                                        'name'=>'uninstallPlugins',
                                        'event'=>array('page'),
                                        'procedure'=>'installUninstallPlugins',
                                        'enabledInstall'=>false
                                        )
                                    );
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG']['plug_install_CORE'], 'data[PLUG][plug_install_CORE]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG']['plug_install_OSTEPU-UI'], 'data[PLUG][plug_install_OSTEPU-UI]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG']['plug_install_OSTEPU-DB'], 'data[PLUG][plug_install_OSTEPU-DB]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG']['plug_install_OSTEPU-FS'], 'data[PLUG][plug_install_OSTEPU-FS]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG']['plug_install_OSTEPU-LOGIC'], 'data[PLUG][plug_install_OSTEPU-LOGIC]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PLUG']['plug_install_INSTALL'], 'data[PLUG][plug_install_INSTALL]', '_', true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
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
        $text='';
        $text .= Design::erstelleBeschreibung($console,Sprachen::Get('packages','description'));
        
        if (self::$onEvents['install']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Sprachen::Get('packages','installSelected'), 'e', '', 'v', Design::erstelleSubmitButton(self::$installPlugins,Sprachen::Get('main','install')), 'h');
        if (self::$onEvents['uninstall']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Sprachen::Get('packages','uninstallSelected'), 'e', '', 'v', Design::erstelleSubmitButton(self::$uninstallPlugins,Sprachen::Get('main','uninstall')), 'h');
        
        if (isset($result[self::$onEvents['check']['name']]) && $result[self::$onEvents['check']['name']]!=null){
           $result =  $result[self::$onEvents['check']['name']];
        } else 
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $installedPlugins = $result['content'];
        
        // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen       
        foreach ($pluginFiles as $plug){
            $dat = file_get_contents(dirname(__FILE__) . '/../../Plugins/'.$plug);
            $dat = json_decode($dat,true);
            $name = $dat['name'];
            $version = $dat['version'];
            $voraussetzungen = $dat['requirements'];
            if (!is_array($voraussetzungen)) $voraussetzungen = array($voraussetzungen);

            $text .= Design::erstelleZeile($console, "{$name} v{$dat['version']}", 'e', ((self::$onEvents['install']['enabledInstall'] || self::$onEvents['uninstall']['enabledInstall']) ? Design::erstelleAuswahl($console, $data['PLUG']['plug_install_'.$name], 'data[PLUG][plug_install_'.$name.']', $plug, null, true) : ''), 'v');  
            
            $isInstalled=false;
            foreach($installedPlugins as $instPlug){
                if ($name == $instPlug['name']){
                    if (isset($instPlug['version'])){
                        $text .= Design::erstelleZeile($console, Sprachen::Get('packages','currentVersion') , 'v', 'v'.$instPlug['version'] , 'v'); 
                    } else 
                        $text .= Design::erstelleZeile($console, Sprachen::Get('packages','currentVersion') , 'v', '???' , 'v');
                    $isInstalled=true;
                    break;
                }
            }
            
            if (!$isInstalled)
                $text .= Design::erstelleZeile($console, Sprachen::Get('packages','currentVersion') , 'v', '---' , 'v');
            
            $vorText = '';
            foreach ($voraussetzungen as $vor){
                $vorText .= "{$vor['name']} v{$vor['version']}, ";
            }
            if ($vorText==''){
                $vorText = '---';
            } else 
                $vorText = substr($vorText,0,-2);
            
            $text .= Design::erstelleZeile($console, Sprachen::Get('packages','requirements') , 'v', $vorText , 'v');
            
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
                    if (is_readable($f))
                        $fileSize += filesize($f);
                }
                $componentCount = count($componentFiles);
            }
              
            $text .= Design::erstelleZeile($console, Sprachen::Get('packages','numberComponents') , 'v', $componentCount , 'v');        
            $text .= Design::erstelleZeile($console, Sprachen::Get('packages','numberFiles') , 'v', $fileCount , 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('packages','size') , 'v', Design::formatBytes($fileSize) , 'v');
        }
        
        /*if ($installPlugins){
            if ($installPluginsResult !=null)
                foreach ($installPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }
        
        if ($uninstallPlugins){
            if ($uninstallPluginsResult !=null)
                foreach ($uninstallPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
                
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }*/
        
        echo Design::erstelleBlock($console, Sprachen::Get('packages','title'), $text);
        return null;
    }
    
    public static function installCheckPlugins($data, &$fail, &$errno, &$error)
    {
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
        return $res;
    }
    
    public static function gibPluginDateien($input, &$fileList, &$fileListAddress, &$componentFiles)
    {
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
    }
    
    public static function installInstallPlugins($data, &$fail, &$errno, &$error)
    {
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
        
        return $res;
    }
    
    public static function installUninstallPlugins($data, &$fail, &$errno, &$error)
    {
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
        
        return $res;
    }
}
#endregion PlugInsInstallieren