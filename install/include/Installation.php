<?php
require_once dirname(__FILE__) . '/../../UI/include/Authentication.php';
require_once dirname(__FILE__) . '/../../Assistants/Structures.php';
require_once dirname(__FILE__) . '/../../Assistants/Request.php';
require_once dirname(__FILE__) . '/../../Assistants/DBRequest.php';
require_once dirname(__FILE__) . '/../../Assistants/DBJson.php';

/**
 * @file Installation.php contains the Installation class
 *
 * @author Till Uhlig
 * @date 2014
 */
  
class Installation
{
   /**
     * Orders an array by given keys.
     *
     * This methods accepts multiple arguments. That means you can define more than one key.
     * e.g. orderby($data, 'key1', SORT_DESC, 'key2', SORT_ASC).
     *
     * @param array $data The array which will be sorted.
     * @param string $key The key of $data.
     * @param mixed $sortorder Either SORT_ASC to sort ascendingly or SORT_DESC to sort descendingly.
     *
     * @return array An array ordered by given parameters.
     */
    public static function orderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
    
    public static function PlattformZusammenstellen($data)
    {   
        // hier aus den Daten ein Plattform-Objekt zusammenstellen
        $platform = Platform::createPlatform(
                                            $data['PL']['url'],
                                            $data['DB']['db_path'],
                                            $data['DB']['db_name'],
                                            null,
                                            null,
                                            $data['DB']['db_user_operator'],
                                            $data['DB']['db_passwd_operator'],
                                            $data['PL']['temp'],
                                            $data['PL']['files'],
                                            $data['PL']['urlExtern']
                                            );
        return $platform;
    }
    
    public static function installiereInit($data, &$fail, &$errno, &$error)
    {
        // Datenbank einrichten
        if (!$fail && (isset($data['DB']['db_override']) && $data['DB']['db_override'] === 'override')){
           $sql = "DROP SCHEMA IF EXISTS `".$data['DB']['db_name']."`;";
           $oldName = $data['DB']['db_name'];
           $data['DB']['db_name'] = null;
           $result = DBRequest::request($sql, false, $data);
           if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
           }
           $data['DB']['db_name'] = $oldName;
        }
       
