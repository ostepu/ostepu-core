<?php
#region PruefungErweiterungen
class PruefungErweiterungen
{
    private static $initialized=false;
    public static $name = 'checkExtensions';
    public static $installed = false;
    public static $page = 0;
    public static $rank = 50;
    public static $enabledShow = true;
    
    public static $onEvents = array('check'=>array('name'=>'checkExtensions','event'=>array('actionCheckExtensions','page','install', 'update')));
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text = '';
        $text .= Design::erstelleBeschreibung($console,Language::Get('extensions','description'));
        
        if (isset($result[self::$onEvents['check']['name']]) && $result[self::$onEvents['check']['name']]!=null){
           $result =  $result[self::$onEvents['check']['name']];
        } else 
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);
        
        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];
        
        if ($content!=null){
            foreach ($content as $extensionName => $status){
                if (!$console){
                    $text .= Design::erstelleZeile($console, $extensionName, 'e', ($status ? Language::Get('main','ok') : "<font color='red'>".Language::Get('main','fail')."</font>"), 'v');
                } else 
                    $text .= $extensionName.' '.($status ? Language::Get('main','ok') : Language::Get('main','fail'))."\n";
            }
        } else 
            $text .= Design::erstelleZeile($console, "<font color='red'>".Language::Get('main','fail')."</font>", 'e');

        echo Design::erstelleBlock($console, Language::Get('extensions','title'), $text);
        return null;
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        $result = array();
        // check if php extensions are existing
        $result['curl'] = self::apache_extension_exists('curl');
        $result['mysql'] = self::apache_extension_exists('mysql');
        $result['mysqli'] = self::apache_extension_exists('mysqli');
        $result['json'] = self::apache_extension_exists('json');
        $result['mbstring'] = self::apache_extension_exists('mbstring');
        $result['openssl'] = self::apache_extension_exists('openssl');
        $result['fileinfo'] = self::apache_extension_exists('fileinfo');
        $result['sockets'] = self::apache_extension_exists('sockets');
        $result['gd'] = self::apache_extension_exists('gd');
        return $result;
    }
    
    public static function apache_extension_exists($extension)
    {
        if (!function_exists('extension_loaded')) return false;
        return extension_loaded($extension);
    }
}
#endregion PruefungErweiterungen