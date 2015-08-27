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
    
    public static $onEvents = array('install'=>array('name'=>'UIConf','event'=>array('actionInstallUIConf','install', 'update')));
    
    public static function getDefaults()
    {
        return array(
                     'conf' => array('data[UI][conf]', '../UI/include/Config.php'),
                     'siteKey' => array('data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4')
                     );
    }
        
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $def = self::getDefaults();
        
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', $def['conf'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', $def['siteKey'][1], true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('userInterface','description'));
            
        if (!$console){    
            $text .= Design::erstelleZeile($console, Language::Get('userInterface','conf'), 'e', Design::erstelleEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true), 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');
            $text .= Design::erstelleZeile($console, Language::Get('userInterface','siteKey'), 'e', Design::erstelleEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4', true), 'v');
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

        echo Design::erstelleBlock($console, Language::Get('userInterface','title'), $text);
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        $fail = false;
        $file = $data['UI']['conf'];
        if (!file_exists(dirname(__FILE__).'/../'.$data['UI']['conf'])){ $fail = true;$error='UI-Konfigurationsdatei wurde nicht gefunden!';return null;}
        
        $text = explode("\n",file_get_contents(dirname(__FILE__).'/../'.$data['UI']['conf']));
        foreach ($text as &$tt){
            if (substr(trim($tt),0,10)==='$serverURI'){
                $tt='$serverURI'. " = '{$data['PL']['url']}';";
            } else
            if (substr(trim($tt),0,14)==='$globalSiteKey'){
                $tt='$globalSiteKey'. " = '{$data['UI']['siteKey']}';";
            }
        }
        
        
        $text = implode("\n",$text);
        if (!@file_put_contents(dirname(__FILE__).'/../'.$file,$text)){ $fail = true;$error='UI-Konfigurationsdatei, kein Schreiben möglich!';return null;} 
        return null;
    }
}
#endregion BenutzerschnittstelleEinrichten