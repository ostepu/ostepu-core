<?php
require_once dirname(__FILE__) . '/../../Assistants/Net/SCP.php';
require_once dirname(__FILE__) . '/../../Assistants/Net/SSH2.php';
require_once dirname(__FILE__) . '/../../Assistants/Crypt/RSA.php';

class Zugang
{
    public static function Verbinden($data)
    {
        $ssh=null;
        if ($data['ZV']['zv_ssh_auth_type'] == 'passwd'){
            $ssh = new Net_SSH2($data['ZV']['zv_ssh_address']);
            if (!@$ssh->login($data['ZV']['zv_ssh_login'], $data['ZV']['zv_ssh_password'])) {
                return null;
            }
            $ssh->setTimeout(0);
        } else if ($data['ZV']['zv_ssh_auth_type'] == 'keyFile'){
            $ssh = new Net_SSH2($data['ZV']['zv_ssh_address']);
            $key = new Crypt_RSA();
            $key->loadKey(file_get_contents($data['ZV']['zv_ssh_key_file']));
            if (!$ssh->login($data['ZV']['zv_ssh_login'], $key)) {
                return null;
            }
            $ssh->setTimeout(0);
        }
        
        return $ssh;
    }
    
    public static function EntferneDateien($files,$filesAddresses, $data)
    {
        $mainPath = dirname(__FILE__) . '/../..';
        
        if ($data['ZV']['zv_type'] == 'local' || $data['ZV']['zv_type'] == ''){
            //return call_user_func($func, $data, $fail, $errno, $error);
        } elseif ($data['ZV']['zv_type'] == 'ssh'){

            $ssh = Zugang::Verbinden($data);
            // Dateien entfernen
            if (!is_array($files)) $files = array($files);
            
            $allPaths = array();
            foreach ($filesAddresses as $addresses)
                $allPaths[] = dirname($addresses);
            
            sort($allPaths);
            $allPaths = array_unique($allPaths);
            $allPaths = array_values($allPaths);
            $allPaths = array_reverse($allPaths);
            
            $scp = new Net_SCP($ssh);
            for($i=0;$i<count($filesAddresses);$i++){
                if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
                $command = '$path=\''.$filesAddresses[$i].'\';return unlink($path);';
                if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
                $ssh->exec('php -r "'.$command.'"');
            }

            foreach ($allPaths as $path)
                if ($path!=null){
                    $command = '$path=\''.$path.'\';return rmdir($path);';
                    if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
                    $ssh->exec('php -r "'.$command.'"');
                }
                
            $ssh->disconnect();
            //$result = $result[$action];
            
            if (isset($result['fail'])){
            $fail = $result['fail'];unset($result['fail']);
            }
            
            if (isset($result['errno'])){
            $errno = $result['errno'];unset($result['errno']);
            }
            
            if (isset($result['error'])){
            $error = $result['error'];unset($result['error']);
            }
            
            //return $result;
        } else
            return array();
    }

    public static function SendeDateien($files,$filesAddresses, $data)
    {
        $mainPath = dirname(__FILE__) . '/../..';
        
        if ($data['ZV']['zv_type'] == 'local' || $data['ZV']['zv_type'] == ''){
            //return call_user_func($func, $data, $fail, $errno, $error);
        } elseif ($data['ZV']['zv_type'] == 'ssh'){

            $ssh = Zugang::Verbinden($data);
            // Dateien senden
            if (!is_array($files)) $files = array($files);
            
            $allPaths = array();
            foreach ($filesAddresses as $addresses)
                $allPaths[] = dirname($addresses);
            
            $allPaths = array_unique($allPaths);
            $allPaths = array_values($allPaths);

            for($i=0;$i<count($allPaths)-1;$i++)
                if (strpos($allPaths[$i+1].'/',$allPaths[$i].'/') === 0)
                    $allPaths[$i]=null;

            foreach ($allPaths as $path)
                if ($path!=null){
                    $command = '$path=\''.$path.'\';$e=explode(\'/\', ltrim($path,\'/\'));$c=count($e);$cp=$e[0];for($i=1;$i<$c;$i++){if(!is_dir($cp) && !@mkdir($cp,0755)){return false;}$cp.=\'/\'.$e[$i];}return @mkdir($path,0755);';
                    if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
                    $ssh->exec('php -r "'.$command.'"');
                }
            
            $scp = new Net_SCP($ssh);
            for($i=0;$i<count($files);$i++){
                if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
                if (file_exists($files[$i]) && is_readable($files[$i])){
                    $scp->put($filesAddresses[$i], $files[$i], NET_SCP_LOCAL_FILE);
                } else {
                
                }
            }

            $ssh->disconnect();
            //$result = $result[$action];
            
            if (isset($result['fail'])){
            $fail = $result['fail'];unset($result['fail']);
            }
            
            if (isset($result['errno'])){
            $errno = $result['errno'];unset($result['errno']);
            }
            
            if (isset($result['error'])){
            $error = $result['error'];unset($result['error']);
            }
            
            //return $result;
        } else
            return array();
    }

    public static function Ermitteln($action, $func, $data, &$fail, &$errno, &$error)
    {
        if ($data['ZV']['zv_type'] == 'local' || $data['ZV']['zv_type'] == ''){
            if (is_callable($func)){
                    $temp = explode('::',$func);
                    
                    $answer = $temp[0]::$temp[1]($data, $fail, $errno, $error);
                    return $answer;
            } else {
                $error = "Funktion $func kann nicht aufgerufen werden!";
                return array();
            }
           
        } elseif ($data['ZV']['zv_type'] == 'ssh'){

            $ssh = Zugang::Verbinden($data);
            $result = $ssh->exec('php -f "install/install.php" -- '.$action);
//echo $result;
            $result = json_decode($result,true);
            $ssh->disconnect();
            
            if (!isset($result[$action])) return array();
            
            $result = $result[$action];
            
            if (isset($result['fail'])){
                $fail = $result['fail'];
            }
            unset($result['fail']);
            
            if (isset($result['errno']) ){
                $errno = $result['errno'];
            }
            unset($result['errno']);
            
            if (isset($result['error'])){
                $error = $result['error'];
            }
            unset($result['error']);
            
            return $result;
        } else
            return array();
    }
}
?>