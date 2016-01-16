<?php
#region BenutzerschnittstelleEinrichten
class BenutzerschnittstelleEinrichten
{
    private static $initialized=false;
    public static $name = 'UIConf';
    public static $installed = false;
    public static $page = 6;
    public static $rank = 50;
    public static $enabledShow = true;
    public static $enabledInstall = true;
    private static $langTemplate='BenutzerschnittstelleEinrichten';

    public static $onEvents = array('install'=>array('name'=>'UIConf','event'=>array('actionInstallUIConf','install', 'update')));

    public static function getSettingsBar(&$data)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        $defs = self::getDefaults();
        $res = array(
                     'siteKey' => array(Language::Get('userInterface','siteKey',self::$langTemplate), $data['UI']['siteKey'], $defs['siteKey'][1])
                     );
        Installation::log(array('text'=>'Resultat: '.json_encode($res)));
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
        return $res;
    }

    public static function getDefaults()
    {
        return array(
                     'conf' => array('data[UI][conf]', '../UI/include/Config.php'),
                     'siteKey' => array('data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4')
                     );
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Language::Get('main','languageInstantiated')));
       
        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', $def['conf'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', $def['siteKey'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;
           
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('userInterface','description',self::$langTemplate));

        if (!$console){
            $text .= Design::erstelleZeile($console, Language::Get('userInterface','conf',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true), 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');
            $text .= Design::erstelleZeile($console, Language::Get('userInterface','siteKey',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4', true), 'v');
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

        echo Design::erstelleBlock($console, Language::Get('userInterface','title',self::$langTemplate), $text);
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        $fail = false;
        $file = $data['UI']['conf'];

        $text = array("<?php");
        $text[]='$serverURI'. " = '{$data['PL']['url']}';";
        $text[]='$databaseURI = $serverURI . "/DB/DBControl";';
        $text[]='$logicURI = $serverURI . "/logic/LController";';
        $text[]='$filesystemURI = $serverURI . "/FS/FSControl";';
        $text[]='$getSiteURI = $serverURI . "/logic/LGetSite";';
        $text[]='$globalSiteKey'. " = '{$data['UI']['siteKey']}';";
        $text[]='$externalURI'. " = '{$data['PL']['urlExtern']}';";

        $text = implode("\n",$text);
        Installation::log(array('text'=>'Text = '.json_encode($text)));
        $resFile = dirname(__FILE__).'/../'.$file;
        Installation::log(array('text'=>'Datei = '.$resFile));

        if (!@file_put_contents($resFile,$text)){
            $fail = true;
            $error='UI-Konfigurationsdatei, kein Schreiben möglich!';
            Installation::log(array('text'=>'Fehler: '.$error,'logLevel'=>LogLevel::ERROR));
            return null;
        }

        Installation::log(array('text'=>Language::Get('main','functionEnd')));
        return null;
    }
}
#endregion BenutzerschnittstelleEinrichten