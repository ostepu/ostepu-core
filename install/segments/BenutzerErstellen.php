<?php
#region BenutzerErstellen
class BenutzerErstellen
{
    private static $initialized=false;
    public static $name = 'SuperAdmin';
    public static $installed = false;
    public static $page = 4;
    public static $rank = 150;
    public static $enabledShow = true;
    
    public static $onEvents = array('install'=>array('name'=>'SuperAdmin','event'=>array('actionInstallSuperAdmin','install')));
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', '');
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text='';
        $text .= Design::erstelleBeschreibung($console,Sprachen::Get('createSuperAdmin','description'));
            
        if (!$console){
            $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_user_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root'), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_passwd_insert'), 'e', Design::erstellePasswortzeile($console, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', ''), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_first_name_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', ''), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_last_name_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', ''), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_email_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', ''), 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0], Sprachen::Get('main','create')), 'h');
        }
        
        if (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']]!=null){
           $result =  $result[self::$onEvents['install']['name']];
        } else 
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);
        
        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];
        
        if (self::$installed)
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error); 

        echo Design::erstelleBlock($console, Sprachen::Get('createSuperAdmin','title'), $text);
    }
    
    public static function install($data, &$fail, &$errno, &$error)
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
        return null;
    }
}
#endregion BenutzerErstellen