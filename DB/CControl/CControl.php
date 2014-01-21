<?php
/**
 * @file CControl.php contains the CControl class
 * @include DB/CControl/LinkSample.json
 * @include DB/CControl/ComponentSample.json
 */ 

require 'Include/Slim/Slim.php';
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/Logger.php' );

\Slim\Slim::registerAutoloader();

// runs the CControl
new CControl();

/**
 * A class, to abstract the "Component" and "ComponentLinkage" table from database
 *
 * @author Till Uhlig
 */
class CControl
{
    /**
     * @var Slim $_app the slim object
     */ 
    private $_app=null;
    
    /**
     * the component constructor
     */ 
    public function __construct()
    {
        // initialize slim
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');
        
        // PUT EditLink
        $this->_app->put('/link/:linkid(/)',
                        array($this,'editLink'));
        
        // DELETE DeleteLink
        $this->_app->delete('/link/:linkid(/)',
                           array($this,'deleteLink'));
        
        // POST SetLink
        $this->_app->post('/link(/)',
                         array($this,'setLink'));
                         
        // GET GetLink
        $this->_app->get('/link/:linkid(/)',
                         array($this,'getLink'));

                         
                                                  

        // PUT EditComponent
        $this->_app->put('/component/:componentid(/)',
                        array($this,'editComponent'));
        
        // DELETE DeleteComponent
        $this->_app->delete('/component/:componentid(/)',
                           array($this,'deleteComponent'));
        
        // POST SetComponent
        $this->_app->post('/component(/)',
                         array($this,'setComponent'));
                         
        // GET GetComponent
        $this->_app->get('/component/:componentid(/)',
                         array($this,'getComponent'));                               
                         
  
  
                         
        // GET GetComponentDefinitions
        $this->_app->get('/definition(/)',
                         array($this,'getComponentDefinitions'));
                         
        // GET GetComponentDefinition
        $this->_app->get('/definition/:componentid(/)',
                         array($this,'getComponentDefinition'));
                         
        // GET SendComponentDefinitions
        $this->_app->get('/send(/)',
                         array($this,'sendComponentDefinitions'));
                
        // run Slim
        $this->_app->run();

    }
    
    
    
     /**
     * PUT EditLink
     *
     * @param $linkid a database linkage identifier
     */
    public function editLink($linkid)
    {        
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($linkid));
                            
        // decode the received link data, as an object
        $insert = Link::decodeLink($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            // generates the update data for the object
            $data = $in->getInsertData();
            
            // starts a query
            eval("\$sql = \"".file_get_contents("Sql/PutLink.sql")."\";");
            $query_result = DBRequest::request($sql, false);                
           
            // checks the correctness of the query
            if (!$query_result['errno'] && $query_result['content']){
                $this->_app->response->setStatus(201); 
            } else{
                Logger::Log("PUT EditLink failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
            }
        }
    }
    
