<?php
#region LoggerSegment
class LoggerSegment
{
    public static $name = 'login';
    public static $enabledShow = true;
    private static $initialized=false;
    public static $rank = 200;

    public static function getDefaults()
    {
        return array(
                     'log_level_info' => array('data[LOGGER][log_level_info]', null),
                     'log_level_warning' => array('data[LOGGER][log_level_warning]', null),
                     'log_level_error' => array('data[LOGGER][log_level_error]', null)
                     );
    }
    
    public static function showInfoBar(&$data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        if (Einstellungen::$accessAllowed){
            echo "<tr><td class='e'>".Language::Get('logger','title')."</td></tr>";
            echo "<tr><td class='v'>".Language::Get('logger','log_level_info').Design::erstelleAuswahl(false, $data['LOGGER']['log_level_info'], 'data[LOGGER][log_level_info]', 'selected', null, true)."</td></tr>";
            echo "<tr><td class='v'>".Language::Get('logger','log_level_warning').Design::erstelleAuswahl(false, $data['LOGGER']['log_level_warning'], 'data[LOGGER][log_level_warning]', 'selected', null, true)."</td></tr>";
            echo "<tr><td class='v'>".Language::Get('logger','log_level_error').Design::erstelleAuswahl(false, $data['LOGGER']['log_level_error'], 'data[LOGGER][log_level_error]', 'selected', null, true)."</td></tr>";
        }
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        Installation::$logLevel = LogLevel::NONE;
        if (isset($data['LOGGER']['log_level_info']) && $data['LOGGER']['log_level_info'] === 'selected') Installation::$logLevel |= LogLevel::INFO;
        if (isset($data['LOGGER']['log_level_warning']) && $data['LOGGER']['log_level_warning'] === 'selected') Installation::$logLevel |= LogLevel::WARNING;
        if (isset($data['LOGGER']['log_level_error']) && $data['LOGGER']['log_level_error'] === 'selected') Installation::$logLevel |= LogLevel::ERROR;
   
        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['LOGGER']['log_level_info'], 'data[LOGGER][log_level_info]', $def['log_level_info'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['LOGGER']['log_level_warning'], 'data[LOGGER][log_level_warning]', $def['log_level_warning'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['LOGGER']['log_level_error'], 'data[LOGGER][log_level_error]', $def['log_level_error'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }
}
#endregion LoggerSegment