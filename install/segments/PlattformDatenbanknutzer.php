<?php
#region PlattformDatenbanknutzer
class PlattformDatenbanknutzer
{
    private static $initialized=false;
    public static $name = 'DBOperator';
    public static $installed = false; // ob die Installation ausgelöst wurde
    public static $page = 2; // zu welcher Seite das Segment gehört
    public static $rank = 50;
    public static $enabledShow = true;
    
    public static $onEvents = array('install'=>array('name'=>'DBOperator','event'=>array('actionInstallDBOperator','install')));
    
    public static function getDefaults()
    {
        return array(
                     'db_user_override_operator' => array('data[DB][db_user_override_operator]', null),
                     );
    }
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $def = self::getDefaults();
        
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', $def['db_user_override_operator'][1], true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text='';        
        
        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Language::Get('createDatabasePlatformUser','description'));            
            $text .= Design::erstelleZeile($console, Language::Get('createDatabasePlatformUser','db_user_override_operator'), 'e', Design::erstelleAuswahl($console, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', 'override', null, true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('createDatabasePlatformUser','createUser'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0], Language::Get('main','create')), 'h');
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

        echo Design::erstelleBlock($console, Language::Get('createDatabasePlatformUser','title'), $text);
        return null;
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        if (!$fail && ((isset($data['action']) && $data['action']=='update') ||isset($data['DB']['db_user_override_operator']) && $data['DB']['db_user_override_operator'] === 'override')){
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
                if (count($result)>0 && isset($result[0]['exists']) && $result[0]['exists']>0) {
                    $userExists = true;
                }
            }
            $data['DB']['db_name'] = $oldName;
        }

        if (!$fail && !$userExists){
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $sql = "CREATE USER '{$data['DB']['db_user_operator']}'@'%'".
                    "IDENTIFIED BY '{$data['DB']['db_passwd_operator']}';";
            $sql.= "GRANT CREATE VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,SHOW VIEW,CREATE TEMPORARY TABLES,INDEX,ALTER,SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,TRIGGER ".
                    "ON `{$oldName}`.* ".
                    "TO '{$data['DB']['db_user_operator']}'@'%'; ";
            $sql.= "CREATE USER '{$data['DB']['db_user_operator']}'@'localhost'".
                    "IDENTIFIED BY '{$data['DB']['db_passwd_operator']}';";
            $sql.= "GRANT CREATE VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,SHOW VIEW,CREATE TEMPORARY TABLES,INDEX,ALTER,SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,TRIGGER ".
                    "ON `{$oldName}`.* ".
                    "TO '{$data['DB']['db_user_operator']}'@'localhost';";
            $result = DBRequest::request2($sql, false, $data);

            if ($result[0]["errno"] !== 0 && (count($result)<2 || $result[1]["errno"] !== 0)){
                $fail = true; $errno = $result[0]["errno"];$error = isset($result[0]["error"]) ? $result[0]["error"] : '';
            }
            $data['DB']['db_name'] = $oldName;
        } elseif ($userExists){
            $fail = true; $errno = 0;$error = 'user already exists';
        }

        return null;
    }
}
#endregion PlattformDatenbanknutzer