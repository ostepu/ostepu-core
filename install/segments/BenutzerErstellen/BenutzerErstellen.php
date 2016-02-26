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
    private static $langTemplate='BenutzerErstellen';

    public static $onEvents = array('install'=>array('name'=>'SuperAdmin','event'=>array('actionInstallSuperAdmin','install')));

    public static function getDefaults()
    {
        return array(
                     'db_user_insert' => array('data[DB][db_user_insert]', 'root'),
                     'db_first_name_insert' => array('data[DB][db_first_name_insert]', ''),
                     'db_last_name_insert' => array('data[DB][db_last_name_insert]', ''),
                     'db_email_insert' => array('data[DB][db_email_insert]', ''),
                     'db_passwd_insert' => array('data[DB][db_passwd_insert]', '')
                     );
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));
      
        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', $def['db_user_insert'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', $def['db_first_name_insert'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', $def['db_last_name_insert'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', $def['db_email_insert'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', $def['db_passwd_insert'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;
          
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('createSuperAdmin','description',self::$langTemplate));

        if (!$console){
            $text .= Design::erstelleZeile($console, Installation::Get('createSuperAdmin','db_user_insert',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root'), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('createSuperAdmin','db_passwd_insert',self::$langTemplate), 'e', Design::erstellePasswortzeile($console, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', ''), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('createSuperAdmin','db_first_name_insert',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', ''), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('createSuperAdmin','db_last_name_insert',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', ''), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('createSuperAdmin','db_email_insert',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', ''), 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0], Installation::Get('main','create')), 'h');
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

        echo Design::erstelleBlock($console, Installation::Get('createSuperAdmin','title',self::$langTemplate), $text);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        if (!$fail){
           $auth = new Authentication();
           $salt = $auth->generateSalt();
           $passwordHash = $auth->hashPassword($data['DB']['db_passwd_insert'], $salt);

           $sql = "INSERT INTO `User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_salt`, `U_failed_logins`, `U_externalId`, `U_studentNumber`, `U_isSuperAdmin`, `U_comment`) VALUES (NULL, '{$data['DB']['db_user_insert']}', '{$data['DB']['db_email_insert']}', '{$data['DB']['db_last_name_insert']}', '{$data['DB']['db_first_name_insert']}', NULL, '$passwordHash', 1, '{$salt}', 0, NULL, NULL, 1, NULL);";
           $logSql = "INSERT INTO `User` (`U_id`, `U_username`, `U_email`, `U_lastName`, `U_firstName`, `U_title`, `U_password`, `U_flag`, `U_salt`, `U_failed_logins`, `U_externalId`, `U_studentNumber`, `U_isSuperAdmin`, `U_comment`) VALUES (NULL, '{$data['DB']['db_user_insert']}', '{$data['DB']['db_email_insert']}', '{$data['DB']['db_last_name_insert']}', '{$data['DB']['db_first_name_insert']}', NULL, '*****', 1, '*****', 0, NULL, NULL, 1, NULL);";
           Installation::log(array('text'=>Installation::Get('createSuperAdmin','queryResult',self::$langTemplate,array('sql'=>$logSql))));

           $result = DBRequest::request($sql, false, $data);
           Installation::log(array('text'=>Installation::Get('createSuperAdmin','queryResult',self::$langTemplate,array('res'=>json_encode($result)))));
           if ($result['errno'] !== 0){
                $fail = true; $errno = $result['errno'];$error = isset($result["error"]) ? $result["error"] : '';
           }
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }
}
#endregion BenutzerErstellen