    /**
     * DELETE DeleteLink
     *
     * @param $linkid a database linkage identifier
     */
    public function deleteLink($linkid)
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($linkid));
                            
        // starts a query
        eval("\$sql = \"".file_get_contents("Sql/DeleteLink.sql")."\";");
        $result = DBRequest::request($sql, false);
        
        // checks the correctness of the query
        if (!$query_result['errno'] && $query_result['content']){
            $this->_app->response->setStatus(201);                
        } else{
            Logger::Log("DELETE DeleteLink failed",LogLevel::ERROR);
            $this->_app->response->setStatus(452);
        }
    }
    
    /**
     * POST SetLink
     */
    public function setLink()
    {
        // decode the received link data, as an object
        $insert = Link::decodeLink($this->_app->request->getBody());
        
        // always been an array
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            $values = $in->getInsertData();
            
            // starts a query
            eval("\$sql = \"".file_get_contents("Sql/PostLink.sql")."\";");
            $result = DBRequest::request($sql, false);                
           
            // checks the correctness of the query
            if (!$query_result['errno'] && $query_result['content']){
                $data = DBJson::getRows($query_result['content']);
                $queryResult = Query::decodeQuery($data);
                
                // sets the new auto-increment id
                $obj = new Link();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(Link::encodeLink($obj)); 
                $this->_app->response->setStatus(201);            
            } else{
                Logger::Log("POST SetLink failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
            }
        }
    }
    
    /**
     * GET GetLink
     *
     * @param $linkid a database linkage identifier
     */
    public function getLink($linkid)
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($linkid));
                            
        // starts a query
        eval("\$sql = \"".file_get_contents("Sql/GetLink.sql")."\";");
        $query_result = DBRequest::request($sql, false);
        
        // checks the correctness of the query
        if (!$query_result['errno'] && $query_result['content']){
            $data = DBJson::getRows($query_result['content']);
            $links = DBJson::getResultObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
            $this->_app->response->setBody(Link::encodeLink($links));
            $this->_app->response->setStatus(200);
        } else{
            Logger::Log("GET GetLink failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
        }
    }

    /**
     * PUT EditComponent
     *
     * @param $componentid a database component identifier
     */
    public function editComponent($componentid)
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($componentid));
                            
        $insert = Component::decodeComponent($this->_app->request->getBody());
        if (!is_array($insert))
            $insert = array($insert, false);

        foreach ($insert as $in){
            $data = $in->getInsertData();
            
            // starts a query
            eval("\$sql = \"".file_get_contents("Sql/PutComponent.sql")."\";");
            $query_result = DBRequest::request($sql);
            
            // checks the correctness of the query
            if (!$query_result['errno'] && $query_result['content']){
                $this->_app->response->setStatus(201);  
            } else{
                Logger::Log("PUT EditComponent failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
            }
        }
    }
    
    /**
     * DELETE DeleteComponent
     *
     * @param $componentid a database component identifier
     */
    public function deleteComponent($componentid)
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($componentid));
                            
        // starts a query
        eval("\$sql = \"".file_get_contents("Sql/DeleteComponent.sql")."\";");
        $query_result = DBRequest::request($sql, false);
        
        // checks the correctness of the query
        if (!$query_result['errno'] && $query_result['content']){
            $this->_app->response->setStatus(201);
        } else{
            Logger::Log("DELETE DeleteComponent failed",LogLevel::ERROR);
            $this->_app->response->setStatus(451);
        }
    }
    
    /**
     * POST SetComponent
     */
    public function setComponent()
    {
        $insert = Component::decodeComponent($this->_app->request->getBody());
        if (!is_array($insert))
            $insert = array($insert);

        foreach ($insert as $in){
            $values = $in->getInsertData();
            
            // starts a query
            eval("\$sql = \"".file_get_contents("Sql/PostComponent.sql")."\";");
            $query_result = DBRequest::request($sql, false);                
           
            if (!$query_result['errno'] && $query_result['content']){
                $data = DBJson::getRows($query_result['content']);
                $queryResult = Query::decodeQuery($data);
                
                $obj = new Component();
                $obj->setId($queryResult->getInsertId());
            
                $this->_app->response->setBody(Component::encodeComponent($obj)); 
                $this->_app->response->setStatus(201);
                
            } else{
                Logger::Log("POST SetComponent failed",LogLevel::ERROR);
                $this->_app->response->setStatus(451);
            }
        }
    }
    
    /**
     * GET GetComponent
     *
     * @param $componentid a database component identifier
     */
    public function getComponent($componentid)
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($componentid));
                            
        // starts a query
        eval("\$sql = \"".file_get_contents("Sql/GetComponent.sql")."\";");
        $query_result = DBRequest::request($sql, false);
        
        // checks the correctness of the query
        if (!$query_result['errno'] && $query_result['content']){
            $data = DBJson::getRows($query_result['content']);
            $components = DBJson::getResultObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
            $this->_app->response->setBody(Component::encodeComponent($components));
            $this->_app->response->setStatus(200);
        } else{
            Logger::Log("GET GetComponent failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
        }
        


    }
    
    /**
     * GET GetComponentDefinitions
     */
    public function getComponentDefinitions()
    {
        // starts a query
        eval("\$sql = \"".file_get_contents("Sql/GetComponentDefinitions.sql")."\";");
        $query_result = DBRequest::request($sql, false);

        // checks the correctness of the query
        if (!$query_result['errno'] && $query_result['content']){
            $data = DBJson::getRows($query_result['content']);

            $components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
            $links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
            $result = DBJson::concatResultObjectLists($data, $components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$links,Link::getDBPrimaryKey());  
            $this->_app->response->setBody(Component::encodeComponent($result));
            $this->_app->response->setStatus(200);
        } else{
            Logger::Log("GET GetComponentDefinitions failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
        }
        

    }
    
    /**
     * GET GetComponentDefinition
     *
     * @param $componentid a database component identifier
     */
    public function getComponentDefinition($componentid)
    {
        // checks whether incoming data has the correct data type
        DBJson::checkInput($this->_app, 
                            ctype_digit($componentid));
                            
        // starts a query
        eval("\$sql = \"".file_get_contents("Sql/GetComponentDefinition.sql")."\";");
        $query_result = DBRequest::request($sql, false);
        
        // checks the correctness of the query
        if (!$query_result['errno'] && $query_result['content']){
            $data = DBJson::getRows($query_result['content']);

            $Components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
            $Links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
            $result = DBJson::concatResultObjectLists($data, $Components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
            if (count($result)>0)
                $this->_app->response->setBody(Component::encodeComponent($result[0]));
                $this->_app->response->setStatus(200);
        } else{
            Logger::Log("GET GetComponentDefinition failed",LogLevel::ERROR);
            $this->_app->response->setStatus(409);
        }      
    }
    
    /**
     * GET SendComponentDefinitions
     */
    public function sendComponentDefinitions()
    {
        // starts a query
        eval("\$sql = \"".file_get_contents("Sql/GetComponentDefinitions.sql")."\";");
        $query_result = DBRequest::request($sql, false);

        // checks the correctness of the query
        if (!$query_result['errno'] && $query_result['content']){
            $data = DBJson::getRows($query_result['content']);

            $Components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
            $Links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
            $result = DBJson::concatResultObjectLists($data, $Components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
        
            foreach ($result as $object){
                $object = Component::decodeComponent(Component::encodeComponent($object));
                
                $result = Request::post($object->getAddress()."/component",array(),Component::encodeComponent($object));
                echo $result['status']. '--' . $object->getName() . '--' . $object->getAddress() . "\n";
            
                if ($result['status'] != 201){
                    $add = "";
                    if (isset($result['content']))
                        $add = $result['content'];
         
                    Logger::Log($result['status'] . '--' . $object->getName() . '--' . $object->getAddress() . "\n" . $add . "\n",LogLevel::ERROR);
                }
            }
            $this->_app->response->setStatus(200);
        } else{
            Logger::Log("GET SendComponentDefinitions failed",LogLevel::ERROR);
                $this->_app->response->setStatus(isset($query_result['status']) ? $query_result['status'] : 409);
        }
    }

}
?>