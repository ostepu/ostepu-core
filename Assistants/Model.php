<?php
/**
 * @file Model.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */

require_once ( dirname(__FILE__) . '/vendor/Slim/Slim/Route.php' );
require_once ( dirname(__FILE__) . '/vendor/Slim/Slim/Router.php' );
require_once ( dirname(__FILE__) . '/vendor/Slim/Slim/Environment.php' );
include_once ( dirname(__FILE__) . '/Structures.php' );
include_once ( dirname(__FILE__) . '/Request.php' );
include_once ( dirname(__FILE__) . '/Logger.php' );
include_once ( dirname(__FILE__) . '/CConfig.php' );
include_once ( dirname(__FILE__) . '/DBRequest.php' );
include_once ( dirname(__FILE__) . '/DBJson.php' );

class Model
{

    /**
     * @var string $_path Der lokale Pfad des Moduls
     */
    private $_path=null;

    /**
     * @var string $_prefix Unterstützte Präfixe (veraltet)
     */
    private $_prefix=null;

    /**
     * @var Component $_conf the component data object
     */
    public $_conf = null;

    /**
     * @var string $_class Der Klassenname des Moduls
     */
    private $_class = null;

    /**
     * @var Component $_com Die Definition der Ausgänge
     */
    private $_com = null;


    private $_noInfo = null;
    private $_noHelp = null;
    
    private $_options = array();
    private $_default = array('cloneable'=>false,
                              'addOptionsToParameters'=>false,
                              'addOptionsToParametersAsPostfix'=>false,
                              'addProfileToParameters'=>false,
                              'addProfileToParametersAsPostfix'=>false,
                              'defaultParams' => array());

    /**
     * Der Konstruktor
     *
     * @param string Unterstützte Präfixe (veraltet)
     * @param string Der lokale Pfade des Moduls
     * @param string Der Klassenname des Moduls
     */
     // options:
     // cloneable: true|false
    public function __construct( $prefix, $path, $class, $noInfo = false, $noHelp = false, $options = array())
    {
        $this->_path=$path;
        $this->_prefix=$prefix;
        $this->_class=$class;
        $this->_noInfo=$noInfo;
        $this->_noHelp=$noHelp;
        $this->_options = $options;
    }
    
