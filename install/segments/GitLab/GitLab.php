<?php
#region GitLab
class GitLab {

    private static $initialized = false;
    public static $name = 'gitLab';
    public static $installed = false;
    public static $page = 1;
    public static $rank = 150;
    public static $enabledShow = true;
    private static $langTemplate = 'GitLab';
    public static $onEvents = array();

    public static function getDefaults() {
        return array(
            'gitLabUrl' => array('data[GITLAB][gitLabUrl]', 'http://URL.de'),
            'private_token' => array('data[GITLAB][private_token]', '<private_token>')
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
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['GITLAB']['gitLabUrl'], 'data[GITLAB][gitLabUrl]', $def['gitLabUrl'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['GITLAB']['private_token'], 'data[GITLAB][private_token]', $def['private_token'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function show($console, $result, $data) {
        // das Segment soll nur gezeichnet werden, wenn der Nutzer eingeloggt ist
        if (!Einstellungen::$accessAllowed) {
            return;
        }

        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $text = '';
        $text .= Design::erstelleBeschreibung($console, Installation::Get('gitLabIntegration', 'description', self::$langTemplate));

        if (!$console) {
            $text .= Design::erstelleZeile($console, Installation::Get('gitLabIntegration', 'gitLabUrl', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['GITLAB']['gitLabUrl'], 'data[GITLAB][gitLabUrl]', $data['GITLAB']['gitLabUrl'], true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('gitLabIntegration', 'private_token', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['GITLAB']['private_token'], 'data[GITLAB][private_token]', $data['GITLAB']['private_token'], true), 'v');
        }

        echo Design::erstelleBlock($console, Installation::Get('gitLabIntegration', 'title', self::$langTemplate), $text);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function platformSetting($data) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $result = array('LGitLab_gitLabUrl' => $data['GITLAB']['gitLabUrl'], 'LGitLab_private_token' => $data['GITLAB']['private_token']);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $result;
    }

}

#endregion Hilfe