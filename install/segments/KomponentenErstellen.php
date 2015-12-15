<?php
#region KomponentenErstellen
class KomponentenErstellen
{
    public static $name = 'componentDefs';
    public static $installed = false;
    public static $page = 3;
    public static $rank = 50;
    public static $enabledShow = true;
    public static $enabledInstall = true;

    public static $onEvents = array('install'=>array('name'=>'componentDefs','event'=>array('actionInstallComponentDefs','install', 'update')));

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $text='';

        if (!$console)
            $text .= Design::erstelleBeschreibung($console,Language::Get('generateComponents','description'));

        if (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']]!=null){
           $result =  $result[self::$onEvents['install']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];

        if (!$console)
            $text .= Design::erstelleZeile($console, Language::Get('generateComponents','generateComponents'), 'e', '','v',Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');

        if (self::$installed){
            if (isset($content['components'])){
                $text .= Design::erstelleZeile($console, Language::Get('generateComponents','numberComponents'), 'v', $content['componentsCount'],'v');
                $text .= Design::erstelleZeile($console, Language::Get('generateComponents','numberLinks'), 'v', $content['linksCount'],'v');
            }

            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Language::Get('generateComponents','title'), $text);
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $serverFiles = Installation::GibServerDateien();

        $installComponentDefsResult['components']=array();
        foreach($serverFiles as $sf){
            $sf = pathinfo($sf)['filename'];
            $tempData = Einstellungen::ladeEinstellungenDirekt($sf,$data);
            if ($tempData === null){
                $fail = true;
                $error = Language::Get('generateComponents','noAccess');
                return;
            }

            $componentList = Zugang::Ermitteln('actionInstallComponentDefs','KomponentenErstellen::installiereKomponentenDefinitionen',$tempData, $fail, $errno, $error);

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
                        $comList[] = "('{$input['name']}', '{$input['urlExtern']}/{$input['path']}', '".(isset($input['option']) ? $input['option'] : '')."', '".implode(';',(isset($input['def']) ? $input['def'] : array()))."')";
                        // Verknüpfungen erstellen
                        $setDBNames[] = " SET @{$key}_{$input['name']} = (select CO_id from Component where CO_address='{$input['urlExtern']}/{$input['path']}' limit 1); ";
                        $input['dbName'] = $key.'_'.$input['name'];
                        $input['registered'] = '1';
                    }
                    if (!isset($tempList[$key2])) $tempList[$key2] = array();
                        $tempList[$key2][] = $input;

                } elseif (isset($input['type']) && $input['type']=='clone') {
                    // Komponente basiert auf einer bestehenden
                    if (!isset($input['base'])) continue;
                    if (!isset($input['baseURI'])) $input['baseURI'] = '';

                    if (isset($ComponentListInput[$input['base']]))
                        foreach ($ComponentListInput[$input['base']] as $key3 => $input2){
                            if (!isset($input2['name'])) continue;

                            // pruefe, dass die Eintraege nicht doppelt erstellt werden
                            $found=false;
                            if (isset($ComponentListInput[$input['name']]))
                                foreach ($ComponentListInput[$input['name']] as $input3){
                                    if ((!isset($input3['type']) || $input3['type']=='normal') && $input['name'] == $input3['name'] && "{$input3['urlExtern']}/{$input3['path']}" == "{$input2['urlExtern']}/{$input2['path']}{$input['baseURI']}"){
                                        $found = true;
                                        break;
                                    }
                                }
                            if ($found){ continue;}

                            if (isset($tempList[$input['name']]))
                                foreach ($tempList[$input['name']] as $input3){
                                    if ($input['name'] == $input3['name'] && "{$input3['urlExtern']}/{$input3['path']}" == "{$input2['urlExtern']}/{$input2['path']}{$input['baseURI']}"){
                                        $found = true;
                                        break;
                                    }
                                }
                            if ($found){ continue;}

                            $input2['path'] = "{$input2['path']}{$input['baseURI']}";
                            $input2['def'] = array_merge($input2['def'],$input['def']);

                            $input2['links'] = array_merge((isset($input2['links']) ? $input2['links'] : array()),(isset($input['links']) ? $input['links'] : array()));
                            $input2['connector'] = array_merge((isset($input2['connector']) ? $input2['connector'] : array()),(isset($input['connector']) ? $input['connector'] : array()));
                            if (isset($input['option']))
                                $input2['option'] = $input['option'];

                            $input2['name'] = $input['name'];
                            $input2['registered'] = null;
                            if (!isset($tempList[$key2])) $tempList[$key2] = array();
                            $tempList[$key2][] = $input2;
                        }
                }
            }
        }
            $ComponentListInput = $tempList;
        }

        $sql = "START TRANSACTION;SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;TRUNCATE TABLE `ComponentLinkage`;SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;COMMIT;";//TRUNCATE TABLE `Component`;
        Installation::log(array('text'=>'sql = '.$sql));
        $res = DBRequest::request2($sql, false, $data, true);
        Installation::log(array('text'=>'Resultat: '.json_encode($res)));

        $sql = "UPDATE `Component` SET `CO_status` = '0';";
        Installation::log(array('text'=>'sql = '.$sql));
        $res = DBRequest::request2($sql, false, $data, true);
        Installation::log(array('text'=>'Resultat: '.json_encode($res)));

        $sql = "START TRANSACTION;SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;INSERT INTO `Component` (`CO_name`, `CO_address`, `CO_option`, `CO_def`) VALUES ";
        $installComponentDefsResult['componentsCount'] = count($comList);
        $sql.=implode(',',$comList);
        unset($comList);
        $sql .= " ON DUPLICATE KEY UPDATE CO_status='1', CO_address=VALUES(CO_address), CO_option=VALUES(CO_option), CO_def=VALUES(CO_def);SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;COMMIT;";
        Installation::log(array('text'=>'sql = '.$sql));
        $res = DBRequest::request2($sql, false, $data, true);
        Installation::log(array('text'=>'Resultat: '.json_encode($res)));

        $sql = "START TRANSACTION;SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;";
        $sql .=implode('',$setDBNames);
        unset($setDBNames);
        $links = array();

        foreach ($ComponentListInput as $key2 => $ComNames){
            foreach ($ComNames as $key => $input){
                if (isset($input['type']) && $input['type']!='normal') continue;
                if (isset($input['dbName'])){

                    // prüfe nun alle Verknüpfungen dieser Komponente und erstelle diese
                    if (isset($input['links']))
                        foreach ($input['links'] as $link){
                            if (!isset($link['target'])) $link['target'] = '';
                            if (!is_array($link['target'])) $link['target'] = array($link['target']);

                            foreach ($link['target'] as $tar){// $tar -> der Name der Zielkomponente
                                if (!isset($ComponentListInput[$tar])) continue;
                                foreach ($ComponentListInput[$tar] as $target){
                                    // $target -> das Objekt der Zielkomponente
                                    if (!isset($target['dbName'])) continue;
                                    if (!isset($input['link_type']) || $input['link_type']=='local' || $input['link_type']==''){
                                        if ($input['urlExtern'] == $target['urlExtern']){

                                            $priority = (isset($input['priority']) ? ", CL_priority = {$input['priority']}" : '');
                                            $relevanz = (isset($input['relevanz']) ? $input['relevanz'] : '');
                                            $sql .= " INSERT INTO `ComponentLinkage` SET CO_id_owner = @{$input['dbName']}, CL_name = '{$link['name']}', CL_relevanz = '{$relevanz}', CO_id_target = @{$target['dbName']} {$priority};";
                                            $links[] = 1;
                                        }
                                    } elseif ($input['link_type']=='full'){
                                        if ($input['urlExtern'] == $target['urlExtern'] || (isset($target['link_availability']) && $target['link_availability']=='full')){

                                            $priority = (isset($input['priority']) ? ", CL_priority = {$input['priority']}" : '');
                                            $relevanz = (isset($input['relevanz']) ? $input['relevanz'] : '');
                                            $sql .= " INSERT INTO `ComponentLinkage` SET CO_id_owner = @{$input['dbName']}, CL_name = '{$link['name']}', CL_relevanz = '{$relevanz}', CO_id_target = @{$target['dbName']} {$priority};";
                                            $links[] = 1;
                                        }
                                    }
                                }
                            }
                        }

                    if (isset($input['connector'])){
                        foreach ($input['connector'] as $link){
                            if (!isset($link['target'])) $link['target'] = '';
                            if (!is_array($link['target'])) $link['target'] = array($link['target']);
                            if (!isset($link['links'])) $link['links'] = array('a'=>null);
                            foreach ($link['links'] as $callKey=>$call){
                                foreach ($link['target'] as $tar){// $tar -> der Name der Zielkomponente
                                    if (!isset($ComponentListInput[$tar])) continue;
                                    foreach ($ComponentListInput[$tar] as $target){
                                        // $target -> das Objekt der Zielkomponente
                                        if (!isset($target['dbName'])) continue;
                                        if (!isset($input['link_type']) || $input['link_type']=='local' || $input['link_type']==''){
                                            if ($input['urlExtern'] == $target['urlExtern']){

                                                $priority = (isset($link['priority']) ? ", CL_priority = {$link['priority']}" : '');
                                                $method = (isset($call['method']) ? $call['method'] : 'GET');
                                                $path = (isset($call['path']) ? ", CL_path = '{$method} {$call['path']}'" : '');
                                                $relevanz = (isset($link['relevanz']) ? $link['relevanz'] : '');
                                                $sql .= " INSERT INTO `ComponentLinkage` SET CO_id_owner = @{$target['dbName']}, CL_name = '{$link['name']}', CL_relevanz = '{$relevanz}', CO_id_target = @{$input['dbName']} {$priority} {$path};";
                                                $links[] = 1;
                                            }
                                        } elseif ($input['link_type']=='full'){
                                            if ($input['urlExtern'] == $target['urlExtern'] || (isset($input['link_availability']) && $input['link_availability']=='full')){

                                                $priority = (isset($link['priority']) ? ", CL_priority = {$link['priority']}" : '');
                                                $method = (isset($call['method']) ? $call['method'] : 'GET');
                                                $path = (isset($call['path']) ? ", CL_path = '{$method} {$call['path']}'" : '');
                                                $relevanz = (isset($link['relevanz']) ? $link['relevanz'] : '');
                                                $sql .= " INSERT INTO `ComponentLinkage` SET CO_id_owner = @{$target['dbName']}, CL_name = '{$link['name']}', CL_relevanz = '{$relevanz}', CO_id_target = @{$input['dbName']} {$priority} {$path};";
                                                $links[] = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $installComponentDefsResult['linksCount'] = count($links);
        $sql .= " SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;COMMIT;";
        Installation::log(array('text'=>'sql = '.$sql));
        $res = DBRequest::request2($sql, false, $data, true);
        Installation::log(array('text'=>'Resultat: '.json_encode($res)));
        $installComponentDefsResult['components'] = $ComponentListInput;

        Installation::log(array('text'=>'beende Funktion'));
        return $installComponentDefsResult;
    }

    public static function installiereKomponentenDefinitionen($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $res = array();

        if (!$fail){
            $mainPath = dirname(__FILE__) . '/../..';
            $components = array();

            $componentFiles = array();
            $plugins = PlugInsInstallieren::installCheckPlugins($data, $fail, $errno, $error);

            foreach ($plugins as $input){

                // Dateiliste zusammentragen
                $fileList = array();
                $fileListAddress = array();
                PlugInsInstallieren::gibPluginDateien($input, $fileList, $fileListAddress, $componentFiles);
                unset($fileList);
                unset($fileListAddress);
            }


            // Komponentennamen und Orte ermitteln
            $res['components'] = array();
            foreach ($componentFiles as $comFile){
                if (!file_exists($comFile) || !is_readable($comFile)) continue;
                $input = file_get_contents($comFile);
                $input = json_decode($input,true);
                if ($input==null) continue;

                if (isset($data['PL']['urlExtern'])) $input['urlExtern'] = $data['PL']['urlExtern'];
                if (isset($data['PL']['url'])) $input['url'] = $data['PL']['url'];
                $input['path'] = substr(dirname($comFile),strlen($mainPath)+1);
                $input['def'] = array($input['name'],str_replace("\\","/",realpath($comFile)));
                if (isset($data['CO']['co_link_type'])) $input['link_type'] = $data['CO']['co_link_type'];
                if (isset($data['CO']['co_link_availability'])) $input['link_availability'] = $data['CO']['co_link_availability'];

                if (isset($input['files'])) unset($input['files']);

                $res['components'][] = $input;
            }
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $res;
    }
}
#endregion KomponentenErstellen