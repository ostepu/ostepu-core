<?php
require_once '../Assistants/Slim/Slim.php';
require_once '../Assistants/Request.php';
require_once '../Assistants/DBRequest.php';
require_once '../Assistants/DBJson.php';
require_once '../Assistants/Structures.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the Installer-Component
 */
class Installer
{
    /**
     * @var Slim $_app the slim object
     */
    private $app = null;
    
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct()
    {
        // initialize slim    
        $this->app = new \Slim\Slim(array( 'debug' => true ));

        // POST,GET showInstall
        $this->app->map('(/)',
                        array($this, 'install'))->via('POST', 'GET');
                        
        // POST,GET check
        $this->app->map('/check(/)',
                        array($this, 'checkRequirements'))->via('POST', 'GET');
                        
        // POST,GET SimpleInstall
        $this->app->map('/simple(/)',
                        array($this, 'simpleInstall'))->via('POST', 'GET');

        // run Slim
        $this->app->run();
    }
    
    function apache_module_exists($module)
    {
        return in_array($module, apache_get_modules());
    }

    function apache_extension_exists($extension)
    {
        return extension_loaded($extension);
    }
    
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
        
    public function checkRequirements()
    {
        // check if apache modules are existing
        if (!$this->apache_module_exists('mod_php5')) {$this->app->response->setStatus( 409 ); $this->app->stop();}
        if (!$this->apache_module_exists('mod_rewrite')) {$this->app->response->setStatus( 409 ); $this->app->stop();}
        
        // check if php extensions are existing
        if (!$this->apache_extension_exists('curl')) {$this->app->response->setStatus( 409 ); $this->app->stop();}
        if (!$this->apache_extension_exists('mysql')) {$this->app->response->setStatus( 409 ); $this->app->stop();}
        if (!$this->apache_extension_exists('mysqli')) {$this->app->response->setStatus( 409 ); $this->app->stop();}
        if (!$this->apache_extension_exists('json')) {$this->app->response->setStatus( 409 ); $this->app->stop();}
        
        $this->app->response->setStatus( 200 );
    }
    
