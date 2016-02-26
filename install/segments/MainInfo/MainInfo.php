<?php
#region MainInfo
class MainInfo
{
    public static $name = 'MainInfo';
    public static $installed = false; // ob die Installationsroutine ausgelöst wurde
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 0; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
                              // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktion aufrufbar ist
    private static $langTemplate='MainInfo';

    public static $onEvents = array(
                                    '0' =>array(
                                                     'name'=>'MainInfo',
                                                     'event'=>array(
                                                                    'actionInstallMain', // vom Segment selbst gewaehlter Ausloeser
                                                                    'install', // bei der Installation
                                                                    'update', // bei einer Aktualisierung
                                                                    'page'), // beim Seitenaufruf
                                                     'procedure'=>'install' // die im Installationsfall aufzurufende Funktion
                                                     )
                                    );

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }
  
    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;
          
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text='';
        $failure=false;
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Installation::log(array('text'=>Installation::Get('mainInfo','checkPath',self::$langTemplate,array('path'=>Einstellungen::$path))));
        Installation::log(array('text'=>Installation::Get('mainInfo','checkFile',self::$langTemplate,array('file'=>__FILE__))));
        if (!is_dir(Einstellungen::$path) || !is_writable(__FILE__)) {
            $text .= Design::erstelleZeile($console, Installation::Get('mainInfo','notWritable',self::$langTemplate), 'error');
            $failure = true;
            Installation::log(array('text'=>Installation::Get('mainInfo','noWritePermission',self::$langTemplate), 'logLevel'=>LogLevel::ERROR));
        } else {
            Installation::log(array('text'=>Installation::Get('mainInfo','positiveResult',self::$langTemplate)));
        }

        if ($failure) {
            echo Design::erstelleBlock($console, Installation::Get('mainInfo','title',self::$langTemplate), $text);
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }
}
#endregion MainInfo