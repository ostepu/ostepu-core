<?php
#region LoggerSegment
class LoggerSegment
{
    public static $name = 'logger';
    public static $enabledShow = true;
    private static $initialized=false;
    public static $rank = 200;
    
    private static $logLevel = array('info'=>LogLevel::INFO,'warning'=>LogLevel::WARNING,'error'=>LogLevel::ERROR);

    public static function getDefaults()
    {
        $res = array();
        foreach (self::$logLevel as $levelName => $level)
            $res['log_level_'.$levelName] = array('data[LOGGER][log_level_'.$levelName.']', null);
        return $res;
    }
    
    public static function showInfoBar(&$data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        if (Einstellungen::$accessAllowed){
            echo "<tr><td class='e'>".Language::Get('logger','title')."</td></tr>";
            foreach (self::$logLevel as $levelName => $level) {
                echo "<tr><td class='v'>".Design::erstelleAuswahl(false, $data['LOGGER']['log_level_'.$levelName], 'data[LOGGER][log_level_'.$levelName.']', 'selected', null, true).Language::Get('logger','log_level_'.$levelName)."</td></tr>";
            }
        }
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        Installation::$logLevel = LogLevel::NONE;
        foreach (self::$logLevel as $levelName => $level) {
            if (isset($data['LOGGER']['log_level_'.$levelName]) && $data['LOGGER']['log_level_'.$levelName] === 'selected') Installation::$logLevel |= $level;
        }
   
        $def = self::getDefaults();

        $text = '';
        foreach (self::$logLevel as $levelName => $level) {
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['LOGGER']['log_level_'.$levelName], 'data[LOGGER][log_level_'.$levelName.']', $def['log_level_'.$levelName][1], true);
        }
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }
}
#endregion LoggerSegment