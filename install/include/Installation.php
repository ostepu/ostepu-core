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
                                            $data['DB']['db_passwd_operator']
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
                    }
                }
            }
        }
        
        return $res;
    }
    
    public static function installierePlattform($data, &$fail, &$errno, &$error)
    {
        $res = array();
    
        if (!$fail){
            // die /platform Befehle auslösen
            $list = array('DB/DBApprovalCondition','DB/DBAttachment','DB/DBCourse','DB/DBCourseStatus','DB/DBExercise','DB/DBExerciseFileType','DB/DBExerciseSheet','DB/DBExerciseType','DB/DBExternalId','DB/DBFile','DB/DBGroup','DB/DBInvitation','DB/DBMarking','DB/DBSelectedSubmission','DB/DBSession','DB/DBSubmission','DB/DBUser');
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
            foreach ($definitions as $definition){
                $components[$definition->getName()]['definition'] = $definition;
                            
                $request = Request_CreateRequest::createGet($definition->getAddress().'/info/commands',array(),'');
                $result2->addRequest($request);
                $request = Request_CreateRequest::createGet($definition->getAddress().'/info/links',array(),'');
                $result3->addRequest($request);
            }
            
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
                                    $router = new \Slim\Router();
                                    foreach($commands as $command){
                                        $route = new \Slim\Route($command['path'],'is_array');
                                        $route->via(strtoupper($command['method']));
                                        $router->map($route);
                                    }
                                    $components[$definition->getName()]['router'] = $router;
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
    
    public static function installiereDBKonfigurationsdatei($data, $id, &$fail, &$errno, &$error)
    {
        $file = $data['DB']['config'][$id];
        $text = "[DB]\n".
                "db_path = {$data['DB']['db_path']}\n".
                "db_user = {$data['DB']['db_user_operator']}\n".
                "db_passwd = {$data['DB']['db_passwd_operator']}\n".
                "db_name = {$data['DB']['db_name']}";
                
        if (!@file_put_contents($file,$text)) $fail = true;
    }
    
    public static function installiereUIKonfigurationsdatei($data, &$fail, &$errno, &$error)
    {
        $fail = false;
        $file = $data['UI']['conf'];
        $text = explode("\n",file_get_contents($data['UI']['conf']));
        foreach ($text as &$tt){
            if (substr(trim($tt),0,10)==='$serverURI'){
                $tt='$serverURI'. " = '{$data['PL']['url']}';";
            }
        }
        $text = implode("\n",$text);

        if (!@file_put_contents($file,$text)) $fail = true;
    }

    public static function installiereKomponentendatei($data, &$fail, &$errno, &$error)
    {
        if (!$fail){
            if (!file_exists($data['DB']['componentsSql'])){
                $error = "Datei existiert nicht";
                $fail = true;
                return;
        }
                
           $sql = file_get_contents($data['DB']['componentsSql']);
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
            $sql = "DROP USER {$data['DB']['db_user_operator']}@localhost;";
            $result = DBRequest::request($sql, false, $data);
            if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
            }
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
            $sql = "GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,TRIGGER ".
                    "ON {$oldName}.* ".
                    "TO '{$data['DB']['db_user_operator']}'@'localhost' ".
                    "IDENTIFIED BY '{$data['DB']['db_passwd_operator']}';";
            $result = DBRequest::request($sql, false, $data);
            if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
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
}
?>