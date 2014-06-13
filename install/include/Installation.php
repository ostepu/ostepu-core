<?php


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
       $fail = false;
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
    
    public static function installiereSuperAdmin($data, &$fail, &$errno, &$error)
    {
        if (!$fail){
           $sql = "INSERT INTO `User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_salt`, `U_failed_logins`, `U_externalId`, `U_studentNumber`, `U_isSuperAdmin`, `U_comment`) VALUES (NULL, '{$data['DB']['db_user_insert']}', '{$data['DB']['db_email_insert']}', '{$data['DB']['db_last_name_insert']}', '{$data['DB']['db_first_name_insert']}', NULL, '{$data['DB']['db_passwd_insert']}', 1, NULL, 0, NULL, NULL, 1, NULL);";
           $result = DBRequest::request($sql, false, $data);
           if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
           }
        }
    
    }
    
    public static function installiereDatenbankdatei($data, &$fail, &$errno, &$error)
    {
        // database.sql    
        if (!$fail){
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
            $sql = "CREATE SCHEMA IF NOT EXISTS `".$data['DB']['db_name']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;";
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $result = DBRequest::request($sql, false, $data);
            if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
            }
            $data['DB']['db_name'] = $oldName;
        }
        
        if (!$fail){
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