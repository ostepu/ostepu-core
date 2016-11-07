<?php

/**
 * @file PlattformDatenbanknutzer.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
#region PlattformDatenbanknutzer
class PlattformDatenbanknutzer {

    private static $initialized = false;
    public static $name = 'DBOperator';
    public static $installed = false; // ob die Installation ausgelöst wurde
    public static $page = 2; // zu welcher Seite das Segment gehört
    public static $rank = 50;
    public static $enabledShow = true;
    private static $langTemplate = 'PlattformDatenbanknutzer';
    public static $onEvents = array('install' => array('name' => 'DBOperator', 'event' => array('actionInstallDBOperator', 'install')));

    public static function getDefaults()
    {        
        return array(
                     'db_user_override_operator' => array('data[DB][db_user_override_operator]', null),
                     'db_passwd_operatorRead' => array('data[DB][db_passwd_operatorRead]', Einstellungen::CreatePassword(20)),
                     'db_passwd_operatorWrite' => array('data[DB][db_passwd_operatorWrite]', Einstellungen::CreatePassword(20)),
                     'db_passwd_operatorSetup' => array('data[DB][db_passwd_operatorSetup]', Einstellungen::CreatePassword(50))
                     );
    }

    /**
     * initialisiert das Segment
     * @param type $console
     * @param string[][] $data die Serverdaten
     * @param bool $fail wenn ein Fehler auftritt, dann auf true setzen
     * @param string $errno im Fehlerfall kann hier eine Fehlernummer angegeben werden
     * @param string $error ein Fehlertext für den Fehlerfall
     */
    public static function init($console, &$data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__) . '/');
        Installation::log(array('text' => Installation::Get('main', 'languageInstantiated')));

        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operatorRead'], 'data[DB][db_passwd_operatorRead]', $def['db_passwd_operatorRead'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operatorWrite'], 'data[DB][db_passwd_operatorWrite]', $def['db_passwd_operatorWrite'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operatorSetup'], 'data[DB][db_passwd_operatorSetup]', $def['db_passwd_operatorSetup'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', $def['db_user_override_operator'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function show($console, $result, $data) {
        if (!Einstellungen::$accessAllowed) {
            return;
        }

        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $text = '';

        if (!$console) {
            $text .= Design::erstelleBeschreibung($console, Installation::Get('createDatabasePlatformUser', 'description', self::$langTemplate));
            $text .= Design::erstelleZeile($console, Installation::Get('createDatabasePlatformUser', 'db_user_override_operator', self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', 'override', null, true), 'v_c');
            $text .= Design::erstelleZeile($console, Installation::Get('createDatabasePlatformUser', 'createUser', self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0], Installation::Get('main', 'create')), 'h');
        }

        if (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']]!=null){
            $result =  $result[self::$onEvents['install']['name']];

            // diese Zeilen sorgen dafür, dass die Werte, welche im install gesetzt wurden, nicht 
            // nach dem Formular absenden wieder ueberschrieben werden
            $data['DB']['db_passwd_operatorRead'] = Einstellungen::Get('data[DB][db_passwd_operatorRead]', $data['DB']['db_passwd_operatorRead']);
            $data['DB']['db_passwd_operatorWrite'] = Einstellungen::Get('data[DB][db_passwd_operatorWrite]', $data['DB']['db_passwd_operatorWrite']);
            $data['DB']['db_passwd_operatorSetup'] = Einstellungen::Get('data[DB][db_passwd_operatorSetup]', $data['DB']['db_passwd_operatorSetup']);
            $def = self::getDefaults();
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operatorRead'], 'data[DB][db_passwd_operatorRead]', $def['db_passwd_operatorRead'][1],false);
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operatorWrite'], 'data[DB][db_passwd_operatorWrite]', $def['db_passwd_operatorWrite'][1],false);
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operatorSetup'], 'data[DB][db_passwd_operatorSetup]', $def['db_passwd_operatorSetup'][1],false);
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];
        if (self::$installed) {
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Installation::Get('createDatabasePlatformUser', 'title', self::$langTemplate), $text);

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        if (!$fail && ((isset($data['action']) && $data['action'] == 'update') || isset($data['DB']['db_user_override_operator']) && $data['DB']['db_user_override_operator'] === 'override')) {
            Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'removeUser', self::$langTemplate)));
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $sql = "DROP USER '{$data['DB']['db_user_operator']}'@'%';";
            $sql .= "DROP USER '{$data['DB']['db_user_operator']}Read'@'%';";
            $sql .= "DROP USER '{$data['DB']['db_user_operator']}Write'@'%';";
            $sql .= "DROP USER '{$data['DB']['db_user_operator']}Setup'@'%';";
            Installation::log(array('text'=>Installation::Get('createDatabasePlatformUser','dropGlobalUserSql',self::$langTemplate,array('sql'=>$sql))));
            $sql2 = "DROP USER '{$data['DB']['db_user_operator']}'@'localhost';";
            $sql2 .= "DROP USER '{$data['DB']['db_user_operator']}Read'@'localhost';";
            $sql2 .= "DROP USER '{$data['DB']['db_user_operator']}Write'@'localhost';";
            $sql2 .= "DROP USER '{$data['DB']['db_user_operator']}Setup'@'localhost';";
            Installation::log(array('text'=>Installation::Get('createDatabasePlatformUser','dropLocalUserSql',self::$langTemplate,array('sql'=>$sql2))));

            $result = DBRequest::request2($sql, false, $data);
            $result = DBRequest::request2($sql2, false, $data);
            /* if ($result['errno'] !== 0){
              $fail = true; $errno = $result['errno'];$error = isset($result["error"]) ? $result["error"] : '';
              } */
            $data['DB']['db_name'] = $oldName;
        }

        $userExists = false;
        if (!$fail) {
            Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'findExistingUser', self::$langTemplate, array('user' => $data['DB']['db_user_operator']))));

            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $sql = "SELECT count(1) as 'exists' FROM mysql.user WHERE user = '{$data['DB']['db_user_operator']}';";
            Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'findExistingUserSql', self::$langTemplate, array('sql' => $sql))));

            $result = DBRequest::request($sql, false, $data);

            if ($result['errno'] !== 0 || !isset($result["content"])) {
                $fail = true;
                $errno = $result['errno'];
                $error = isset($result["error"]) ? $result["error"] : '';
                Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'failureFindExistingUser', self::$langTemplate, array('message' => json_encode($result))), 'logLevel' => LogLevel::ERROR));
            } else {
                $result = DBJson::getRows($result['content']);
                if (count($result) > 0 && isset($result[0]['exists']) && $result[0]['exists'] > 0) {
                    Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'foundUser', self::$langTemplate)));
                    $userExists = true;
                } else {
                    Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'foundNoUser', self::$langTemplate)));
                }
            }
            $data['DB']['db_name'] = $oldName;
        }

        if (!$fail && !$userExists) {
            Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'creatingUser', self::$langTemplate, array('user' => $data['DB']['db_user_operator']))));

            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;

            $userVariants = array('', 'Read', 'Write', 'Setup');
            $userRights = array('REFERENCES,LOCK TABLES,CREATE VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,SHOW VIEW,CREATE TEMPORARY TABLES,INDEX,ALTER,SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,TRIGGER',
                                'LOCK TABLES,CREATE VIEW,SELECT,SHOW VIEW,EXECUTE',
                                'LOCK TABLES,INSERT,UPDATE,DELETE,CREATE VIEW,SELECT,SHOW VIEW,EXECUTE',
                                'REFERENCES,LOCK TABLES,CREATE VIEW,EXECUTE,ALTER ROUTINE,CREATE ROUTINE,SHOW VIEW,CREATE TEMPORARY TABLES,INDEX,ALTER,SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,TRIGGER');
            
            // erzeuge neue Passwörter für die speziellen Nutzer
            $data['DB']['db_passwd_operatorRead'] = Einstellungen::CreatePassword(20);
            $data['DB']['db_passwd_operatorWrite'] = Einstellungen::CreatePassword(20);
            $data['DB']['db_passwd_operatorSetup'] = Einstellungen::CreatePassword(50);
            
            Einstellungen::Set('data[DB][db_passwd_operatorRead]', $data['DB']['db_passwd_operatorRead']);
            Einstellungen::Set('data[DB][db_passwd_operatorWrite]', $data['DB']['db_passwd_operatorWrite']);
            Einstellungen::Set('data[DB][db_passwd_operatorSetup]', $data['DB']['db_passwd_operatorSetup']);
            
            $userPasswords = array($data['DB']['db_passwd_operator'], $data['DB']['db_passwd_operatorRead'], $data['DB']['db_passwd_operatorWrite'], $data['DB']['db_passwd_operatorSetup']);
            
            foreach ($userVariants as $key => $addToUserName){
                $sql = "CREATE USER '".$data['DB']['db_user_operator'].$addToUserName."'@'%' ".
                        "IDENTIFIED BY '".$userPasswords[$key]."';";
                $sql.= "GRANT ".$userRights[$key]." ".
                        "ON `{$oldName}`.* ".
                        "TO '".$data['DB']['db_user_operator'].$addToUserName."'@'%'; ";
                $sql.= "CREATE USER '".$data['DB']['db_user_operator'].$addToUserName."'@'localhost' ".
                        "IDENTIFIED BY '".$userPasswords[$key]."';";
                $sql.= "GRANT ".$userRights[$key]." ".
                        "ON `{$oldName}`.* ".
                        "TO '".$data['DB']['db_user_operator'].$addToUserName."'@'localhost';";

                $logSql = "CREATE USER '".$data['DB']['db_user_operator'].$addToUserName."'@'%' ".
                        "IDENTIFIED BY *****;";
                $logSql.= "GRANT ".$userRights[$key]." ".
                        "ON `{$oldName}`.* ".
                        "TO '".$data['DB']['db_user_operator'].$addToUserName."'@'%'; ";
                $logSql.= "CREATE USER '".$data['DB']['db_user_operator'].$addToUserName."'@'localhost' ".
                        "IDENTIFIED BY *****;";
                $logSql.= "GRANT ".$userRights[$key]." ".
                        "ON `{$oldName}`.* ".
                        "TO '".$data['DB']['db_user_operator'].$addToUserName."'@'localhost';";
                Installation::log(array('text'=>Installation::Get('createDatabasePlatformUser','createUserSql',self::$langTemplate,array('sql'=>$logSql))));
                $result = DBRequest::request2($sql, false, $data);           
                
                if ($result[0]['errno'] !== 0 && (count($result)<2 || $result[1]['errno'] !== 0)){
                    $fail = true; $errno = $result[0]['errno'];$error = isset($result[0]["error"]) ? $result[0]["error"] : '';
                    Installation::log(array('text'=>Installation::Get('createDatabasePlatformUser','failureCreateUser',self::$langTemplate,array('message'=>json_encode($result))), 'logLevel'=>LogLevel::ERROR));
                    break;
                }
            }    

            $data['DB']['db_name'] = $oldName;
        } elseif ($userExists) {
            $fail = true;
            $errno = 0;
            $error = 'user already exists';
            Installation::log(array('text' => Installation::Get('createDatabasePlatformUser', 'failureRemoveUser', self::$langTemplate, array('message' => $error)), 'logLevel' => LogLevel::ERROR));
        }

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

}

#endregion PlattformDatenbanknutzer