<?php
require_once dirname(__FILE__) . '/../../Assistants/Net/SCP.php';
require_once dirname(__FILE__) . '/../../Assistants/Net/SSH2.php';
require_once dirname(__FILE__) . '/../../Assistants/Crypt/RSA.php';

class Zugang
{
    public static function Ermitteln($action, $func, $data, &$fail, &$errno, &$error)
    {
        if ($data['ZV']['zv_type'] == 'local' || $data['ZV']['zv_type'] == ''){
            return call_user_func($func, $data, $fail, $errno, $error);
        } elseif ($data['ZV']['zv_type'] == 'ssh'){
            if ($data['ZV']['zv_ssh_auth_type'] == 'passwd'){
                $ssh = new Net_SSH2($data['ZV']['zv_ssh_address']);
                if (!@$ssh->login($data['ZV']['zv_ssh_login'], $data['ZV']['zv_ssh_password'])) {
                    exit('bad login');
                }
            } else if ($data['ZV']['zv_ssh_auth_type'] == 'keyFile'){
                $ssh = new Net_SSH2($data['ZV']['zv_ssh_address']);
                $key = new Crypt_RSA();
                $key->loadKey(file_get_contents($data['ZV']['zv_ssh_key_file']));
                if (!$ssh->login($data['ZV']['zv_ssh_login'], $key)) {
                    exit('bad login');
                }
            }

            $result = json_decode($ssh->exec('php -f "install/install.php" -- '.$action),true);
            $ssh->disconnect();
            $result = $result[$action];
            
            if (isset($result['fail'])){
            $fail = $result['fail'];unset($result['fail']);
            }
            
            if (isset($result['errno'])){
            $errno = $result['errno'];unset($result['errno']);
            }
            
            if (isset($result['error'])){
            $error = $result['error'];unset($result['error']);
            }
            
            return $result;
        } else
            return array();
    }
}
?>