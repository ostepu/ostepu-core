<?php
#region PlugInsInstallieren
if (!$simple) {
    $pluginFiles = array();
    if ($handle = @opendir(dirname(__FILE__) . '/../../Plugins')) {
        while (false !== ($file = readdir($handle))) {
            if (substr($file,-5)!='.json' || $file=='.' || $file=='..') continue;
            if (is_dir(dirname(__FILE__) . '/../../Plugins/'.$file)) continue;
            $pluginFiles[] = $file;
        }
        closedir($handle);
    }
    
    if ($selected_menu === 6){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('packages','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','installSelected'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallPlugins',Sprachen::Get('main','install')), 'h');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','uninstallSelected'), 'e', '', 'v', Design::erstelleSubmitButton('actionUninstallPlugins',Sprachen::Get('main','uninstall')), 'h');
        
        // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen       
        foreach ($pluginFiles as $plug){
            $dat = file_get_contents(dirname(__FILE__) . '/../../Plugins/'.$plug);
            $dat = json_decode($dat,true);
            $name = $dat['name'];
            $version = $dat['version'];
            $voraussetzungen = $dat['requirements'];
            if (!is_array($voraussetzungen)) $voraussetzungen = array($voraussetzungen);

            $text .= Design::erstelleZeile($simple, "{$name} v{$dat['version']}", 'e', Design::erstelleAuswahl($simple, $data['PLUG']['plug_install_'.$name], 'data[PLUG][plug_install_'.$name.']', $plug, null, true), 'v');
            
            $isInstalled=false;
            foreach($installedPlugins as $instPlug){
                if ($name == $instPlug['name']){
                    if (isset($instPlug['version'])){
                        $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','currentVersion') , 'v', 'v'.$instPlug['version'] , 'v'); 
                    } else 
                        $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','currentVersion') , 'v', '???' , 'v');
                    $isInstalled=true;
                    break;
                }
            }
            
            if (!$isInstalled)
                $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','currentVersion') , 'v', '---' , 'v');
            
            $vorText = '';
            foreach ($voraussetzungen as $vor){
                $vorText .= "{$vor['name']} v{$vor['version']}, ";
            }
            if ($vorText==''){
                $vorText = '---';
            } else 
                $vorText = substr($vorText,0,-2);
            
            $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','requirements') , 'v', $vorText , 'v');
            
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
                Installation::gibPluginDateien($input, $fileList, $fileListAddress, $componentFiles);
                $fileCount=count($fileList);
                foreach($fileList as $f){
                    if (is_readable($f))
                        $fileSize += filesize($f);
                }
                $componentCount = count($componentFiles);
            }
              
            $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','numberComponents') , 'v', $componentCount , 'v');        
            $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','numberFiles') , 'v', $fileCount , 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('packages','size') , 'v', Design::formatBytes($fileSize) , 'v');
        }
        
        if ($installPlugins){
            if ($installPluginsResult !=null)
                foreach ($installPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
        }
        
        if ($uninstallPlugins){
            if ($uninstallPluginsResult !=null)
                foreach ($uninstallPluginsResult as $component){
                   // $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
                
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
        }
        
        echo Design::erstelleBlock($simple, Sprachen::Get('packages','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PLUG']['plug_install_CORE'], 'data[PLUG][plug_install_CORE]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PLUG']['plug_install_OSTEPU-UI'], 'data[PLUG][plug_install_OSTEPU-UI]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PLUG']['plug_install_OSTEPU-DB'], 'data[PLUG][plug_install_OSTEPU-DB]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PLUG']['plug_install_OSTEPU-FS'], 'data[PLUG][plug_install_OSTEPU-FS]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PLUG']['plug_install_OSTEPU-LOGIC'], 'data[PLUG][plug_install_OSTEPU-LOGIC]', '_', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PLUG']['plug_install_INSTALL'], 'data[PLUG][plug_install_INSTALL]', '_', true);
        echo $text;
    }
}
#endregion PlugInsInstallieren
?>