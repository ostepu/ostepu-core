<?php
/**
 * @file (filename)
 * %(description)
 */ 

require 'Slim/Slim.php';
include 'include/Component.php';
include 'include/structures.php';
include 'include/Request.php';
include 'include/DBRequest.php';
include 'include/DBJson.php';

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
                        array($this,'EditComponentDefinition'));
        
        // DELETE DeleteComponentDefinition
        $this->app->delete('/component/:componentid',
                           array($this,'DeleteComponentDefinition'));
        
        // POST SetComponentDefinition
        $this->app->post('/component',
                         array($this,'SetComponentDefinition'));
                         
        // GET GetComponentDefinitions
        $this->app->get('/component',
                         array($this,'GetComponentDefinitions'));
                         
        // GET SendComponentDefinitions
        $this->app->get('/component/send',
                         array($this,'SendComponentDefinitions'));
                
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
    public function EditComponentDefinition($componentid){
      //  $this->app->response->setStatus(200);
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // DELETE DeleteComponentDefinition
    public function DeleteComponentDefinition($courseid){
     //   $this->app->response->setStatus(252);
    }
    
    /**
     * (description)
     *
     * @param $path (description)
     */
    // POST SetComponentDefinition
    public function SetComponentDefinition(){
       // $this->app->response->setStatus(201);
    }
    
    /**
     * (description)
     *
     * @param $userid (description)
     */
    // GET GetComponentDefinitions
    public function GetComponentDefinitions(){
        eval("\$sql = \"".implode('\n',file("include/sql/GetComponentDefinitions.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
        $data = DBJson::GetRows($query_result);

        $Components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
        $Links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
        $result = DBJson::ConcatObjectLists($data, $Components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
        $this->app->response->setBody(Component::encodeComponent($result));
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     */
    // GET SendComponentDefinitions
    public function SendComponentDefinitions(){
        eval("\$sql = \"".implode('\n',file("include/sql/GetComponentDefinitions.sql"))."\";");
        $query_result = DBRequest::request($sql);
       
        $this->app->response->setStatus(200);
        $data = DBJson::GetRows($query_result);

        $Components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
        $Links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
        $result = DBJson::ConcatObjectLists($data, $Components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$Links,Link::getDBPrimaryKey());  
        
        $request = new multiRequest();   
        foreach ($result as $object){
        $object = Component::decodeComponent(Component::encodeComponent($object));
        $ch = createRequest::createPost($object->getAddress()."/component",array(),Component::encodeComponent($object));
        $request->addRequest($ch); 
        }
        $res = $request->run();
     //   echo var_dump($res);
    }

}
?>