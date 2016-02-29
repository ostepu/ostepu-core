<?php
/**
 * @file Zugang.php
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once dirname(__FILE__) . '/../../Assistants/SSH/Net/SCP.php';
require_once dirname(__FILE__) . '/../../Assistants/SSH/Net/SSH2.php';
require_once dirname(__FILE__) . '/../../Assistants/SSH/Crypt/RSA.php';

class Zugang
{
    /**
     * ???
     *
     * @param string[][] $data Die Serverdaten
     * @return ???
     */
    public static function Verbinden($data)
    {
        $ssh=null;
        if ($data['ZV']['zv_ssh_auth_type'] == 'passwd'){
            $ssh = new Net_SSH2($data['ZV']['zv_ssh_address']);
            $res = @$ssh->login($data['ZV']['zv_ssh_login'], $data['ZV']['zv_ssh_password']);
            if (!$res) {
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

    /**
     * ???
     *
     * @param ???
     * @param ???
     * @param string[][] $data Die Serverdaten
     * @return ???
     */
    public static function EntferneDateien($files,$filesAddresses, $data)
    {
        $mainPath = dirname(__FILE__) . '/../..';

        if ($data['ZV']['zv_type'] == 'local' || $data['ZV']['zv_type'] == ''){
            // leer
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
                $command = '$path="'.$filesAddresses[$i].'";return unlink($path);';
                if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
                $command = Zugang::checkServerType($ssh, $command);
                $ssh->exec('php -r '.$command);
            }

            foreach ($allPaths as $path){
                if ($path!=null){
                    $command = '$path="'.$path.'";return rmdir($path);';
                    if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
                    $command = Zugang::checkServerType($ssh, $command);
                    $ssh->exec('php -r '.$command);
                }
            }

            $ssh->disconnect();

            if (isset($result['fail'])){
                $fail = $result['fail'];unset($result['fail']);
            }

            if (isset($result['errno'])){
                $errno = $result['errno'];unset($result['errno']);
            }

            if (isset($result['error'])){
                $error = $result['error'];unset($result['error']);
            }

        } else
            return array();
    }

    /**
     * ???
     *
     * @param ???
     * @param ???
     * @return ???
     */
    public static function checkServerType($ssh, $command)
    {
        $answer = $ssh->exec('php -r \'$g=1;echo "OK";\'');

        if ($answer=='OK')
            return "'".$command."'";

        return '"'.str_replace("\"","'",$command).'"';
    }

    /**
     * Sendet die Konfigurationsdatei des Servers an den Clienten
     *
     * @todo implementieren
     */
    public static function SendeServerEinstellungen()
    {
        // ausfüllen
        // ausfüllen
        // ausfüllen
    }

    /**
     * ???
     *
     * @param ???
     * @param ???
     * @param string[][] $data Die Serverdaten
     * @return ???
     */
    public static function SendeDateien($files,$filesAddresses, $data)
    {
        $mainPath = dirname(__FILE__) . '/../..';

        if ($data['ZV']['zv_type'] == 'local' || $data['ZV']['zv_type'] == ''){
            // leer
        } elseif ($data['ZV']['zv_type'] == 'ssh'){

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

            $ssh = Zugang::Verbinden($data);
            foreach ($allPaths as $path){
                if ($path!=null){
                    $command = '$path="'.$path.'"; $e=explode("/", ltrim($path,"/")); $c=count($e); $cp=$e[0]; for($i=1;$i<$c;$i++){if(!is_dir($cp) && !@mkdir($cp,0775)){return false;} chmod($cp,0775);$cp.="/".$e[$i];} @mkdir($path,0775);chmod($path,0775);return true;';
                    $command = Zugang::checkServerType($ssh,$command);
                    if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);}
                    $ssh->exec('php -r '.$command);
                }
            }

            $zip = new ZipArchive( );
            Einstellungen::generatepath( dirname(__FILE__).'/../temp' );
            if ( $zip->open(
                                dirname(__FILE__).'/../temp/data.zip',
                                ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE
                                ) === TRUE ){
                for($i=0;$i<count($files);$i++){
                    if (file_exists($files[$i]) && is_readable($files[$i])){
                        $zip->addFromString(
                                            $filesAddresses[$i],
                                            file_get_contents($files[$i])
                                            );
                    }
                }
                $zip->close( );
            }

            if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);}
            $scp = new Net_SCP($ssh);
            if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
            $scp->put('data.zip', dirname(__FILE__).'/../temp/data.zip', NET_SCP_LOCAL_FILE);

            $command = '$zip = new ZipArchive;$zip->open("data.zip");$zip->extractTo(".");$zip->close();unlink("data.zip");';
            if (count($ssh->channel_status)>0 && $ssh->channel_status[0] != 97){$ssh = Zugang::Verbinden($data);$scp = new Net_SCP($ssh);}
            $command = Zugang::checkServerType($ssh,$command);
            $ssh->exec('php -r '.$command);

            $ssh->disconnect();

            if (isset($result['fail'])){
                $fail = $result['fail'];unset($result['fail']);
            }

            if (isset($result['errno'])){
                $errno = $result['errno'];unset($result['errno']);
            }

            if (isset($result['error'])){
                $error = $result['error'];unset($result['error']);
            }

        } else
            return array();
    }

    /**
     * ???
     *
     * @param ???
     * @param string $func Der Bezeichner der aufzurufenden Funktion
     * @param string[][] $data Die Serverdaten
     * @param bool $fail true = Fehler, false = sonst
     * @param int $errno Die Fehlernummer
     * @param string $error Der Fehlertext
     * @return ???
     */
    public static function Ermitteln($action, $func, $data, &$fail, &$errno, &$error)
    {
        if (!isset($data['ZV']['zv_type']) || $data['ZV']['zv_type'] == 'local' || $data['ZV']['zv_type'] == ''){
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
            $result = $ssh->exec('php -f install/install.php -- '.$action);
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