    public function install($simple = false)
    {
        $installFail = false;
        
        if (isset($_POST['data']))
            $data = $_POST['data'];
            
        if (isset($_POST['actionInstall'])) $_POST['action'] = 'install';
        //if (isset($_POST['action'])) unset($_POST['action']);
        
        if ($simple)
            $this->app->response->headers->set('Content-Type', 'application/json');
        
        // check if apache modules are existing
        $modPhpExists = $this->apache_module_exists('mod_php5');
        $modRewriteExists = $this->apache_module_exists('mod_rewrite');
        
        // check if php extensions are existing
        $curlExists = $this->apache_extension_exists('curl');
        $mysqlExists = $this->apache_extension_exists('mysql');
        $mysqliExists = $this->apache_extension_exists('mysqli');
        $jsonExists = $this->apache_extension_exists('json');
        
        echo "
            <html><head><style type='text/css'>
            body {background-color: #ffffff; color: #000000;}
            body, td, th, h1, h2 {font-family: sans-serif;}
            pre {margin: 0px; font-family: monospace;}
            a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
            a:hover {text-decoration: underline;}
            table {border-collapse: collapse;}
            .center {text-align: center;}
            .center table { margin-left: auto; margin-right: auto; text-align: left;}
            .center th { text-align: center !important; }
            td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
            h1 {font-size: 150%;}
            h2 {font-size: 125%;}
            .p {text-align: left;}
            .e {background-color: #ccccff; font-weight: bold; color: #000000;}
            .h {background-color: #9999cc; font-weight: bold; color: #000000;text-align: right;}
            .v {background-color: #cccccc; color: #000000;}
            .vr {background-color: #cccccc; text-align: right; color: #000000;}
            img {float: right; border: 0px;}
            hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
            </style></head><body>
            <div class='center'>
            <h1>Installation</h1></br><hr />
        ";
        
        echo "<form action='' method='post'>";
        echo "
        <h2>Apache Module</h2>
        <table border='0' cellpadding='3' width='600'>
        ";
        echo "<tr><td class='e'>mod_php5</td><td class='v'>".($modPhpExists ? "OK" : "<font color='red'>Fehler</font>")."</td></tr>";
        echo "<tr><td class='e'>mod_rewrite</td><td class='v'>".($modRewriteExists ? "OK" : "<font color='red'>Fehler</font>")."</td></tr>";
        echo "</table><br />";
        
        echo "
        <h2>PHP Erweiterungen</h2>
        <table border='0' cellpadding='3' width='600'>
        ";
        echo "<tr><td class='e'>curl</td><td class='v'>".($curlExists ? "OK" : "<font color='red'>Fehler</font>")."</td></tr>";
        echo "<tr><td class='e'>mysql</td><td class='v'>".($mysqlExists ? "OK" : "<font color='red'>Fehler</font>")."</td></tr>";
        echo "<tr><td class='e'>mysqli</td><td class='v'>".($mysqliExists ? "OK" : "<font color='red'>Fehler</font>")."</td></tr>";
        echo "<tr><td class='e'>json</td><td class='v'>".($jsonExists ? "OK" : "<font color='red'>Fehler</font>")."</td></tr>";
        echo "</table><br />";
        
        echo "
        <h2>Grundeinstellungen</h2>
        <table border='0' cellpadding='3' width='600'>
        ";
        
        if (isset($data['PL']['url'])) $data['PL']['url'] = rtrim($data['PL']['url'], '/');
        
        echo "<tr><td class='e'>URL</td><td class='v'><input style='width:100%' type='text' name='data[PL][url]' value='".(isset($data['PL']['url']) ? $data['PL']['url'] : 'http://localhost/uebungsplattform')."'></td></tr>";
        echo "</table><br />";
 
        echo "
        <h2>Datenbank</h2>
        <table border='0' cellpadding='3' width='600'>
        ";
        echo "<tr><td class='e'>Adresse</td><td class='v'><input style='width:100%' type='text' name='data[DB][db_path]' value='".(isset($data['DB']['db_path']) ? $data['DB']['db_path'] : 'localhost')."'></td></tr>";
        echo "<tr><td class='e'>Datenbank</td><td class='v'><input style='width:100%' type='text' name='data[DB][db_name]' value='".(isset($data['DB']['db_name']) ? $data['DB']['db_name'] : 'uebungsplattform')."'></td></tr>";
        echo "<tr><td class='e'>Benutzername</td><td class='v'><input style='width:100%' type='text' name='data[DB][db_user]' value='".(isset($data['DB']['db_user']) ? $data['DB']['db_user'] : 'root')."'></td></tr>";
        echo "<tr><td class='e'>Passwort</td><td class='v'><input style='width:100%' type='password' name='data[DB][db_passwd]' value='".(isset($data['DB']['db_passwd']) ? $data['DB']['db_passwd'] : '')."'></td></tr>";
        
        echo "<tr><td class='e'>Datenbankdatei</td><td class='v'><input style='width:100%' type='text' name='data[DB][databaseSql]' value='".(isset($data['DB']['databaseSql']) ? $data['DB']['databaseSql'] : '../DB/Database2.sql')."'></td><td class='h'><input type='submit' name='actionInstallDatabase' value=' Installieren '></td></tr>";
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabase'])) && !$installFail){
            // database.sql
            $fail = false;
            $errno = 0;
            $error = '';
            
            if (!$fail){
               $sql = "DROP SCHEMA IF EXISTS `".$data['DB']['db_name']."`;";
               $oldName = $data['DB']['db_name'];
               $data['DB']['db_name'] = null;
               $result = DBRequest::request($sql, false, $data);
               if ($result["errno"] !== 0){
                    $fail = true; $errno = $result["errno"];$error = $result["error"];
               }
               $data['DB']['db_name'] = $oldName;
           }
           
           if (!$fail){
               $sql = "CREATE SCHEMA IF NOT EXISTS `".$data['DB']['db_name']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;";
               $oldName = $data['DB']['db_name'];
               $data['DB']['db_name'] = null;
               $result = DBRequest::request($sql, false, $data);
               if ($result["errno"] !== 0){
                    $fail = true; $errno = $result["errno"];$error = $result["error"];
               }
               $data['DB']['db_name'] = $oldName;
           }
            
           if (!$fail){
               $sql = file_get_contents($data['DB']['databaseSql']);
               $result = DBRequest::request2($sql, false, $data);
               if (!is_array($result)) $result = array($result);
               foreach ($result as $res){
                    if ($res["errno"] !== 0){
                        $fail = true; $errno = $result["errno"];$error = $result["error"];
                        break;
                    }
               }
               

           }
           
           if ($fail === true){
            $installFail = true;
            echo "<tr><td class='e'>Installation</td><td class='v'><font color='red'>Fehler ({$errno}) <br> {$error}</font></td></tr>";
           } else{
             echo "<tr><td class='e'>Installation</td><td class='v'>OK</td></tr>";
           }
        
        }
        
        echo "<tr><td class='e'>Komponentendefinition</td><td class='v'><input style='width:100%' type='text' name='data[DB][componentsSql]' value='".(isset($data['DB']['componentsSql']) ? $data['DB']['componentsSql'] : '../DB/Components2.sql')."'></td><td class='h'><input type='submit' name='actionInstallComponents' value=' Installieren '></td></tr>";
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallComponents'])) && !$installFail){
            // components.sql
           $fail = false;
           $sql = file_get_contents($data['DB']['componentsSql']);
           $sql = str_replace("'localhost/uebungsplattform/", "'{$data['PL']['url']}/" ,$sql);
           
           $result = DBRequest::request2($sql, false, $data);
           if (!is_array($result)) $result = array($result);
           foreach ($result as $res){
                if ($res["errno"] !== 0){
                    $fail = true; $errno = $result["errno"];$error = $result["error"];
                    break;
                }
           }
               
           if ($fail === true){
            $installFail = true;
            echo "<tr><td class='e'>Installation</td><td class='v'><font color='red'>Fehler ({$errno}) <br> {$error}</font></td></tr>";
           } else{
             echo "<tr><td class='e'>Installation</td><td class='v'>OK</td></tr>";
           }
        
        }

        
        echo "</table><br />";
        
        echo "
        <h2>Benutzerschnittstelle einrichten</h2>
        <table border='0' cellpadding='3' width='600'>
        ";
        echo "<tr><td class='e'>Konfigurationsdatei (mit Schreibrechten)</td><td class='v'><input style='width:100%' type='text' name='data[UI][conf]' value='".(isset($data['UI']['conf']) ? $data['UI']['conf'] : '../UI/include/Config.php')."'></td><td class='h'><input type='submit' name='actionInstallUIConf' value=' Installieren '></td></tr>";
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallUIConf'])) && !$installFail && isset($data['UI']['conf']) && $data['UI']['conf']!==''){
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
               
           if ($fail === true){
            $installFail = true;
            echo "<tr><td class='e'>Installation</td><td class='v'><font color='red'>Fehler ({$errno}) <br> {$error}</font></td></tr>";
           } else
             echo "<tr><td class='e'>Installation</td><td class='v'>OK</td></tr>";
        
        }
        
        echo "</table><br />";
        
        echo "
        <h2>Datenbankschnittstelle einrichten</h2>
        <table border='0' cellpadding='3' width='600'>
        ";
        
        echo "<tr><td class='e'>Konfigurationsdatei (mit Schreibrechten)</td><td class='v'><input style='width:100%' type='text' name='data[DB][config][]' value='".(isset($data['DB']['config'][0]) ? $data['DB']['config'][0] : '../DB/CControl/config.ini')."'></td><td class='h'><input type='submit' name='actionInstallDatabaseConf0' value=' Installieren '></td></tr>";
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabaseConf0'])) && !$installFail && isset($data['DB']['config'][0]) && $data['DB']['config'][0]!==''){
           $fail = false;
           $file = $data['DB']['config'][0];
           $text = "[DB]\n".
                   "db_path = {$data['DB']['db_path']}\n".
                   "db_user = {$data['DB']['db_user']}\n".
                   "db_passwd = {$data['DB']['db_passwd']}\n".
                   "db_name = {$data['DB']['db_name']}";
           if (!@file_put_contents($file,$text)) $fail = true;
            
           if ($fail === true){
            $installFail = true;
            echo "<tr><td class='e'>Installation</td><td class='v'><font color='red'>Fehler ({$errno}) <br> {$error}</font></td></tr>";
           } else
             echo "<tr><td class='e'>Installation</td><td class='v'>OK</td></tr>";
        
        }
        
        echo "<tr><td class='e'>Konfigurationsdatei (mit Schreibrechten)</td><td class='v'><input style='width:100%' type='text' name='data[DB][config][]' value='".(isset($data['DB']['config'][1]) ? $data['DB']['config'][1] : '../DB/DBQuery/config.ini')."'></td><td class='h'><input type='submit' name='actionInstallDatabaseConf1' value=' Installieren '></td></tr>";
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabaseConf1'])) && !$installFail && isset($data['DB']['config'][1]) && $data['DB']['config'][1]!==''){
           $fail = false;
           $file = $data['DB']['config'][1];
           $text = "[DB]\n".
                   "db_path = {$data['DB']['db_path']}\n".
                   "db_user = {$data['DB']['db_user']}\n".
                   "db_passwd = {$data['DB']['db_passwd']}\n".
                   "db_name = {$data['DB']['db_name']}";
           if (!@file_put_contents($file,$text)) $fail = true;
            
           if ($fail === true){
            $installFail = true;
            echo "<tr><td class='e'>Installation</td><td class='v'><font color='red'>Fehler ({$errno}) <br> {$error}</font></td></tr>";
           } else
             echo "<tr><td class='e'>Installation</td><td class='v'>OK</td></tr>";
        
        }

        echo "<tr><td class='e'>Konfigurationsdatei (mit Schreibrechten)</td><td class='v'><input style='width:100%' type='text' name='data[DB][config][]' value='".(isset($data['DB']['config'][2]) ? $data['DB']['config'][2] : '../DB/DBQuery2/config.ini')."'></td><td class='h'><input type='submit' name='actionInstallDatabaseConf2' value=' Installieren '></td></tr>";
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInstallDatabaseConf2'])) && !$installFail && isset($data['DB']['config'][2]) && $data['DB']['config'][2]!==''){
           $fail = false;
           $file = $data['DB']['config'][2];
           $text = "[DB]\n".
                   "db_path = {$data['DB']['db_path']}\n".
                   "db_user = {$data['DB']['db_user']}\n".
                   "db_passwd = {$data['DB']['db_passwd']}\n".
                   "db_name = {$data['DB']['db_name']}";
           if (!@file_put_contents($file,$text)) $fail = true;
            
           if ($fail === true){
            $installFail = true;
            echo "<tr><td class='e'>Installation</td><td class='v'><font color='red'>Fehler ({$errno}) <br> {$error}</font></td></tr>";
           } else
             echo "<tr><td class='e'>Installation</td><td class='v'>OK</td></tr>";
        
        }
        