        if (!$fail){ 
            $add = ((isset($data['DB']['db_ignore']) && $data['DB']['db_ignore'] === 'ignore') ? 'IF NOT EXISTS ' : '');
            $sql = "CREATE SCHEMA {$add}`".$data['DB']['db_name']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;";
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $result = DBRequest::request($sql, false, $data);
            if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
            }
            $data['DB']['db_name'] = $oldName;
        }
        
        
        // CControl+DBQuery+DBQuery2 einrichten
        $res = array();
        if (!$fail){
            $list = array('DB/CControl','DB/DBQuery','DB/DBQuery2');
            $platform = Installation::PlattformZusammenstellen($data);

            for ($i=0;$i<count($list);$i++){
                $url = $list[$i];//$data['PL']['init'];
                // inits all components
                $result = Request::post($data['PL']['url'].'/'.$url. '/platform',array(),Platform::encodePlatform($platform));

                $res[$url] = array();
                if (isset($result['content']) && isset($result['status']) && $result['status'] === 201){
                    $res[$url]['status'] = 201;
                } else {
                    $res[$url]['status'] = 409;
                    $fail = true;
                    if (isset($result['status'])){
                        $errno = $result['status'];
                        $res[$url]['status'] = $result['status'];
                    };
                    ///if (isset($result['content'])) echo $result['content'];
                }
            }
        }
        
        return $res;
    }
    
    public static function GibServerDateien()
    {
        $serverFiles = array();
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);
        if ($handle = opendir(dirname(__FILE__) . '/../config')) {
            while (false !== ($file = readdir($handle))) {
                if ($file=='.' || $file=='..') continue;
                $serverFiles[] = $file;
            }
            closedir($handle);
        }
        return $serverFiles;
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
    
    public static function checkPlugins($data, &$fail, &$errno, &$error)
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
    
    public static function initialisierePlugins($data, &$fail, &$errno, &$error)
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
    
    public static function deinitialisierePlugins($data, &$fail, &$errno, &$error)
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
    
    public static function installiereKomponentenDefinitionen($data, &$fail, &$errno, &$error)
    {
        $res = array();
    
        if (!$fail){
            $mainPath = dirname(__FILE__) . '/../..';
            $components = array();
                
            $componentFiles = array();
            $plugins = Installation::checkPlugins($data, $fail, $errno, $error);
            
            foreach ($plugins as $input){
                
                // Dateiliste zusammentragen
                $fileList = array();
                $fileListAddress = array();
                Installation::gibPluginDateien($input, $fileList, $fileListAddress, $componentFiles);
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
                $input['urlExtern'] = $data['PL']['urlExtern'];
                $input['url'] = $data['PL']['url'];
                $input['path'] = substr(dirname($comFile),strlen($mainPath)+1);
                $input['link_type'] = $data['CO']['co_link_type'];
                $input['link_availability'] = $data['CO']['co_link_availability'];
                
                if (isset($input['files'])) unset($input['files']);
                
                $res['components'][] = $input;
                /*if (!isset($input['type']) || $input['type']=='normal'){
                    // normale Komponente
                    if (!isset($input['name'])) continue;
                    if (!isset($components[$input['name']]))$components[$input['name']] = array();
                    $components[$input['name']][] = substr(dirname($comFile),strlen($mainPath)+1);
                    
                } elseif (isset($input['type']) && $input['type']=='clone') {
                    // Komponente basiert auf einer bestehenden
                    if (!isset($components[$input['name']]))$components[$input['name']] = array();
                    $components[$input['name']][] = substr(dirname($comFile),strlen($mainPath)+1);
                }*/
            }
            
            // Komponenten eintragen
            //$res['components'] = array();
            //$sql = "START TRANSACTION;INSERT INTO `Component` (`CO_name`, `CO_address`, `CO_option`) VALUES ";
            //$comList = array();
            foreach($components as $comName => $coms){
                foreach($coms as $com){
                    //$comList[] = "('{$comName}', '{$com}', '')";
                    //$res['components'][] = array($comName,$com,$data['PL']['urlExtern']);
                }
            }
            //$sql.=implode(',',$comList);

            //$sql .= " ON DUPLICATE KEY UPDATE CO_address=VALUES(CO_address), CO_option=VALUES(CO_option);COMMIT;";
            //DBRequest::request2($sql, false, $data);
            //$res = $components;
        }
        
        return $res;
    }
    
    public static function installierePlattform($data, &$fail, &$errno, &$error)
    {
        $res = array();
    
        if (!$fail){
            // die /platform Befehle auslösen
            $list = array('DB/DBApprovalCondition','DB/DBAttachment','DB/DBCourse','DB/DBCourseStatus','DB/DBExercise','DB/DBExerciseFileType','DB/DBExerciseSheet','DB/DBExerciseType','DB/DBExternalId','DB/DBFile','DB/DBGroup','DB/DBInvitation','DB/DBMarking','DB/DBSelectedSubmission','DB/DBSession','DB/DBSubmission','DB/DBUser','FS/FSFile','FS/FSPdf','FS/FSZip','FS/FSBinder','logic/LTutor');
            
            $platform = Installation::PlattformZusammenstellen($data);
            
            $multiRequestHandle = new Request_MultiRequest();

            for ($i=0;$i<count($list);$i++){
                $url = $list[$i];//$data['PL']['init'];
                // inits all components
                $handler = Request_CreateRequest::createPost($data['PL']['url'].'/'.$url. '/platform',array(),Platform::encodePlatform($platform));
                $multiRequestHandle->addRequest($handler);
            }
            
            $answer = $multiRequestHandle->run();
            
            for ($i=0;$i<count($list);$i++){
                $url = $list[$i];            
                $result = $answer[$i];
                $res[$url] = array();
                if (isset($result['content']) && isset($result['status']) && $result['status'] === 201){
                    $res[$url]['status'] = 201;
                } else {
                    $res[$url]['status'] = 409;
                    $fail = true;
                    if (isset($result['status'])){
                        $errno = $result['status'];
                        $res[$url]['status'] = $result['status'];
                    }
                }
            }
        }
        
        return $res;
    }
    
    public static function initialisiereKomponenten($data, &$fail, &$errno, &$error)
    {
        $fail = false;
        $url = $data['PL']['init'];
        $components = array();
       
        // inits all components
        $result = Request::get($data['PL']['url'].'/'.$url. '/definition/send',array(),'');
        //echo $result['content'];
        if (isset($result['content']) && isset($result['status'])){

            // component routers
            $router = array();

            $results = Component::decodeComponent($result['content']);
            $results = Installation::orderBy(json_decode(Component::encodeComponent($results),true),'name',SORT_ASC);
            $results = Component::decodeComponent(Component::encodeComponent($results));
            if (!is_array($results)) $results = array($results);
            
            foreach($results as $res){
                $components[$res->getName()] = array();
                $components[$res->getName()]['init'] = $res;
            }

            // get component definitions from database
            $result4 = Request::get($data['PL']['url'].'/'.$url. '/definition',array(),'');
            
            if (isset($result4['content']) && isset($result4['status']) && $result4['status'] === 200){
                $definitions = Component::decodeComponent($result4['content']);
                if (!is_array($definitions)) $definitions = array($definitions);
                
                $result2 = new Request_MultiRequest();
                $result3 = new Request_MultiRequest();
                $tempDef = array();
                foreach ($definitions as $definition){
                    if (strpos($definition->getAddress().'/', $data['PL']['urlExtern'].'/')===false) {continue;}
                    
                    $components[$definition->getName()]['definition'] = $definition;
                    $tempDef[] = $definition;      
                    $request = Request_CreateRequest::createGet($definition->getAddress().'/info/commands',array(),'');
                    $result2->addRequest($request);
                    $request = Request_CreateRequest::createGet($definition->getAddress().'/info/links',array(),'');
                    $result3->addRequest($request);
                }
                $definitions = $tempDef;
                
                $result2 = $result2->run();
                $result3 = $result3->run();
            
                foreach($results as $res){
                    if ($res===null){
                        $fail = true;
                        continue;
                    }

                    $countLinks = 0;
                    $resultCounter=-1;
                    foreach ($definitions as $definition){
                        //if (strpos($definition->getAddress().'/', $data['PL']['urlExtern'].'/')===false) continue;
                        
                        $resultCounter++;
                        if ($definition->getId() === $res->getId()){
                        
                            $links = $definition->getLinks();
                            $links = Installation::orderBy(json_decode(Link::encodeLink($links),true),'name',SORT_ASC);
                            $links = Link::decodeLink(Link::encodeLink($links));
                            if (!is_array($links)) $links = array($links);
                            
                            $components[$definition->getName()]['links'] = $links;
                            
                            if (isset($result2[$resultCounter]['content']) && isset($result2[$resultCounter]['status']) && $result2[$resultCounter]['status'] === 200){
                                $commands = json_decode($result2[$resultCounter]['content'], true);
                                if ($commands!==null){
                                    /*$router = new \Slim\Router();
                                    foreach($commands as $command){
                                        $route = new \Slim\Route($command['path'],'is_array');
                                        $route->via(strtoupper($command['method']));
                                        $router->map($route);
                                    }
                                    $components[$definition->getName()]['router'] = $router;*/
                                    $components[$definition->getName()]['commands'] = $commands;
                                }
                            }
                            
                            if (isset($result3[$resultCounter]['content']) && isset($result3[$resultCounter]['status']) && $result3[$resultCounter]['status'] === 200){
                                $calls = json_decode($result3[$resultCounter]['content'], true);
                                $components[$definition->getName()]['call'] = $calls;
                            }
                                                        
                            break;
                        }
                    }

                    if ($res->getStatus() !== 201){
                        $fail = true;
                    }
                }
            } else{
               $fail = true;
               $error = "keine Definitionen";
            }
            
       }else{
            $fail = true;
       }
        
        if (isset($result['status']) && $result['status'] !== 200){
            $fail = true;
            $error = "Initialisierung fehlgeschlagen";
            $errno = $result['status'];
        }
        
        return $components;
    }
    
    public static function installiereUIKonfigurationsdatei($data, &$fail, &$errno, &$error)
    {
        $fail = false;
        $file = $data['UI']['conf'];
        if (!file_exists(dirname(__FILE__).'/../'.$data['UI']['conf'])){ $fail = true;$error='UI-Konfigurationsdatei wurde nicht gefunden!';return;}
        
        $text = explode("\n",file_get_contents(dirname(__FILE__).'/../'.$data['UI']['conf']));
        foreach ($text as &$tt){
            if (substr(trim($tt),0,10)==='$serverURI'){
                $tt='$serverURI'. " = '{$data['PL']['url']}';";
            } else
            if (substr(trim($tt),0,14)==='$globalSiteKey'){
                $tt='$globalSiteKey'. " = '{$data['UI']['siteKey']}';";
            }
        }
        
        
        $text = implode("\n",$text);
        if (!@file_put_contents(dirname(__FILE__).'/../'.$file,$text)){ $fail = true;$error='UI-Konfigurationsdatei, kein Schreiben möglich!';return;}
    }

    public static function installiereKomponentendatei($data, &$fail, &$errno, &$error)
    {
        $mainPath = dirname(__FILE__) . '/..';
        if (!$fail){
            if (!file_exists($mainPath.'/'.$data['DB']['componentsSql'])){
                $error = "Datei existiert nicht";
                $fail = true;
                return;
            }
                
           $sql = file_get_contents($mainPath.'/'.$data['DB']['componentsSql']);
           $sql = str_replace("'localhost/uebungsplattform/", "'{$data['PL']['url']}/" ,$sql);

           $result = DBRequest::request2($sql, false, $data);
           if (!is_array($result)) $result = array($result);
           foreach ($result as $res){
                if ($res["errno"] !== 0){
                    $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
                    break;
                }
           }
       }
    }

    public static function installiereSuperAdmin($data, &$fail, &$errno, &$error)
    {
        if (!$fail){    
           $auth = new Authentication();
           $salt = $auth->generateSalt();
           $passwordHash = $auth->hashPassword($data['DB']['db_passwd_insert'], $salt);
           
           $sql = "INSERT INTO `User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_salt`, `U_failed_logins`, `U_externalId`, `U_studentNumber`, `U_isSuperAdmin`, `U_comment`) VALUES (NULL, '{$data['DB']['db_user_insert']}', '{$data['DB']['db_email_insert']}', '{$data['DB']['db_last_name_insert']}', '{$data['DB']['db_first_name_insert']}', NULL, '$passwordHash', 1, '{$salt}', 0, NULL, NULL, 1, NULL);";
           $result = DBRequest::request($sql, false, $data);
           if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
           }
        }
    }
    
    public static function installiereDBOperator($data, &$fail, &$errno, &$error)
    {
        if (!$fail && isset($data['DB']['db_user_override_operator']) && $data['DB']['db_user_override_operator'] === 'override'){
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $sql = "DROP USER '{$data['DB']['db_user_operator']}'@'%';";
            $sql2 = "DROP USER '{$data['DB']['db_user_operator']}'@'localhost';";
            $result = DBRequest::request2($sql, false, $data);
            $result = DBRequest::request2($sql2, false, $data);
            /*if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
            }*/
            $data['DB']['db_name'] = $oldName;
        }
        
        $userExists = false;
        if (!$fail){
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $sql = "SELECT count(1) as 'exists' FROM mysql.user WHERE user = '{$data['DB']['db_user_operator']}';";
            $result = DBRequest::request($sql, false, $data);

            if ($result["errno"] !== 0 || !isset($result["content"])){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
            } else {
                $result = DBJson::getRows($result['content']);
                if (count($result)>0 && isset($result[0]['exists']) && $result[0]['exists'] === '1') {
                    $userExists = true;
                }
            }
            $data['DB']['db_name'] = $oldName;
        }
 
        if (!$fail && !$userExists){
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $sql = "GRANT CREATE VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,SHOW VIEW,CREATE TEMPORARY TABLES,INDEX,ALTER,SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,TRIGGER ".
                    "ON `{$oldName}`.* ".
                    "TO '{$data['DB']['db_user_operator']}'@'%' ".
                    "IDENTIFIED BY '{$data['DB']['db_passwd_operator']}';";
            $sql.= "GRANT CREATE VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,SHOW VIEW,CREATE TEMPORARY TABLES,INDEX,ALTER,SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,TRIGGER ".
                    "ON `{$oldName}`.* ".
                    "TO '{$data['DB']['db_user_operator']}'@'localhost' ".
                    "IDENTIFIED BY '{$data['DB']['db_passwd_operator']}';";
            $result = DBRequest::request2($sql, false, $data);
            if ($result[0]["errno"] !== 0 && (count($result)<2 || $result[1]["errno"] !== 0)){
                $fail = true; $errno = $result[0]["errno"];$error = isset($result[0]["error"]) ? $result[0]["error"] : '';
            }
            $data['DB']['db_name'] = $oldName;
        } elseif ($userExists){
            $fail = true; $errno = 0;$error = 'user already exists';
        }
    }
    
    public static function installiereDatenbankdatei($data, &$fail, &$errno, &$error)
    {
        // database.sql    
        if (!$fail && (isset($data['DB']['db_override']) && $data['DB']['db_override'] === 'override')){
           $sql = "DROP SCHEMA IF EXISTS `".$data['DB']['db_name']."`;";
           $oldName = $data['DB']['db_name'];
           $data['DB']['db_name'] = null;
           $result = DBRequest::request($sql, false, $data);
           if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
           }
           $data['DB']['db_name'] = $oldName;
        }
       
        if (!$fail){
            $sql = "CREATE SCHEMA `".$data['DB']['db_name']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;";
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $result = DBRequest::request($sql, false, $data);
            if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
            }
            $data['DB']['db_name'] = $oldName;
        }
        
        if (!$fail){
            if (!file_exists($data['DB']['databaseSql'])){
                $error = "Datei existiert nicht";
                $fail = true;
                return;
            }
            
            $sql = file_get_contents($data['DB']['databaseSql']);
            $result = DBRequest::request2($sql, false, $data);
            if (!is_array($result)) $result = array($result);
            foreach ($result as $res){
                if ($res["errno"] !== 0){
                    $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
                    break;
                }
           }
        }
    }
   
   /** 
    * Finds path, relative to the given root folder, of all files and directories in the given directory and its sub-directories non recursively. 
    * Will return an array of the form 
    * array( 
    *   'files' => [], 
    *   'dirs'  => [], 
    * ) 
    * @author sreekumar 
    * @param string $root 
    * @result array 
    */ 
    public static function read_all_files($root = '.'){ 
      $files  = array('files'=>array(), 'dirs'=>array()); 
      $directories  = array(); 
      $last_letter  = $root[strlen($root)-1]; 
      $root  = ($last_letter == '/') ? $root : $root.'/'; 
      
      $directories[]  = $root; 
      
      while (sizeof($directories)) { 
        $dir  = array_pop($directories); 
        if ($handle = opendir($dir)) { 
          while (false !== ($file = readdir($handle))) { 
            if ($file == '.' || $file == '..') { 
              continue; 
            } 
            $file  = $dir.$file; 
            if (is_dir($file)) { 
              $directory_path = $file.'/'; 
              array_push($directories, $directory_path); 
              $files['dirs'][]  = $directory_path; 
            } elseif (is_file($file)) { 
              $files['files'][]  = $file; 
            } 
          } 
          closedir($handle); 
        } 
      } 
      
      return $files; 
    } 
}