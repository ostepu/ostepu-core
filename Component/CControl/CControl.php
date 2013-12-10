<?php
/**
 * @file (filename)
 * %(description)
 */ 

require 'Include/Slim/Slim.php';
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DbRequest.php' );
include_once( 'Include/DbJson.php' );

\Slim\Slim::registerAutoloader();

new CControl();

/**
 * (description)
 */
class CControl
{
    public function __construct(){
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // PUT EditComponentDefinition
        $this->app->put('/component/:componentid',
                        array($this,'editComponentDefinition'));
        
        // DELETE DeleteComponentDefinition
        $this->app->delete('/component/:componentid',
                           array($this,'deleteComponentDefinition'));
        
        // POST SetComponentDefinition
        $this->app->post('/component',
                         array($this,'setComponentDefinition'));
                         
        // GET GetComponentDefinitions
        $this->app->get('/component',
                         array($this,'getComponentDefinitions'));
                         
        // GET SendComponentDefinitions
        $this->app->get('/component/send',
                         array($this,'sendComponentDefinitions'));
                
        if (strpos ($this->app->request->getResourceUri(),"/component")===0){
            // run Slim
            $this->app->run();
        }
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // PUT EditComponentDefinition
    public function editComponentDefinition($componentid){
      //  $this->app->response->setStatus(200);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // DELETE DeleteComponentDefinition
    public function deleteComponentDefinition($courseid){
     //   $this->app->response->setStatus(252);
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    // POST SetComponentDefinition
    public function setComponentDefinition(){
       // $this->app->response->setStatus(201);
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetComponentDefinitions
    public function getComponentDefinitions(){
        eval("\$sql = \"".implode('\n',file("include/sql/GetComponentDefinitions.sql"))."\";");
        $query_result = DbRequest::request($sql);
        $this->app->response->setStatus(200);
        $data = DbJson::getRows($query_result);

        $Components = DBJson::getObjectsByAttributes($data, Component::getDbPrimaryKey(), Component::getDBConvert());
        $Links = DBJson::getObjectsByAttributes($data, Link::getDbPrimaryKey(), Link::getDBConvert());
        $result = DBJson::concatObjectLists($data, $Components,Component::getDbPrimaryKey(),Component::getDbConvert()['CO_links'] ,$Links,Link::getDbPrimaryKey());  
        $this->app->response->setBody(Component::encodeComponent($result));
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET SendComponentDefinitions
    public function sendComponentDefinitions(){
        eval("\$sql = \"".implode('\n',file("include/sql/GetComponentDefinitions.sql"))."\";");
        $query_result = DbRequest::request($sql);
       
        $this->app->response->setStatus(200);
        $data = DBJson::getRows($query_result);

        $Components = DBJson::getObjectsByAttributes($data, Component::getDbPrimaryKey(), Component::getDbConvert());
        $Links = DbJson::getObjectsByAttributes($data, Link::getDbPrimaryKey(), Link::getDBConvert());
        $result = DbJson::concatObjectLists($data, $Components,Component::getDbPrimaryKey(),Component::getDbConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
        
        $request = new Request_MultiRequest();   
        foreach ($result as $object){
        $object = Component::decodeComponent(Component::encodeComponent($object));
        $ch = Request_CreateRequest::createPost($object->getAddress()."/component",array(),Component::encodeComponent($object));
        $request->addRequest($ch); 
        }
        $res = $request->run();
    }

}
?>