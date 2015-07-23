<?php
require_once ( dirname(__FILE__) . '/Slim/Route.php' );
require_once ( dirname(__FILE__) . '/Slim/Router.php' );
require_once ( dirname(__FILE__) . '/Slim/Environment.php' );
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
    
    /**
     * Der Konstruktor
     *
     * @param string Unterstützte Präfixe (veraltet)
     * @param string Der lokale Pfade des Moduls
     * @param string Der Klassenname des Moduls
     */
    public function __construct( $prefix, $path, $class )
    {
        $this->_path=$path;
        $this->_prefix=$prefix;
        $this->_class=$class;
    }

    /**
     * Führt das Modul entsprechend der Commands.json und Component.json Definitionen aus
     */
    public function run()
    {
        // runs the CConfig
        $com = new CConfig( $this->_prefix, $this->_path );

        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
        $this->_conf=$conf;
        $this->_com=$com;
        $commands = $com->commands(array(),true,true);
        //$commands[] = array('name' => 'postMultiGetRequest','method' => 'POST', 'path' => '/multiGetRequest', 'inputType' => 'Link', 'outputType' => '');
        
        $router = new \Slim\Router();
        foreach ($commands as $command){
            $route = new \Slim\Route($command['path'],array($this->_class,(isset($command['callback']) ? $command['callback'] : $command['name'])),false);
            $route->via(strtoupper($command['method']));
            $route->setName($command['name']);
            $router->map($route);
            if (strtoupper($command['method'])=='GET'){
                $route = new \Slim\Route($command['path'],array($this->_class,(isset($command['callback']) ? $command['callback'] : $command['name'])),false);
                $route->via('HEAD');
                $route->setName($command['name']);
                $router->map($route);
            }
        }

        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = str_replace('?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''), '', substr_replace($requestUri, '', 0, strlen((strpos($requestUri, $scriptName) !== false ? $scriptName : str_replace('\\', '', dirname($scriptName))))));
        $matches = $router->getMatchedRoutes(strtoupper($_SERVER['REQUEST_METHOD']), $path);

        if (count($matches)>0){
            $matches = $matches[0];
            $selectedCommand=null;
            foreach ($commands as $command){
                if ($command['name'] === $matches->getName()){
                    $selectedCommand = $command;
                    break;
                }
            }
            
            $rawInput = \Slim\Environment::getInstance()->offsetGet('slim.input');
            if (!$rawInput) {
                $rawInput = @file_get_contents('php://input');
            }
            ///Logger::Log('input>> '.$rawInput, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');

            $arr = true;
            if (isset($selectedCommand['inputType']) && trim($selectedCommand['inputType'])!=''){
                $inputType = $selectedCommand['inputType'];
                $rawInput = call_user_func_array('\\'.$inputType.'::decode'.$inputType, array($rawInput));
                        
                if ( !is_array( $rawInput ) ){
                    $rawInput = array( $rawInput );
                    $arr = false;
                }
            }

            // call method
            $params = $matches->getParams();
            if (isset($selectedCommand['inputType']) && trim($selectedCommand['inputType'])!='' && isset($rawInput)){
                $result=array("status"=>201,"content"=>array());
                foreach($rawInput as $input){
                    $res = call_user_func_array($matches->getCallable(), array($selectedCommand['name'],"input"=>$input,$params));
                    if (is_callable(array($res['content'],'setStatus')))
                        $res['content']->setStatus($res['status']);
                    $result["content"][] = $res['content'];
                    if (isset($res['status']))
                        $result["status"] = $res['status']; // eingefuegt
                }
            } else {
                $result = call_user_func_array($matches->getCallable(), array($selectedCommand['name'],"input"=>$rawInput,$params));
            }
            
            if ($selectedCommand['method']=='HEAD'){
                $result['content'] = '';
            } elseif (isset($selectedCommand['outputType']) && trim($selectedCommand['outputType'])!='' && trim($selectedCommand['outputType'])!='binary'){
                $outputType = $selectedCommand['outputType'];
                
                if (isset( $result['content']) ){
                    if ( !is_array( $result['content'] ) ){
                        $result['content'] = array( $result['content'] );
                    }
                    
                    if ( !$arr && count( $result['content'] ) == 1 )
                        $result['content'] = $result['content'][0];

                    $result['content'] = call_user_func_array('\\'.$outputType.'::encode'.$outputType, array($result['content']));
                }
                header('Content-Type: application/json');                                            
            } elseif (isset($selectedCommand['outputType']) && trim($selectedCommand['outputType'])=='binary'){
                if (isset( $result['content'])){
                    if (!is_string($result['content']))
                        $result['content'] = json_encode($result['content']);
                }
            } else {
                if (isset( $result['content']) )
                    $result['content'] = json_encode($result['content']);
            }
        } else {
            $result=self::isEmpty();
        }

        if (isset( $result['content'])  )
            echo $result['content'];  
                    
        if (isset( $result['status']) ){
            http_response_code($result['status']); 
        } else 
            http_response_code(200); 
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
        $instructions = $this->_com->instruction(array(),true)['links'];
        $selectedInstruction=null;
        foreach($instructions as $instruction){
            if ($instruction['name']==$linkName){
                $selectedInstruction=$instruction;
                break;
            }
        }
        
        $order = $selectedInstruction['links'][0]['path'];
        foreach ($params as $key=>$param)
            $order = str_replace( ':'.$key, $param, $order);

        $result = Request::routeRequest( 
                                        $selectedInstruction['links'][0]['method'],
                                        $order,
                                        array(),
                                        $body,
                                        $link,
                                        $link->getPrefix()
                                        );    
                      
        if ( $result['status'] == $positiveStatus ){
             
            if ($returnType!==null){
                $result['content'] = call_user_func_array('\\'.$returnType.'::decode'.$returnType, array($result['content']));
                if ( !is_array( $result['content'] ) )
                    $result['content'] = array( $result['content'] );
            }
            return call_user_func_array($positiveMethod, array_merge(array("input"=>$result['content']),$positiveParams));
        }
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
    public function callSqlTemplate($linkName, $file, $params, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $checkSession=true)
    {
        $link=CConfig::getLink($this->_conf->getLinks( ),$linkName);
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $link,
                                              $file,
                                              $params,
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] == $positiveStatus){
            $queryResult = Query::decodeQuery( $result['content'] );
            if (!is_array($queryResult)) $queryResult = array($queryResult);
            return call_user_func_array($positiveMethod, array_merge(array("input"=>$queryResult),$positiveParams));
        }
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
    public function callSql($linkName, $sql, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $checkSession=true)
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
            $queryResult = Query::decodeQuery( $result['content'] );
            if (!is_array($queryResult)) $queryResult = array($queryResult);
            return call_user_func_array($positiveMethod, array_merge(array("input"=>$queryResult),$positiveParams));
        }
        return call_user_func_array($negativeMethod, $negativeParams);
    }
    
    /**
     * Liefert eine Rückgabe
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function createAnswer($status=200, $content=''){
        return array("status"=>$status,"content"=>$content);
    }
    
    /**
     * Liefert eine Rückgabe (ein Problem ist aufgetreten)
     *
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isProblem($content=null){
        return self::createAnswer(409,$content);
    }
    
    /**
     * Liefert eine Rückgabe (wurde erstellt)
     *
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isCreated($content=null){
        return self::createAnswer(201,$content);
    }
    
    /**
     * Liefert eine Rückgabe (Anfrage war erfolgreich)
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isOk($content=null){
        return self::createAnswer(200,$content);
    }
    
    /**
     * Liefert eine Rückgabe (Ressource wurde nicht gefunden)
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isEmpty($content=null){
        return self::createAnswer(404,$content);
    }

    /**
     * Liefert eine Rückgabe (ein Problem ist aufgetreten)
     *
     * @param int $status Der Status
     * @param string $content Der optionale Inhalt
     * @return array('status'=>..,'content'=>..) Die Antwort
     */
    public static function isRejected($content=null){
        return self::createAnswer(401,$content);
    }
}