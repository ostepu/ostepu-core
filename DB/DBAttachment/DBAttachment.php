<?php
/**
 * @file DBAttachment.php contains the DBAttachment class
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBAttachment::getPrefix());

// runs the DBAttachment
if (!$com->used())
    new DBAttachment($com->loadConfig());  
    
/**
 * A class, to abstract the "Attachment" table from database
 *
 * @author Till Uhlig
 */
class DBAttachment
{
    /**
     * @var Slim $_app the slim object
     */ 
    private $_app=null;
    
    /**
     * @var Component $_conf the component data object
     */ 
    private $_conf=null;
    
    /**
     * @var Link[] $query a list of links to a query component
     */ 
    private $query=array();
    
    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */
    private static $_prefix = "attachment";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return DBAttachment::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */ 
    public static function setPrefix($value)
    {
        DBAttachment::$_prefix = $value;
    }
    
    /**
     * the component constructor
     *
     * @param Component $conf component data
     */ 
    public function __construct($conf)
    {
        // initialize component
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        // initialize slim
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditAttachment
        $this->_app->put('/' . $this->getPrefix() . '(/attachment)/:aid(/)',
                        array($this,'editAttachment'));
        
        // DELETE DeleteAttachment
        $this->_app->delete('/' . $this->getPrefix() . '(/attachment)/:aid(/)',
                           array($this,'deleteAttachment'));
        
        // POST SetAttachment
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'setAttachment'));    
        
        // GET GetAttachment
        $this->_app->get('/' . $this->getPrefix() . '(/attachment)/:aid(/)',
                        array($this,'getAttachment'));
        
        // GET GetAllAttachments
        $this->_app->get('/' . $this->getPrefix() . '(/attachment)(/)',
                        array($this,'getAllAttachments'));
                        
        // GET GetExerciseAttachments
        $this->_app->get('/' . $this->getPrefix() . '/exercise/:eid(/)',
                        array($this,'getExerciseAttachments'));
                        
        // GET GetSheetAttachments
        $this->_app->get('/' . $this->getPrefix() . '/exercisesheet/:esid(/)',
                        array($this,'getSheetAttachments'));
                        
        // starts slim only if the right prefix was received
        if (strpos ($this->_app->request->getResourceUri(),'/' . 
                    $this->getPrefx()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditAttachment
     *
     * @param $aid a database attachment identifier
     */
    public function editAttachment($aid)
    {
        Logger::Log("starts PUT EditAttachment",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($aid));
                            
        // decode the received attachment data, as an object
        $insert = Attachment::decodeAttachment($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/EditAttachment.sql", 
                                        array("aid" => $aid, "values" => $data));                   
           
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("PUT EditAttachment failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * DELETE DeleteAttachment
     *
     * @param $aid a database attachment identifier
     */
    public function deleteAttachment($aid)
    {
        Logger::Log("starts DELETE DeleteAttachment",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($aid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/DeleteAttachment.sql", 
                                        array("aid" => $aid));    
            
        // checks the correctness of the query  
        if ($result['status']>=200 && $result['status']<=299){
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteAttachment failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }
    
    /**
     * POST SetAttachment
     */
    public function setAttachment()
    {
        Logger::Log("starts POST SetAttachment",LogLevel::DEBUG);
        
        // decode the received attachment data, as an object
        $insert = Attachment::decodeAttachment($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/SetAttachment.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new Attachment();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(Attachment::encodeAttachment($obj)); 
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST SetAttachment failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
                $this->_app->stop();
            }
        }
    }
    
    /**
     * GET GetAttachment
     *
     * @param $aid a database attachment identifier
     */
    public function getAttachment($aid)
    {     
        Logger::Log("starts GET GetAttachment",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($aid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAttachment.sql", 
                                        array("aid" => $aid));
        
        // checks the correctness of the query                                       
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of an file by using a defined list of 
            // its attributes
            $file = DBJson::getObjectsByAttributes($data, 
                                    File::getDBPrimaryKey(), 
                                    File::getDBConvert());
            
            // generates an assoc array of an attachment by using a defined list of 
            // its attributes
            $attachment = DBJson::getObjectsByAttributes($data, 
                                    Attachment::getDBPrimaryKey(), 
                                    Attachment::getDBConvert());
            
            // concatenates the attachment and the associated file
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $attachment,
                                    Attachment::getDBPrimaryKey(),
                                    Attachment::getDBConvert()['F_file'], 
                                    $file,File::getDBPrimaryKey());              
            
            // to reindex
            $res = array_merge($res);
            
            // only one object as result
            if (count($res)>0)
                $res = $res[0];
                
            $this->_app->response->setBody(Attachment::encodeAttachment($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAttachment failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetAllAttachments
     */
    public function getAllAttachments()
    {    
        Logger::Log("starts GET GetAllAttachments",LogLevel::DEBUG);
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetAllAttachments.sql", 
                                        array());
        
        // checks the correctness of the query                                     
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                    File::getDBPrimaryKey(), 
                                    File::getDBConvert());
            
            // generates an assoc array of attachments by using a defined list of 
            // its attributes
            $attachments = DBJson::getObjectsByAttributes($data, 
            Attachment::getDBPrimaryKey(), 
            Attachment::getDBConvert());
            
            // concatenates the attachments and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                                    $attachments, 
                                    Attachment::getDBPrimaryKey(), 
                                    Attachment::getDBConvert()['F_file'], 
                                    $files,File::getDBPrimaryKey());              
            
            // to reindex
            $res = array_merge($res);
                
            $this->_app->response->setBody(Attachment::encodeAttachment($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllAttachments failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetExerciseAttachments
     *
     * @param int $eid a database exercise identifier
     */
    public function getExerciseAttachments($eid)
    {     
        Logger::Log("starts GET GetExerciseAttachments",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($eid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetExerciseAttachments.sql", 
                                        array("eid" => $eid));
        
        // checks the correctness of the query                                    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                    File::getDBPrimaryKey(), 
                                    File::getDBConvert());
            
            // generates an assoc array of attachments by using a defined list of 
            // its attributes
            $attachments = DBJson::getObjectsByAttributes($data, 
                                    Attachment::getDBPrimaryKey(), 
                                    Attachment::getDBConvert());
            
            // concatenates the attachments and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                                $attachments,Attachment::getDBPrimaryKey(), 
                                Attachment::getDBConvert()['F_file'], 
                                $files,File::getDBPrimaryKey());              
           
            // to reindex
            $res = array_merge($res);
                
            $this->_app->response->setBody(Attachment::encodeAttachment($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseAttachments failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }
    
    /**
     * GET GetSheetAttachments
     *
     * @param int $esid a database exercise sheet identifier
     */
    public function getSheetAttachments($esid)
    {      
        Logger::Log("starts GET GetSheetAttachments",LogLevel::DEBUG);
        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($esid));
                            
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile($this->query, 
                                        "Sql/GetSheetAttachments.sql", 
                                        array("esid" => $esid));
        
        // checks the correctness of the query                                    
        if ($result['status']>=200 && $result['status']<=299){
            $query = Query::decodeQuery($result['content']);
            
            $data = $query->getResponse();
            
            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                    File::getDBPrimaryKey(), 
                                    File::getDBConvert());
            
            // generates an assoc array of attachments by using a defined list of 
            // its attributes
            $attachments = DBJson::getObjectsByAttributes($data, 
                                    Attachment::getDBPrimaryKey(), 
                                    Attachment::getDBConvert());
            
            // concatenates the attachments and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                        $attachments,Attachment::getDBPrimaryKey(), 
                        Attachment::getDBConvert()['F_file'], 
                        $files,File::getDBPrimaryKey());              
            
            // to reindex
            $res = array_merge($res);
                
            $this->_app->response->setBody(Attachment::encodeAttachment($res));
        
            $this->_app->response->setStatus($result['status']);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetAttachments failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }
}
?>