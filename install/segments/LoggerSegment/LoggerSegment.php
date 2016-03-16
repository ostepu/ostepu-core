<?php
/**
 * @file LoggerSegment.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */

#region LoggerSegment
class LoggerSegment
{
    public static $name = 'logger';
    public static $enabledShow = true;
    private static $initialized=false;
    public static $rank = 200;
    private static $langTemplate='LoggerSegment';

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
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        if (Einstellungen::$accessAllowed){
            echo "<tr><td class='e'>".Installation::Get('logger','title',self::$langTemplate)."</td></tr>";
            foreach (self::$logLevel as $levelName => $level) {
                echo "<tr><td class='v'>".Design::erstelleAuswahl(false, $data['LOGGER']['log_level_'.$levelName], 'data[LOGGER][log_level_'.$levelName.']', 'selected', null, true).Installation::Get('logger','log_level_'.$levelName,self::$langTemplate)."</td></tr>";
            }
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        Installation::$logLevel = LogLevel::NONE;
        foreach (self::$logLevel as $levelName => $level) {
            if (isset($data['LOGGER']['log_level_'.$levelName]) && $data['LOGGER']['log_level_'.$levelName] === 'selected') Installation::$logLevel |= $level;
        }
        Installation::log(array('text'=>Installation::Get('logger','setLogLevel',self::$langTemplate,array('level'=>Installation::$logLevel))));

        $def = self::getDefaults();
        $text = '';
        foreach (self::$logLevel as $levelName => $level) {
            $text .= Design::erstelleVersteckteEingabezeile($console, $data['LOGGER']['log_level_'.$levelName], 'data[LOGGER][log_level_'.$levelName.']', '_', true);
        }
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }
}
#endregion LoggerSegment