<?php
/**
 * @file DBAttachment.php contains the DBAttachment class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBAttachment/AttachmentSample.json
 */ 

require_once( '../../Assistants/Slim/Slim.php' );
include_once( '../../Assistants/Structures.php' );
include_once( '../../Assistants/Request.php' );
include_once( '../../Assistants/DBJson.php' );
include_once( '../../Assistants/DBRequest.php' );
include_once( '../../Assistants/CConfig.php' );
include_once( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBAttachment::getPrefix());

// runs the DBAttachment
if (!$com->used())
    new DBAttachment($com->loadConfig());  
    
/**
 * A class, to abstract the "Attachment" table from database
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
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
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
        
        // POST AddAttachment
        $this->_app->post('/' . $this->getPrefix() . '(/)',
                         array($this,'addAttachment'));    
        
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
                    $this->getPrefix()) === 0){
                    
            // run Slim
            $this->_app->run();
        }
    }


    /**
     * Edits an attachment.
     *
     * Called when this component receives an HTTP PUT request to
     * /attachment/$aid(/) or /attachment/attachment/$aid(/).
     * The request body should contain a JSON object representing the 
     * attachment's new attributes.
     *
     * @param string $aid The id of the attachment that is being updated.
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
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->stop();
            }
        }
    }


    /**
     * Deletes an attachment.
     *
     * Called when this component receives an HTTP DELETE request to
     * /attachment/$aid(/) or /attachment/attachment/$aid(/).
     *
     * @param string $aid The id of the attachment that is being deleted.
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
            $this->_app->response->setStatus(201);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("DELETE DeleteAttachment failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->stop();
        }
    }


    /**
     * Adds an attachment.
     *
     * Called when this component receives an HTTP POST request to
     * /attachment(/).
     * The request body should contain a JSON object representing the 
     * attachment's attributes.
     */
    public function addAttachment()
    {
        Logger::Log("starts POST AddAttachment",LogLevel::DEBUG);
        
        // decode the received attachment data, as an object
        $insert = Attachment::decodeAttachment($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);
        
        // this array contains the indices of the inserted objects
        $res = array();
        foreach ($insert as $in){
            // generates the insert data for the object
            $data = $in->getInsertData();
            
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile($this->query, 
                                            "Sql/AddAttachment.sql", 
                                            array("values" => $data));                   
            
            // checks the correctness of the query
            if ($result['status']>=200 && $result['status']<=299){
                $queryResult = Query::decodeQuery($result['content']);
                
                // sets the new auto-increment id
                $obj = new Attachment();
                $obj->setId($queryResult->getInsertId());
            
                array_push($res, $obj);
                $this->_app->response->setStatus(201);
                if (isset($result['headers']['Content-Type']))
                    $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
            } else{
                Logger::Log("POST AddAttachment failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
                $this->_app->response->setBody(Attachment::encodeAttachment($res)); 
                $this->_app->stop();
            }
        }
        
        if (count($res)==1){
            $this->_app->response->setBody(Attachment::encodeAttachment($res[0])); 
        }
        else
            $this->_app->response->setBody(Attachment::encodeAttachment($res)); 
    }


    /**
     * Returns an attachment.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment/$aid(/) or /attachment/attachment/$aid(/).
     *
     * @param string $aid The id of the attachment that should be returned.
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
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAttachment failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }


    /**
     * Returns all attachments.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment(/) or /attachment/attachment(/).
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
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetAllAttachments failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }


    /**
     * Returns the attachments to a given exercise.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment/exercise/$eid(/).
     *
     * @param string $eid The id of the exercise.
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
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetExerciseAttachments failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }


    /**
     * Returns the attachments to a given exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment/exercisesheet/$esid(/).
     *
     * @param string $esid The id of the exercise sheet.
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
        
            $this->_app->response->setStatus(200);
            if (isset($result['headers']['Content-Type']))
                $this->_app->response->headers->set('Content-Type', $result['headers']['Content-Type']);
                
        } else{
            Logger::Log("GET GetSheetAttachments failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($result['status']) ? $result['status'] : 409);
            $this->_app->response->setBody(Attachment::encodeAttachment(new Attachment()));
            $this->_app->stop();
        }
    }
}
?>