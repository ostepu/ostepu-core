<?php
/**
 * @file (filename)
 * %(description)
 */ 

require 'Include/Slim/Slim.php';
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBRequest.php' );
include_once( 'Include/DBJson.php' );

\Slim\Slim::registerAutoloader();

new CControl();

/**
 * (description)
 */
class CControl
{
    public function __construct()
    {
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
                         
        // GET GetComponentDefinition
        $this->app->get('/component/:componentid',
                         array($this,'getComponentDefinition'));
                         
        // GET SendComponentDefinitions
        $this->app->get('/send/components',
                         array($this,'sendComponentDefinitions'));
                
        // run Slim
        $this->app->run();

    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // PUT EditComponentDefinition
    public function editComponentDefinition($componentid)
    {
      $this->app->response->setStatus(404);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // DELETE DeleteComponentDefinition
    public function deleteComponentDefinition($componentid)
    {
        $this->app->response->setStatus(404);
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    // POST SetComponentDefinition
    public function setComponentDefinition()
    {
       $this->app->response->setStatus(404);
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetComponentDefinitions
    public function getComponentDefinitions()
    {
        eval("\$sql = \"".implode('\n',file("include/sql/GetComponentDefinitions.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
        $data = DBJson::getRows($query_result);

        $Components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
        $Links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
        $result = DBJson::concatObjectLists($data, $Components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
        $this->app->response->setBody(Component::encodeComponent($result));
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetComponentDefinition
    public function getComponentDefinition($componentid)
    {
        eval("\$sql = \"".implode('\n',file("include/sql/GetComponentDefinition.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
        $data = DBJson::getRows($query_result);

        $Components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
        $Links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
        $result = DBJson::concatObjectLists($data, $Components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
        if (count($result)>0)
            $this->app->response->setBody(Component::encodeComponent($result[0]));
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET SendComponentDefinitions
    public function sendComponentDefinitions()
    {
        eval("\$sql = \"".implode('\n',file("include/sql/GetComponentDefinitions.sql"))."\";");
        $query_result = DBRequest::request($sql);
       

        $data = DBJson::getRows($query_result);

        $Components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
        $Links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
        $result = DBJson::concatObjectLists($data, $Components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
        
        foreach ($result as $object){
            $object = Component::decodeComponent(Component::encodeComponent($object));
            $result = Request::post($object->getAddress()."/component",array(),Component::encodeComponent($object));
            echo $object->getAddress() . '--' . $object->getName() . '--' . $result['status'] . "\n";
        }
        $this->app->response->setStatus(200);
    }

}
?>