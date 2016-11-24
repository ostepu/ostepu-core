<?php

/**
 * @file Komponenten.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */
#region Komponenten
class Komponenten {

    private static $initialized = false;
    public static $name = 'initComponents';
    public static $installed = false;
    public static $page = 3;
    public static $rank = 75;
    public static $enabledShow = true;
    public static $enabledInstall = true;
    private static $langTemplate = 'Komponenten';
    public static $onEvents = array('install' => array('name' => 'initComponents', 'event' => array('actionInitComponents', 'install', 'update')));

    public static function getDefaults() {
        return array(
            'co_details' => array('data[CO][co_details]', null)
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
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_details'], 'data[CO][co_details]', $def['co_details'][1], true);
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
        $isUpdate = (isset($data['action']) && $data['action'] == 'update') ? true : false;

        $text = '';
        if (!$console) {
            $text .= Design::erstelleBeschreibung($console, Installation::Get('components', 'description', self::$langTemplate));

            $text .= Design::erstelleZeile($console, Installation::Get('components', 'init', self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');
            $text .= Design::erstelleZeile($console, Installation::Get('components', 'details', self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['CO']['co_details'], 'data[CO][co_details]', 'details', null, true), 'v_c');
        }

        if (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']] != null) {
            $result = $result[self::$onEvents['install']['name']];
        } else {
            $result = array('content' => null, 'fail' => false, 'errno' => null, 'error' => null);
        }

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];

        if (self::$installed) {
            // counts installed commands
            $installedCommands = 0;

            // counts installed components
            $installedComponents = 0;

            // counts installed links
            $installedLinks = 0;

            foreach ($content as $componentName => $component) {
                if (isset($component['init'])) {
                    $content[$componentName]['init'] = Component::decodeComponent(json_encode($component['init']));
                }

                if (isset($component['links'])) {
                    $content[$componentName]['links'] = Link::decodeLink(json_encode($component['links']));
                }

                if (isset($component['commands'])) {
                    if (in_array("Slim\\Slim", get_declared_classes())) {
                        $router = new \Slim\Router();
                        foreach ($component['commands'] as $command) {
                            $route = new \Slim\Route($command['path'], 'is_array');  // is_array wird hier benötigt, weil Route eine Funktion die er auf callable prüfen kann
                            $route->via((isset($command['method']) ? strtoupper($command['method']) : 'GET'));
                            $router->map($route);
                        }
                        $content[$componentName]['router'] = $router;
                    } else {
                        Installation::log(array('text' => Installation::Get('components', 'noSlim', self::$langTemplate)));
                    }
                }
            }

            foreach ($content as $componentName => $component) {
                $linkNames = array();
                $linkNamesUnique = array();
                $callNames = array();

                $links = array();
                if (isset($component['links'])) {
                    $links = $component['links'];
                }

                foreach ($links as $link) {
                    $linkNames[] = $link->getName();
                    $linkNamesUnique[$link->getName()] = $link->getName();
                }

                $calls = null;
                if (isset($component['call'])) {
                    $calls = $component['call'];
                }
                if ($calls !== null) {
                    foreach ($calls as $pos => $callList) {
                        if (isset($callList['name'])) {
                            $callNames[$callList['name']] = $callList['name'];
                        }
                    }
                }

                if (isset($component['init']) && $component['init'] !== null && $component['init']->getStatus() === 201) {
                    //    $countLinks+=count($linkNames) + count(array_diff($callNames,$linkNamesUnique)) + count($linkNamesUnique) - count(array_diff($linkNamesUnique,$callNames));
                    //    $countLinks++;
                } else {
                    if (!isset($component['init']) || $component['init'] === null) {
                        $fail = true;
                        $error = Installation::Get('components', 'componentCrashed', self::$langTemplate);
                    }
                }

                $countCommands = count(isset($component['commands']) ? $component['commands'] : array());
                $tempText = '';
                $countLinks = 1;
                $tempTextList = array();

                if (isset($component['init']) && $component['init'] !== null && $component['init']->getStatus() === 201) {
                    $installedComponents++;
                    $installedLinks += count(isset($component['links']) ? $component['links'] : array());
                    $installedCommands += $countCommands;

                    if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate) {
                        $tempTextList[] = "<tr><td class='v' colspan='2'>" . Installation::Get('components', 'installedCalls', self::$langTemplate) . ": {$countCommands}</td></tr>";
                    }

                    $links = array();
                    if (isset($component['links'])) {
                        $links = $component['links'];
                    }
                    $lastLink = null;

                    foreach ($links as $link) {
                        $calls = null;
                        if (isset($component['call'])) {
                            $calls = $component['call'];
                        }
                        $linkFound = false;
                        if ($calls !== null) {
                            foreach ($calls as $pos => $callList) {
                                if (isset($callList['name']) && $callList['name'] === $link->getName()) {
                                    $linkFound = true;
                                    break;
                                }
                            }
                        }

                        if ($lastLink != $link->getName() && $linkFound) {
                            $calls = null;
                            if (isset($component['call'])) {
                                $calls = $component['call'];
                            }

                            $notRoutable = false;
                            if ($calls !== null) {
                                $errorMessage = '';
                                foreach ($calls as $pos => $callList) {
                                    if ($link->getName() !== $callList['name']) {
                                        continue;
                                    }
                                    if (isset($callList['links']) && $callList['links'] !== null) {
                                        foreach ($callList['links'] as $pos2 => $call) {
                                            if (!isset($content[$link->getTargetName()]['router'])) {
                                                Installation::log(array('text' => Installation::Get('components', 'unknownComponent', self::$langTemplate, array('component' => $link->getTargetName()))));
                                                $notRoutable = true;
                                                $errorMessage = Installation::Get('components', 'notRoutable', self::$langTemplate);
                                                break;
                                            }
                                            if ($content[$link->getTargetName()]['router'] == null) {
                                                continue;
                                            }
                                            if ($call === null) {
                                                continue;
                                            }
                                            if (!isset($call['method'])) {
                                                continue;
                                            }
                                            if (!isset($call['path'])) {
                                                continue;
                                            }

                                            $routes = count($content[$link->getTargetName()]['router']->getMatchedRoutes(strtoupper($call['method']), $call['path']), true);
                                            if ($routes === 0) {
                                                Installation::log(array('text' => Installation::Get('components', 'callIsNotSupported', self::$langTemplate, array('component' => $link->getTargetName(), 'call' => strtoupper($call['method']) . ' ' . $call['path']))));
                                                $errorMessage = Installation::Get('components', 'notRoutable2', self::$langTemplate, array('component' => $link->getTargetName(), 'method' => strtoupper($call['method']), 'path' => $call['path']));
                                                $notRoutable = true;
                                                break;
                                            }
                                        }
                                    }
                                    if ($notRoutable) {
                                        $fail = true;
                                        break;
                                    }
                                }

                                if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate) {
                                    if ($notRoutable) {
                                        $tempTextList[] = "<tr><td class='v'>{$link->getName()}</td><td class='e'><div align ='center'>" . (!$notRoutable ? Installation::Get('main', 'ok') : '<font color="red">' . $errorMessage . '</font>') . "</align></td></tr>";
                                    }
                                }
                            }
                        }

                        if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate) {
                            if (!$linkFound) {
                                $tempTextList[] = "<tr><td class='v'>{$link->getName()}" . " (<font color='red'>" . Installation::Get('components', 'unknown', self::$langTemplate) . "</font>)" . "</td><td class='v'>{$link->getTargetName()}</td></tr>";
                            } else {
                                //"<tr><td class='v'>{$link->getName()}</td><td class='v'>{$link->getTargetName()}</td></tr>";
                                $tempTextList[] = array(array($link->getName()), array($link->getTargetName()), 1);
                            }
                        }

                        $lastLink = $link->getName();
                    }

                    // fehlende links
                    $calls = null;
                    if (isset($component['call'])) {
                        $calls = $component['call'];
                    }

                    if ($calls !== null) {
                        foreach ($calls as $pos => $callList) {
                            $found = false;
                            foreach ($links as $link) {
                                if ($link->getName() == $callList['name']) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate) {
                                    $tempTextList[] = "<tr><td class='v'>{$callList['name']}</td><td class='e'><font color='red'>" . Installation::Get('components', 'unallocated', self::$langTemplate) . "</font></td></tr>";
                                }
                            }
                        }
                    }
                }

                if (isset($component['init']) && isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate) {
                    $max = count($tempTextList);
                    for ($b = 0; $b < $max; $b++) {
                        if (!isset($tempTextList[$b])) {
                            continue;
                        }
                        $part = $tempTextList[$b];
                        if (is_array($part)) {
                            // eine Zeile mit einer Kante, diese soll eventuell mit anderen zusammegefasst werden
                            for ($i = $b + 1; $i < $max; $i++) {
                                if (!isset($tempTextList[$i])) {
                                    continue;
                                }
                                // ermittelt doppelte Zeilen und fasst diese zusammen
                                $elem = $tempTextList[$i];
                                if (!is_array($elem)) {
                                    break;
                                }
                                if ($elem[0] === $part[0] && $elem[1] === $part[1]) {
                                    $part[2] ++;
                                    unset($tempTextList[$i]);
                                }
                            }

                            for ($i = $b + 1; $i < $max; $i++) {
                                if (!isset($tempTextList[$i])) {
                                    continue;
                                }
                                // ermittelt doppelte Ziele in den Zeilen und fasst die Quellen zusammen
                                $elem = $tempTextList[$i];
                                if (!is_array($elem)) {
                                    break;
                                }
                                if ($elem[1] === $part[1] && $elem[2] === 1 && $part[2] === 1) {
                                    $part[0] = array_merge($part[0], $elem[0]);
                                    unset($tempTextList[$i]);
                                }
                            }

                            $tempText .= "<tr><td class='v'>" . implode(', ', $part[0]) . ($part[2] <= 1 ? '' : ' (' . $part[2] . ')') . "</td><td class='v'>" . implode(', ', $part[1]) . "</td></tr>";
                            $countLinks++;
                        } else {
                            // fertige Meldung
                            $tempText .= $part;
                            $countLinks++;
                        }
                    }

                    $defs = explode(";", $component['init']->getDef());
                    $baseComponent = (count($defs) > 2 ? "<br><span class='info-color tiny'>(" . $defs[0] . ")</span>" : '');
                    $text .= "<col width='20%'><col width='60%'><col width='20%'>";
                    $text .= "<tr><td class='e' rowspan='{$countLinks}'>{$componentName}{$baseComponent}</td><td class='v'>{$component['init']->getAddress()}</td><td class='e'><div align ='center'>" . ($component['init']->getStatus() === 201 ? Installation::Get('main', 'ok') : "<font color='red'>" . Installation::Get('main', 'fail') . " ({$component['init']->getStatus()})</font>") . "</align></td></tr>";
                    $text .= $tempText;
                }
            }