    public function getOption($name)
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        }
        return $this->_default[$name];
    }

    /**
     * Führt das Modul entsprechend der Commands.json und Component.json Definitionen aus
     */
    public function run()
    {
        // runs the CConfig
        $com = new CConfig( $this->_prefix, $this->_path, $this->_noInfo, $this->_noHelp, 'de', array('getAndHead'=>true, 'allowOptions'=>true) );

        // lädt die Konfiguration des Moduls
        if ($com->used()) {
            return;
        }
        ///var_dump($conf);
        $this->_com=$com;
        $commands = $com->commands(array(),true,true);

        // multi Requests werden noch nicht unterstützt, das Model soll automatisch die Möglichkeit bieten,
        // mehrere Anfragen mit einmal zu beantworten
        ////$commands[] = array('name' => 'postMultiGetRequest','method' => 'POST', 'path' => '/multiGetRequest', 'inputType' => 'Link', 'outputType' => '');

        // Erstellt für jeden angebotenen Befehl einen Router
        // Ein Router stellt einen erlaubten Aufruf dar (mit Methode und URI), sodass geprüft werden kann,
        // welcher für die Beantwortung zuständig ist
        $router = new \Slim\Router();
        foreach ($commands as $key => $command){
            if (!isset($command['name'])) {
                continue;
            }
            if (!isset($command['method'])) {
                $commands[$key]['method'] = 'GET';
            }
            if (!isset($command['callback'])) {
                $commands[$key]['callback'] = $command['name'];
            }
            if (!isset($command['seqInput'])) {
                $commands[$key]['seqInput'] = 'TRUE';
            }
            if (!isset($command['singleOutput'])) {
                $commands[$key]['singleOutput'] = 'FALSE';
            }
            if (!isset($command['placeholder'])) {
                $commands[$key]['placeholder'] = array();
            }
            $command = $commands[$key];
            
            // Methoden können durch Komma getrennt aufgelistet sein
            $methods = explode(',',$command['method']);

            foreach ($methods as $method){
                // wenn das Modul auch als clone verwendet werden soll, müssen die Aufrufe erweitert werden
                $cloneAdd = '';
                if ($this->getOption('cloneable')){
                    $cloneAdd = '(/profile/:profileName)';
                }
                
                $route = new \Slim\Route($cloneAdd.$command['path'],array($this->_class,$command['callback']),false);
                $route->via(strtoupper($method));
                $route->setName($command['name']);
                $router->map($route);

                // wenn es ein GET Befehl ist, wird automatisch HEAD unterstützt
                if (strtoupper($method)=='GET'){
                    // erzeugt einen HEAD Router
                    $route = new \Slim\Route($cloneAdd.$command['path'],array($this->_class,$command['callback']),false);
                    $route->via('HEAD');
                    $route->setName($command['name']);
                    $router->map($route);
                }
            }
        }

        // hier wird die eingehende URI erzeugt
        // Bsp.: /user/1
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = str_replace('?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''), '', substr_replace($requestUri, '', 0, strlen((strpos($requestUri, $scriptName) !== false ? $scriptName : str_replace('\\', '', dirname($scriptName))))));

        // ermittelt den zuständigen Befehl
        $matches = $router->getMatchedRoutes(strtoupper($_SERVER['REQUEST_METHOD']), $path);

        if (count($matches)>0){
            // mindestens ein zutreffender Befehl wurde gefunden (nimm den Ersten)
            $matches = $matches[0];

            // suche den zugehörigen $commands Eintrag
            $selectedCommand=null;
            foreach ($commands as $command){
                if ($command['name'] === $matches->getName()){
                    $selectedCommand = $command;
                    break;
                }
            }

            if ($selectedCommand == null){
                http_response_code(500);
                return;
            }

            // lies die eingehenden PHP Daten
            $rawInput = \Slim\Environment::getInstance()->offsetGet('slim.input');
            if (!$rawInput) {
                $rawInput = @file_get_contents('php://input');
            }

            // wir wollen wissen, ob die Eingabedaten als Liste rein kommen
            $arr = true;

            // wenn zu diesem Befehl ein inputType angegeben wurde, wird eine Type::decodeType() aufgerufen
            if (isset($selectedCommand['inputType']) && trim($selectedCommand['inputType'])!=''){
                $inputType = $selectedCommand['inputType'];
                $rawInput = call_user_func_array('\\'.$inputType.'::decode'.$inputType, array($rawInput));

                if ( !is_array( $rawInput ) ){
                    // es ist keine Liste, also mach eine daraus (damit man sie einheitlich behandeln kann)
                    $rawInput = array( $rawInput );
                    $arr = false;
                }
            }

            if (strtoupper($selectedCommand['seqInput']) != 'TRUE') {
                $arr = false;
            }

            $params = $matches->getParams();
            
            $placeholder = array('profileName'=>'%^([a-zA-Z0-9_]*)$%'); // profileName soll geprueft werden
            // prüfe die Bedingungen für die Platzhalter
            foreach ($selectedCommand['placeholder'] as $holder){
                if (!isset($holder['name'])) {
                    // der Eintrag muss sich auf einen Platzhalter beziehen
                    continue;
                }
                if (!isset($holder['regex'])) {
                    // der Eintrag muss einen regulären Ausdruck besitzen, der 
                    // getestet werden kann
                    continue;
                }
                $placeholder[$holder['name']] = $holder['regex'];
            }

            // hier werden die eigentlichen Bedingungen der Platzhalter geprüft
            foreach ($params as $key => $value){
                if (isset($placeholder[$key])){
                    if (is_array($value)){
                        // wenn es ein Array ist, wurde ein :Element+ verwendet (Slim)
                        // daher wird der Ausdruck auf jedes Element angewendet
                        foreach($value as $val){
                            $pregRes = @preg_match($placeholder[$key], $val);
                            if ($pregRes === false){
                                error_log(__FILE__.':'.__LINE__.' '.$placeholder[$key].' konnte nicht interpretiert werden');
                                $this->finishRequest(self::isError());
                                return;
                            } else if ($pregRes === 0){
                                error_log(__FILE__.':'.__LINE__.' '.$val.' passt nicht zu '.$placeholder[$key]);
                                $this->finishRequest(self::isPreconditionError());
                                return;
                            }
                        }
                    } else {
                        // einzelnes Element für Slim verwendet :Element
                        $pregRes = @preg_match($placeholder[$key], $value);
                        if ($pregRes === false){
                            error_log(__FILE__.':'.__LINE__.' '.$placeholder[$key].' konnte nicht interpretiert werden');
                            $this->finishRequest(self::isError());
                            return;
                        } else if ($pregRes === 0){
                            error_log(__FILE__.':'.__LINE__.' '.$value.' passt nicht zu '.$placeholder[$key]);
                            $this->finishRequest(self::isPreconditionError());
                            return;
                        }
                    }
                }
            }

            // der Befehl wurde nun bestimmt, sodass wir jetzt den Rest der Komponente laden koennen
            if ($this->getOption('cloneable') && isset($params['profileName'])){
                $conf = $com->loadConfig( $params['profileName'] );
            } else {
                $conf = $com->loadConfig( );
            }
            $this->_conf=$conf;
            
            $params = array_merge($params, $this->getOption('defaultParams'));
            
            
            if ($this->getOption('cloneable')){
                // fügt profileName der Komponente den Ausfuehrungsparametern hinzu
                if (isset($params['profileName'])){
                    $params['profile'] = $params['profileName'];
                } else {
                    $params['profile'] = '';                       
                }
                unset($options);
            }
            
            if ($this->getOption('addOptionsToParametersAsPostfix')){
                // fügt die Options der Komponente den Ausfuehrungsparametern hinzu
                $options = $this->extractComponentOptions();
                if (isset($options) && is_array($options)){
                    $params = array_merge($options, $params);
                    foreach($options as $key => $value){
                        Model::generatePostfix(array($key=>$key), $params);
                    }
                }
                unset($options);
            }
            
            if ($this->getOption('addOptionsToParameters')){
                // fügt die Options der Komponente den Ausfuehrungsparametern hinzu
                $options = $this->extractComponentOptions();
                if (isset($options) && is_array($options)){
                    $params = array_merge($options, $params);
                }
                unset($options);
            }
            
            if ($this->getOption('addProfileToParametersAsPostfix')){
                if ($this->getOption('cloneable')){
                    // fügt profileName der Komponente den Ausfuehrungsparametern hinzu
                    Model::generatePostfix(array('profileName'=>'profile'), $params);
                    unset($options);
                }
            }

            // nun soll die zugehörige Funktion im Modul aufgerufen werden
            if (isset($selectedCommand['inputType']) && trim($selectedCommand['inputType'])!='' && isset($rawInput)){
                // initialisiert die Ausgabe positiv
                $result=array("status"=>201,"content"=>array());

                if (strtoupper($selectedCommand['seqInput']) == 'TRUE'){
                    try {
                        // für jede Eingabe wird die Funktion ausgeführt
                        foreach($rawInput as $input){

                            // Aufruf der Modulfunktion
                            $res = call_user_func_array($matches->getCallable(), array($selectedCommand['name'],"input"=>$input,$params));

                            // wenn es ein Ausgabeobjekt gibt, wird versucht dort einen Status zu setzen
                            if (is_callable(array($res['content'],'setStatus'))){
                                $res['content']->setStatus($res['status']);
                            }

                            // setze Status und Ausgabe
                            $result["content"][] = $res['content'];
                            if (isset($res['status'])){
                                $result["status"] = $res['status'];
                            }
                        }
                    } catch(Exception $e) {
                        header_remove();
                        error_log($e->getMessage());
                        $this->finishRequest(self::isError());
                        return;
                    }

                } else {
                    try {
                        // Aufruf der Modulfunktion
                        $res = call_user_func_array($matches->getCallable(), array($selectedCommand['name'],"input"=>$rawInput,$params));

                        // wenn es ein Ausgabeobjekt gibt, wird versucht dort einen Status zu setzen
                        if (is_callable(array($res['content'],'setStatus'))){
                            $res['content']->setStatus($res['status']);
                        }

                        // setze Status und Ausgabe
                        $result["content"] = $res['content'];
                        if (isset($res['status'])){
                            $result["status"] = $res['status'];
                        }
                    } catch(Exception $e) {
                        header_remove();
                        error_log($e->getMessage());
                        $this->finishRequest(self::isError());
                        return;
                    }
                }

            } else {
                // wenn keinen vorgegebenen Eingabetyp gibt, wird die Eingabe direkt an die Modulfunktion weitergegeben
                $result = call_user_func_array($matches->getCallable(), array($selectedCommand['name'],"input"=>$rawInput,$params));
            }

            if ($selectedCommand['method']=='HEAD'){
                // Bei einer HEAD Funktion (die eventuell im Modul als GET bearbeitet wird),
                // kann die Ausgabe verworfen werden
                $result['content'] = '';
            } elseif (isset($selectedCommand['outputType']) && trim($selectedCommand['outputType'])!='' && trim($selectedCommand['outputType'])!='binary'){
                // wenn ein Ausgabetyp angegeben ist, wird eine Typ::encodeTyp() ausgeführt
                $outputType = $selectedCommand['outputType'];

                if (isset( $result['content']) ){
                    if ( !is_array( $result['content'] ) ){
                        $result['content'] = array( $result['content'] );
                    }

                    if ( $command['singleOutput']!=='FALSE' && count( $result['content'] ) >= 1 ){
                        $result['content'] = $result['content'][0];
                    } elseif ( !$arr && count( $result['content'] ) == 1 ){
                        $result['content'] = $result['content'][0];
                    }

                    $result['content'] = call_user_func_array('\\'.$outputType.'::encode'.$outputType, array($result['content']));
                }
                header('Content-Type: application/json');
            } elseif (isset($selectedCommand['outputType']) && trim($selectedCommand['outputType'])=='binary'){
                // wenn der Ausgabetyp "binär" ist, erfolgt keine Anpassung
            } else {
                // selbst wenn nichts zutrifft, wird json kodiert
                if (isset($result['content'])) {
                    $result['content'] = json_encode($result['content']);
                }
                header('Content-Type: application/json');
            }
        } else {
            // es wurde kein zutreffender Befehl gefunden, also gibt es eine leere Antwort
            $this->finishRequest(self::isError());
            return;
        }

        // ab hier werden die Ergebnisse ausgegeben
        $this->finishRequest($result);
    }

    private function finishRequest($result = array('content'=>'', 'status'=>200, 'statusText'=>null))
    {

        $code = 0;
        if (isset( $result['status']) ){
            $code = $result['status'];
        } else {
            $code = 200;
        }

        $statusText = null;
        if (isset($result['statusText'])) {
            $statusText = ' ' . $result['statusText'];
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . $code . (isset($statusText)?$statusText:''));

        if (isset($result['content'])) {
            echo $result['content'];
        }
    }
    public function getLinks($linkName)
    {
        return CConfig::getLinks($this->_conf->getLinks( ),$linkName);
    }
    
    public function callAll($linkName, $params, $body, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $returnType=null)
    {
        $links=$this->getLinks($linkName);
        $instructions = $this->_com->instruction('',true);

        // ermittle den zutreffenden Ausgang
        $selectedInstruction=null;
        foreach($instructions as $instruction){
            if ($instruction['name']==$linkName){
                $selectedInstruction =$instruction;
            }
        }

        $result = array();
        
        foreach($links as $key => $link){
            $method = 'GET';
            if ($link->getPath()!==null && $link->getPath()!==''){
                $order = $link->getPath();
                $met = strpos($order, ' ');
                if ($met !== false){
                    $method = substr($order,0,$met);
                    $order = substr($order,$met+1);
                } else {
                    return call_user_func_array($negativeMethod, $negativeParams);
                }
            } else {
                $order = $selectedInstruction['links'][0]['path'];
                if (isset($selectedInstruction['links'][0]['method'])){
                    $method = $selectedInstruction['links'][0]['method'];
                }
            }

            // ersetzt die Platzer im Ausgang mit den eingegeben Parametern
            foreach ($params as $key => $param) {
                $order = str_replace(':' . $key, $param, $order);
            }

            // führe nun den Aufruf aus
            $result = Request::routeRequest(
                                            $method,
                                            $order,
                                            array(),
                                            $body,
                                            $link,
                                            $link->getPrefix()
                                            );

            if ( $result['status'] == $positiveStatus ){
                // die Antwort war so, wie wir sie erwartet haben
                if ($returnType!==null){
                    // wenn ein erwarteter Rückgabetyp angegeben wurde, wird eine Typ::decodeType() ausgeführt
                    $result['content'] = call_user_func_array('\\'.$returnType.'::decode'.$returnType, array($result['content']));
                    if (!is_array($result['content'])) {
                        $result['content'] = array($result['content']);
                    }
                }

                // rufe nun die positive Methode auf
                $result[] =  call_user_func_array($positiveMethod, array_merge(array("input"=>$result['content']),$positiveParams));
            }

            // ansonsten rufen wir die negative Methode auf
            $result[] = call_user_func_array($negativeMethod, $negativeParams);
        }
        
        return $result;
    }
    
    public function callAllWithRelevanz($linkName, $relevanz, $params, $body, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $returnType=null)
    {
        $links=$this->getLinks($linkName);
        $instructions = $this->_com->instruction('',true);

        // ermittle den zutreffenden Ausgang
        $selectedInstruction=null;
        foreach($instructions as $instruction){
            if ($instruction['name']==$linkName){
                $selectedInstruction =$instruction;
            }
        }

        $result = array();
        
        foreach($links as $key => $link){
            if ($link->getRelevanz() !== $relevanz) {
                continue;
            }

            $method = 'GET';
            if ($link->getPath()!==null && $link->getPath()!==''){
                $order = $link->getPath();
                $met = strpos($order, ' ');
                if ($met !== false){
                    $method = substr($order,0,$met);
                    $order = substr($order,$met+1);
                } else {
                    return call_user_func_array($negativeMethod, $negativeParams);
                }
            } else {
                $order = $selectedInstruction['links'][0]['path'];
                if (isset($selectedInstruction['links'][0]['method'])){
                    $method = $selectedInstruction['links'][0]['method'];
                }
            }

            // ersetzt die Platzer im Ausgang mit den eingegeben Parametern
            foreach ($params as $key => $param) {
                $order = str_replace(':' . $key, $param, $order);
            }

            // führe nun den Aufruf aus
            $result = Request::routeRequest(
                                            $method,
                                            $order,
                                            array(),
                                            $body,
                                            $link,
                                            $link->getPrefix()
                                            );

            if ( $result['status'] == $positiveStatus ){
                // die Antwort war so, wie wir sie erwartet haben
                if ($returnType!==null){
                    // wenn ein erwarteter Rückgabetyp angegeben wurde, wird eine Typ::decodeType() ausgeführt
                    $result['content'] = call_user_func_array('\\'.$returnType.'::decode'.$returnType, array($result['content']));
                    if (!is_array($result['content'])) {
                        $result['content'] = array($result['content']);
                    }
                }

                // rufe nun die positive Methode auf
                $result[] =  call_user_func_array($positiveMethod, array_merge(array("input"=>$result['content']),$positiveParams));
            }

            // ansonsten rufen wir die negative Methode auf
            $result[] = call_user_func_array($negativeMethod, $negativeParams);
        }
        
        return $result;
    }

    /**
     * Führt eine Anfrage über $linkName aus
     *
     * @param string $linkName Der Name des Ausgangs
     * @param mixed[] $params Die Ersetzungen für die Platzhalter des Befehls (Bsp.: array('uid'=>2,'cid'=>1)
     * @param string body Der Inhalt der Anfrage für POST und PUT
     * @param int $positiveStatus Der Status, welcher als erfolgreiche Antwort gesehen wird (Bsp.: 200)
     * @param callable $positiveMethod Im positiven Fall wird diese Methode aufgerufen
     * @param mixed[] $positiveParams Die Werte, welche an die positive Funktion übergeben werden
     * @param callable $negativeMethod Im negativen Fall wird diese Methode aufgerufen
     * @param mixed[] $negativeParams Die Werte, welche an die negative Funktion übergeben werden
     * @param string returnType Ein optionaler Rückgabetyp (es können Structures angegeben werden, sodass automatisch Typ::encodeType() ausgelöst wird)
     * @return mixed Das Ergebnis der aufgerufenen Resultatfunktion
     */
    public function call($linkName, $params, $body, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $returnType=null)
    {
        $link=CConfig::getLink($this->_conf->getLinks( ),$linkName);
        $instructions = $this->_com->instruction('',true);

        // ermittle den zutreffenden Ausgang
        $selectedInstruction=null;
        foreach($instructions as $instruction){
            if ($instruction['name']==$linkName){
                $selectedInstruction=$instruction;
                break;
            }
        }

        $method = 'GET';
        if ($link->getPath()!==null && $link->getPath()!==''){
            $order = $link->getPath();
            $met = strpos($order, ' ');
            if ($met !== false){
                $method = substr($order,0,$met);
                $order = substr($order,$met+1);
            } else {
                return call_user_func_array($negativeMethod, $negativeParams);
            }
        } else {
            $order = $selectedInstruction['links'][0]['path'];
            if (isset($selectedInstruction['links'][0]['method'])){
                $method = $selectedInstruction['links'][0]['method'];
            }
        }

        // ersetzt die Platzer im Ausgang mit den eingegeben Parametern
        foreach ($params as $key => $param) {
            $order = str_replace(':' . $key, $param, $order);
        }
///echo $order; // die URL, welche aufgerufen wird
        // führe nun den Aufruf aus
        $result = Request::routeRequest(
                                        $method,
                                        $order,
                                        array(),
                                        $body,
                                        $link,
                                        $link->getPrefix()
                                        );

        if ( $result['status'] == $positiveStatus ){
            // die Antwort war so, wie wir sie erwartet haben
            if ($returnType!==null){
                // wenn ein erwarteter Rückgabetyp angegeben wurde, wird eine Typ::decodeType() ausgeführt
                $result['content'] = call_user_func_array('\\'.$returnType.'::decode'.$returnType, array($result['content']));
                if (!is_array($result['content'])) {
                    $result['content'] = array($result['content']);
                }
            }

            // rufe nun die positive Methode auf
            return call_user_func_array($positiveMethod, array_merge(array("input"=>$result['content']),$positiveParams));
        }

        // ansonsten rufen wir die negative Methode auf
        return call_user_func_array($negativeMethod, $negativeParams);
    }

    /**
     * Führt eine Anfrage über $linkName aus, wobei eine Verbindung mit der URI $order genutzt wird
     *
     * @param string $linkName Der Name des Ausgangs
     * @param mixed[] $params Die Ersetzungen für die Platzhalter des Befehls (Bsp.: array('uid'=>2,'cid'=>1)
     * @param string body Der Inhalt der Anfrage für POST und PUT
     * @param int $positiveStatus Der Status, welcher als erfolgreiche Antwort gesehen wird (Bsp.: 200)
     * @param callable $positiveMethod Im positiven Fall wird diese Methode aufgerufen
     * @param mixed[] $positiveParams Die Werte, welche an die positive Funktion übergeben werden
     * @param callable $negativeMethod Im negativen Fall wird diese Methode aufgerufen
     * @param mixed[] $negativeParams Die Werte, welche an die negative Funktion übergeben werden
     * @param string returnType Ein optionaler Rückgabetyp (es können Structures angegeben werden, sodass automatisch Typ::encodeType() ausgelöst wird)
     * @return mixed Das Ergebnis der aufgerufenen Resultatfunktion
     */
    public function callByURI($linkName, $order, $params, $body, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $returnType=null)
    {
        $links=CConfig::getLinks($this->_conf->getLinks( ),$linkName);
        $link=null;

        $instructions = $this->_com->instruction('',true);

        // ermittle den zutreffenden Ausgang
        $selectedInstruction=null;
        foreach($instructions as $instruction){
            if ($instruction['name']==$linkName){
                $selectedInstruction=$instruction;
                break;
            }
        }

        if (isset($selectedInstruction['links'][0]['path']) && $selectedInstruction['links'][0]['path'] == $order){
            $link = $links[0];
        } else {
            foreach($links as $li){
                $testPath = $li->getPath();
                $met = strpos($testPath, ' ');
                if ($met !== false){
                    $testPath = substr($testPath,$met+1);
                    foreach ($params as $key => $param) {
                        $testPath = str_replace(':' . $key, $param, $testPath);
                    }

                    if ($testPath == $order){
                        $link = $li;
                        break;
                    }
                }
            }
        }

        if ($link == null){
            return call_user_func_array($negativeMethod, $negativeParams);
        }

        $method = 'GET';
        if ($link->getPath()!==null && $link->getPath()!==''){
            $order = $link->getPath();
            $met = strpos($order, ' ');
            if ($met !== false){
                $method = substr($order,0,$met);
                $order = substr($order,$met+1);
            } else {
                return call_user_func_array($negativeMethod, $negativeParams);
            }
        } else {
            $order = $selectedInstruction['links'][0]['path'];
            if (isset($selectedInstruction['links'][0]['method'])){
                $method = $selectedInstruction['links'][0]['method'];
            }
        }

        // ersetzt die Platzer im Ausgang mit den eingegeben Parametern
        foreach ($params as $key => $param) {
            $order = str_replace(':' . $key, $param, $order);
        }

        // führe nun den Aufruf aus
        $result = Request::routeRequest(
                                        $method,
                                        $order,
                                        array(),
                                        $body,
                                        $link
                                        );

        if ( $result['status'] == $positiveStatus ){
            // die Antwort war so, wie wir sie erwartet haben
            if ($returnType!==null){
                // wenn ein erwarteter Rückgabetyp angegeben wurde, wird eine Typ::decodeType() ausgeführt
                $result['content'] = call_user_func_array('\\'.$returnType.'::decode'.$returnType, array($result['content']));
                if (!is_array($result['content'])) {
                    $result['content'] = array($result['content']);
                }
            }

            // rufe nun die positive Methode auf
            return call_user_func_array($positiveMethod, array_merge(array("input"=>$result['content']),$positiveParams));
        }

        // ansonsten rufen wir die negative Methode auf
        return call_user_func_array($negativeMethod, $negativeParams);
    }

    /**
     * Sendet den Inhalt von $file an $linkName und behandelt die Antwort
     *
     * @param string $linkName Der Name des Ausgangs
     * @param string $file Der Pfad des SQL Templates
     * @param mixed[] $params Die Variablen, welche im Template verwendet werden können (Bsp.: array('time'=>12)
     * @param int $positiveStatus Der Status, welcher als erfolgreiche Antwort gesehen wird (Bsp.: 200)
     * @param callable $positiveMethod Im positiven Fall wird diese Methode aufgerufen
     * @param mixed[] $positiveParams Die Werte, welche an die positive Funktion übergeben werden
     * @param callable $negativeMethod Im negativen Fall wird diese Methode aufgerufen
     * @param mixed[] $negativeParams Die Werte, welche an die negative Funktion übergeben werden
     * @param bool $checkSession Ob die Sessiondaten in der Datenbank geprüft werden sollen
     * @return mixed Das Ergebnis der aufgerufenen Resultatfunktion
     */
    public function callSqlTemplate($linkName, $file, $params, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $checkSession=false)
    {
        $link=CConfig::getLink($this->_conf->getLinks( ),$linkName);

        // führe nun den Aufruf mit der SQL $file aus
        $result = DBRequest::getRoutedSqlFile(
                                              $link,
                                              $file,
                                              $params,
                                              $checkSession
                                              );

        if ( $result['status'] == $positiveStatus){
            // die Antwort war so, wie wir sie erwartet haben
            $queryResult = Query::decodeQuery( $result['content'] );
            if (!is_array($queryResult)) {
                $queryResult = array($queryResult);
            }

            // rufe nun die positive Methode auf
            return call_user_func_array($positiveMethod, array_merge(array("input"=>$queryResult),$positiveParams));
        }

        // ansonsten rufen wir die negative Methode auf
        return call_user_func_array($negativeMethod, $negativeParams);
    }

    /**
     * Sendet $sql an $linkName und behandelt die Antwort
     *
     * @param string $linkName Der Name des Ausgangs
     * @param string $sql Der zu verwendende SQL Inhalt
     * @param int $positiveStatus Der Status, welcher als erfolgreiche Antwort gesehen wird (Bsp.: 200)
     * @param callable $positiveMethod Im positiven Fall wird diese Methode aufgerufen
     * @param mixed[] $positiveParams Die Werte, welche an die positive Funktion übergeben werden
     * @param callable $negativeMethod Im negativen Fall wird diese Methode aufgerufen
     * @param mixed[] $negativeParams Die Werte, welche an die negative Funktion übergeben werden
     * @param bool $checkSession Ob die Sessiondaten in der Datenbank geprüft werden sollen
     * @return mixed Das Ergebnis der aufgerufenen Resultatfunktion
     */
    public function callSql($linkName, $sql, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $checkSession=false)
    {
        $link=CConfig::getLink($this->_conf->getLinks( ),$linkName);
        // starts a query, by using given sql statements/statement
        $result = DBRequest::getRoutedSql(
                                              $link,
                                              $sql,
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] == $positiveStatus){
            // die Antwort war so, wie wir sie erwartet haben
            $queryResult = Query::decodeQuery( $result['content'] );
            if (!is_array($queryResult)) {
                $queryResult = array($queryResult);
            }

            // rufe nun die positive Methode auf
            return call_user_func_array($positiveMethod, array_merge(array("input"=>$queryResult),$positiveParams));
        }

        // ansonsten rufen wir die negative Methode auf
        return call_user_func_array($negativeMethod, $negativeParams);
    }

    /**
     * Liefert eine Rückgabe
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function createAnswer($status=200, $content='')
    {
        return array("status"=>$status,"content"=>$content);
    }

    /**
     * Liefert eine Rückgabe (ein Problem ist aufgetreten)
     *
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isProblem($content=null)
    {
        if (func_num_args()>1){
            return self::isProblemAnswer(func_get_arg(0),func_get_arg(1));
        }
        return self::createAnswer(409,$content);
    }
    private static function isProblemAnswer($input, $params)
    {
        if ($params===null){
            return self::createAnswer(409,$content);
        }
        return self::createAnswer(409,$params);
    }

    /**
     * Liefert eine Rückgabe (ein schwerwiegender Fehler ist aufgetreten)
     *
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isError($content=null)
    {
        if (func_num_args()>1){
            return self::isErrorAnswer(func_get_arg(0),func_get_arg(1));
        }
        return self::createAnswer(500,$content);
    }
    private static function isErrorAnswer($input, $params)
    {
        if ($params===null){
            return self::createAnswer(500,$content);
        }
        return self::createAnswer(500,$params);
    }

    public static function isPreconditionError($content=null)
    {
        if (func_num_args()>1){
            return self::isPreconditionErrorAnswer(func_get_arg(0),func_get_arg(1));
        }
        return self::createAnswer(412,$content);
    }
    private static function isPreconditionErrorAnswer($input, $params)
    {
        if ($params===null){
            return self::createAnswer(412,$content);
        }
        return self::createAnswer(412,$params);
    }

    /**
     * Liefert eine Rückgabe (wurde erstellt)
     *
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isCreated($content=null)
    {
        if (func_num_args()>1){
            return self::isCreatedAnswer(func_get_arg(0),func_get_arg(1));
        }
        return self::createAnswer(201,$content);
    }
    private static function isCreatedAnswer($input, $params)
    {
        if ($params===null){
            return self::createAnswer(201,$content);
        }
        return self::createAnswer(201,$params);
    }

    /**
     * Liefert eine Rückgabe (Anfrage war erfolgreich)
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isOk($content=null)
    {
        if (func_num_args()>1){
            return self::isOkAnswer(func_get_arg(0),func_get_arg(1));
        }
        return self::createAnswer(200,$content);
    }
    private static function isOkAnswer($input, $params)
    {
        if ($params===null){
            return self::createAnswer(200,$content);
        }
        return self::createAnswer(200,$params);
    }

    /**
     * Liefert eine Rückgabe (Ressource wurde nicht gefunden)
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isEmpty($content=null)
    {
        if (func_num_args()>1){
            return self::isEmptyAnswer(func_get_arg(0),func_get_arg(1));
        }
        return self::createAnswer(404,$content);
    }
    private static function isEmptyAnswer($input, $params)
    {
        if ($params===null){
            return self::createAnswer(404,$content);
        }
        return self::createAnswer(404,$params);
    }

    /**
     * Liefert eine Rückgabe (ein Problem ist aufgetreten)
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isRejected($content=null)
    {
        if (func_num_args()>1){
            return self::isRejectedAnswer(func_get_arg(0),func_get_arg(1));
        }
        return self::createAnswer(401,$content);
    }
    private static function isRejectedAnswer($input, $params)
    {
        if ($params===null){
            return self::createAnswer(401,$content);
        }
        return self::createAnswer(401,$params);
    }

    public static function header($name, $value)
    {
        header($name.': '.$value);
    }
    
    // für die Tabellen werden oft postfixe benötigt, welche aus
    // den eingehenden Parametern aufgebaut werden
    // list hat den Aufbau array(ausgangselement => zielelement, ...)
    public static function generatePostfix($list, &$params)
    {
        foreach ($list as $key => $value){
            $tmp = '';
            if (isset($params[$key]) && trim($params[$key]) !== ''){
                $tmp = '_'.$params[$key];
            }
            $params[$value] = $tmp;
        }
    }
    
    // liefert die Optionen der Komponente (sind in der Components.json eingetragen)
    public function extractComponentOptions($split=true, $glueA=';', $glueB='=')
    {
        if ($this->_conf !== null){
            $options = $this->_conf->getOption();
            if (!isset($options)){
                // es wurden keine Optionen gefunden
                if ($split){
                    return array();
                }
                return '';
            }
            
            // zerlegt den Optionsstring zunaechst nach glueA, dann nach glueB
            if ($split){
                $options = explode($glueA, $options);
                $res = array();
                foreach($options as $option){
                    $tmp = explode($glueB, $option);
                    if (count($tmp)==1){
                        if (trim($tmp[0]) === '') {
                            continue;
                        }
                        $res[$tmp[0]] = $tmp[0];
                    } else if(count($tmp)==2){
                        if (trim($tmp[0]) === '') {
                            continue;
                        }
                        $res[$tmp[0]] = $tmp[1];
                    } else {
                        $name = array_shift($tmp);
                        $res[$name] = $tmp;
                    }
                }
                return $res;
            }
            // wenn die Optionen nicht zerlegt werden sollen, dann nur als String
            return $options;
        }
        // es ist keine Komponente geladen
        return null;
    }
}