        echo "</table><br />";
        
        echo "
        <h2>Komponenten</h2>

        <table border='0' cellpadding='3' width='600'>
        ";
         echo "<tr><td colspan='2'>Zur Initialisierung der Komponenten werden in deren Ordnern Schreibrechte benoetigt. (zum Schreiben der CConfig.json Dateien)</td></tr>";
         
        if (isset($data['PL']['init'])) $data['PL']['init'] = rtrim($data['PL']['init'], '/');
        echo "<tr><td class='e'>Initialisierung (Komponente)</td><td class='v'><input style='width:100%' type='text' name='data[PL][init]' value='".(isset($data['PL']['init']) ? $data['PL']['init'] : 'DB/CControl')."'></td><td class='h'><input type='submit' name='actionInitComponents' value=' Installieren '></td></tr>";
        if (((isset($_POST['action']) && $_POST['action'] === 'install') || isset($_POST['actionInitComponents'])) && !$installFail && isset($data['PL']['init']) && $data['PL']['init']!==''){
           $fail = false;
           $url = $data['PL']['init'];
           
           // inits all components
           $result = Request::get($data['PL']['url'].'/'.$url. '/definition/send',array(),'');
           if (isset($result['content']) && isset($result['status']) && $result['status'] === 200){
                $results = Component::decodeComponent($result['content']);
                $results = $this->orderBy(json_decode(Component::encodeComponent($results),true),'name',SORT_ASC);
                $results = Component::decodeComponent(Component::encodeComponent($results));
                if (!is_array($results)) $results = array($results);
                
                
                // get component definitions from database
                $result = Request::get($data['PL']['url'].'/'.$url. '/definition',array(),'');
                
                if (isset($result['content']) && isset($result['status']) && $result['status'] === 200){
                $definitions = Component::decodeComponent($result['content']);
                if (!is_array($definitions)) $definitions = array($definitions);
                
                    foreach($results as $res){
                        if ($res===null){
                            $fail = true;
                            continue;
                        }

                        $linkText='';
                        $countLinks = 0;
                        foreach ($definitions as $definition){
                            if ($definition->getId() === $res->getId()){
                                $links = $definition->getLinks();
                                $links = $this->orderBy(json_decode(Link::encodeLink($links),true),'name',SORT_ASC);
                                $links = Link::decodeLink(Link::encodeLink($links));
                                if (!is_array($links)) $links = array($links);
                                
                                $countLinks = count($links);
                                foreach($links as $link){
                                    $linkText .= "<tr><td class='v'>{$link->getName()}</td><td class='v'>{$link->getTargetName()}</td></tr>"; 
                                }
                                
                                break;
                            }
                        }
                        
                        $countLinks++;
                        $componentText = "<tr><td class='e' rowspan='{$countLinks}'>{$res->getName()}</td><td class='e'>{$res->getAddress()}</td><td class='e'>".($res->getStatus() === 201 ? "OK" : "<font color='red'>Fehler ({$res->getStatus()})</font>")."</td></tr>";

                        echo $componentText;
                        echo $linkText;
                       
                        if ($res->getStatus() !== 201)
                            $fail = true;
                    }
                }
           }else
            $fail = true;
            
           if ($fail === true){
            $installFail = true;
            echo "<tr><td class='e'>Installation</td><td class='v'></td><td class='v'><font color='red'>Fehler</font></td></tr>";
           } else
             echo "<tr><td class='e'>Installation</td><td class='v'></td><td class='v'>OK</td></tr>";
        
        }
        
        echo "</table><br />";
        
        echo "<table border='0' cellpadding='3' width='600'>";
        echo "<tr><td class='h'><div align='center'><input type='submit' name='actionInstall' value=' Alles Installieren '></div></td></tr>";
        echo "</table><br />";
        echo "</form>";
        
        

        echo "
            </div></body></html>
        ";
        
    }
    
    public function simpleInstall(){
          $this->install(true);
    }
}

// create a new instance of Installer class 
new Installer();
?>