            if ($installedComponents == 0) {
                $fail = true;
                $error = Installation::Get('components', 'noComponents', self::$langTemplate);
            } else if ($installedLinks == 0) {
                $fail = true;
                $error = Installation::Get('components', 'noLinks', self::$langTemplate);
            } else if ($installedCommands == 0) {
                $fail = true;
                $error = Installation::Get('components', 'noCommands', self::$langTemplate);
            }

            if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate) {
                $text .= Design::erstelleZeile($console, '', '', '', '', '', '');
            }

            $text .= Design::erstelleZeile($console, Installation::Get('components', 'installedComponents', self::$langTemplate), 'e', '', 'v', $installedComponents, 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('components', 'installedLinks', self::$langTemplate), 'e', '', 'v', $installedLinks, 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('components', 'installedCommands', self::$langTemplate), 'e', '', 'v', $installedCommands, 'v');

            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Installation::Get('components', 'title', self::$langTemplate), $text);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function install($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        
        // er soll so lange rechnen können, wie er benötigt (Achtung!!!)
        set_time_limit(0);
        
        $fail = false;
        $url = $data['PL']['init'];
        $components = array();

        // inits all components
        Installation::log(array('text' => Installation::Get('components', 'createQueryInitComponents', self::$langTemplate, array('url' => 'GET ' . $data['PL']['url'] . '/' . $url . '/definition/send'))));
        $result = Request::get($data['PL']['url'] . '/' . $url . '/definition/send', array(), '');
        Installation::log(array('text' => Installation::Get('components', 'queryInitComponentsResult', self::$langTemplate, array('res' => json_encode($result)))));

        if (isset($result['content']) && isset($result['status'])) {

            // component routers
            $router = array();

            $results = Installation::orderBy(json_decode($result['content'], true), 'name', SORT_ASC);
            $results = Component::decodeComponent(json_encode($results));

            if (!is_array($results)) {
                $results = array($results);
            }

            if (count($results) == 0) {
                $fail = true;
                $error = Installation::Get('components', 'noComponents', self::$langTemplate);
            }

            foreach ($results as $res) {
                $components[$res->getName()] = array();
                $components[$res->getName()]['init'] = $res;
            }

            // get component definitions from database
            Installation::log(array('text' => Installation::Get('components', 'createQueryGetDefinition', self::$langTemplate, array('url' => 'GET ' . $data['PL']['url'] . '/' . $url . '/definition'))));
            $result4 = Request::get($data['PL']['url'] . '/' . $url . '/definition', array(), '');
            Installation::log(array('text' => Installation::Get('components', 'queryGetDefinitionResult', self::$langTemplate, array('res' => json_encode($result4)))));

            if (isset($result4['content']) && isset($result4['status']) && $result4['status'] === 200) {
                $definitions = Component::decodeComponent($result4['content']);
                if (!is_array($definitions)) {
                    $definitions = array($definitions);
                }

                if (count($definitions) == 0) {
                    $fail = true;
                    $error = Installation::Get('components', 'noDefinitions', self::$langTemplate);
                }

                $result2 = new Request_MultiRequest();
                $result3 = new Request_MultiRequest();
                $tempDef = array();
                foreach ($definitions as $definition) {
                    if (strpos($definition->getAddress() . '/', $data['PL']['urlExtern'] . '/') === false) {
                        continue;
                    }

                    $components[$definition->getName()]['definition'] = $definition;
                    $tempDef[] = $definition;

                    Installation::log(array('text' => Installation::Get('components', 'createQueryGetCommands', self::$langTemplate, array('url' => 'GET ' . $definition->getAddress() . '/info/commands'))));
                    $request = Request_CreateRequest::createGet($definition->getAddress() . '/info/commands', array(), '');
                    $result2->addRequest($request);
                    Installation::log(array('text' => Installation::Get('components', 'createQueryGetLinks', self::$langTemplate, array('url' => 'GET ' . $definition->getAddress() . '/info/links'))));
                    $request = Request_CreateRequest::createGet($definition->getAddress() . '/info/links', array(), '');
                    $result3->addRequest($request);
                }
                $definitions = $tempDef;

                $result2 = $result2->run();
                $result3 = $result3->run();
                Installation::log(array('text' => Installation::Get('components', 'queryGetCommandsResult', self::$langTemplate, array('res' => json_encode($result2)))));
                Installation::log(array('text' => Installation::Get('components', 'queryGetLinksResult', self::$langTemplate, array('res' => json_encode($result3)))));


                foreach ($results as $res) {
                    if ($res === null) {
                        $fail = true;
                        continue;
                    }

                    $countLinks = 0;
                    $resultCounter = -1;
                    foreach ($definitions as $definition) {
                        //if (strpos($definition->getAddress().'/', $data['PL']['urlExtern'].'/')===false) continue;

                        $resultCounter++;
                        if ($definition->getId() === $res->getId()) {

                            $links = $definition->getLinks();
                            $links = Installation::orderBy(json_decode(Link::encodeLink($links), true), 'name', SORT_ASC);
                            $links = Link::decodeLink(json_encode($links));
                            if (!is_array($links)) {
                                $links = array($links);
                            }

                            $components[$definition->getName()]['links'] = $links;

                            if (isset($result2[$resultCounter]['content']) && isset($result2[$resultCounter]['status']) && $result2[$resultCounter]['status'] === 200) {
                                $commands = json_decode($result2[$resultCounter]['content'], true);
                                if ($commands !== null) {
                                    $components[$definition->getName()]['commands'] = $commands;
                                }
                            }

                            if (isset($result3[$resultCounter]['content']) && isset($result3[$resultCounter]['status']) && $result3[$resultCounter]['status'] === 200) {
                                $calls = json_decode($result3[$resultCounter]['content'], true);
                                $components[$definition->getName()]['call'] = $calls;
                            }

                            break;
                        }
                    }

                    if ($res->getStatus() !== 201) {
                        $fail = true;
                    }
                }
            } else {
                $fail = true;
                $error = Installation::Get('components', 'noDefinitions', self::$langTemplate);
                Installation::log(array('text' => Installation::Get('components', 'failure', self::$langTemplate, array('message' => $error)), 'logLevel' => LogLevel::ERROR));
            }
        } else {
            $fail = true;
            $error = Installation::Get('components', 'operationFailed', self::$langTemplate);
            Installation::log(array('text' => Installation::Get('components', 'failure', self::$langTemplate, array('message' => $error)), 'logLevel' => LogLevel::ERROR));
        }

        if (isset($result['status']) && $result['status'] !== 200) {
            $fail = true;
            $error = Installation::Get('components', 'operationFailed', self::$langTemplate);
            $errno = $result['status'];
            Installation::log(array('text' => Installation::Get('components', 'failure2', self::$langTemplate, array('message' => $error, 'status' => $errno)), 'logLevel' => LogLevel::ERROR));
        }

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $components;
    }

}

#endregion Komponenten