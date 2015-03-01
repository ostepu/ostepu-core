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
    private $_path=null;
    private $_prefix=null;
    /**
     * @var Slim $_app the slim object
     */
   // private $_app = null;

    /**
     * @var Component $_conf the component data object
     */
    public $_conf = null;
    private $_class = null;
    private $_com = null;
    
    public function __construct( $prefix, $path, $class )
    {
        $this->_path=$path;
        $this->_prefix=$prefix;
        $this->_class=$class;
    }

    public function run()
    {
        // runs the CConfig
        $com = new CConfig( $this->_prefix, $this->_path );

        // runs the DBUser
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
    
    public function call($linkName, $params, $body, $positiveStatus, callable $positiveMethod, $positiveParams, callable $negativeMethod, $negativeParams, $returnType=null)
    {
   // public function call($linkName, $params, $body, $returnType=null){
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
//echo $selectedInstruction['links'][0]['method'].' '.$order;
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
    
    public static function createAnswer($status=200, $content=''){
        return array("status"=>$status,"content"=>$content);
    }
    public static function isProblem($content=null){
        return self::createAnswer(409,$content);
    }
    public static function isCreated($content=null){
        return self::createAnswer(201,$content);
    }
    public static function isOk($content=null){
        return self::createAnswer(200,$content);
    }
    public static function isEmpty($content=null){
        return self::createAnswer(404,$content);
    }
    public static function isRejected($content=null){
        return self::createAnswer(401,$content);
    }
}
?>