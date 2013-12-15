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
        
        // PUT EditLink
        $this->app->put('/link/:linkid',
                        array($this,'editLink'));
        
        // DELETE DeleteLink
        $this->app->delete('/link/:linkid',
                           array($this,'deleteLink'));
        
        // POST SetLink
        $this->app->post('/link',
                         array($this,'setLink'));
                         
        // GET GetLink
        $this->app->get('/link/:linkid',
                         array($this,'getLink'));

                         
                                                  

        // PUT EditComponent
        $this->app->put('/component/:componentid',
                        array($this,'editComponent'));
        
        // DELETE DeleteComponent
        $this->app->delete('/component/:componentid',
                           array($this,'deleteComponent'));
        
        // POST SetComponent
        $this->app->post('/component',
                         array($this,'setComponent'));
                         
        // GET GetComponent
        $this->app->get('/component/:componentid',
                         array($this,'getComponent'));                               
                         
  
  
                         
        // GET GetComponentDefinitions
        $this->app->get('/definition',
                         array($this,'getComponentDefinitions'));
                         
        // GET GetComponentDefinition
        $this->app->get('/definition/:componentid',
                         array($this,'getComponentDefinition'));
                         
        // GET SendComponentDefinitions
        $this->app->get('/send',
                         array($this,'sendComponentDefinitions'));
                
        // run Slim
        $this->app->run();

    }
    
    
    
     /**
     * PUT EditLink
     *
     * @param $linkid (description)
     */
    public function editLink($linkid)
    {
        $value = DBJson::getUpdateDataFromInput($this->app->request->getBody(), Link::getDBConvert(), ',');

        eval("\$sql = \"".implode('\n',file("Sql/PutLink.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
    }
    
    /**
     * (description)
     *
     * @param $linkid (description)
     */
    // DELETE DeleteLink
    public function deleteLink($linkid)
    {
        eval("\$sql = \"".implode('\n',file("Sql/DeleteLink.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
    }
    
    /**
     * POST SetLink
     */
    public function setLink()
    {
        $body = $this->app->request->getBody();
        $component = Component::decodeComponent($body);
        $links = $component->getLinks();
        $insert = array();
        foreach ($links as $link){
           $res = (array) $link;
           $res['CL_id_owner'] = $component->getID();
           $res['CL_id_target'] = "???";
           array_push($insert, $res);
        }
        
        $insert = DBJson::getInsertDataFromInput($insert, Link::getDBConvert(), ',');
        foreach ($insert as $in){
            $columns = $in[0];
            $value = $in[1];
            eval("\$sql = \"".implode('\n',file("Sql/PostLink.sql"))."\";");
           $query_result = DBRequest::request($sql);
        }
        $this->app->response->setStatus(200);
    }
    
    /**
     * GET GetLink
     *
     * @param $linkid (description)
     */
    public function getLink($linkid)
    {
        eval("\$sql = \"".implode('\n',file("Sql/GetLink.sql"))."\";");
        $query_result = DBRequest::request($sql);
        
        $data = DBJson::getRows($query_result);
        $links = DBJson::getResultObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
        $this->app->response->setBody(Link::encodeLink($links));
        $this->app->response->setStatus(200);
    }



 
    /**
     * PUT EditComponent
     *
     * @param $componentid (description)
     */
    public function editComponent($componentid)
    {
        $value = DBJson::getUpdateDataFromInput($this->app->request->getBody(), Component::getDBConvert(), ',');

        eval("\$sql = \"".implode('\n',file("Sql/PutComponent.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
    }
    
    /**
     * DELETE DeleteComponent
     *
     * @param $componentid (description)
     */
    public function deleteComponent($componentid)
    {
        eval("\$sql = \"".implode('\n',file("Sql/DeleteComponent.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
    }
    
    /**
     * POST SetComponent
     */
    public function setComponent()
    {
        $insert = DBJson::getInsertDataFromInput($this->app->request->getBody(), Component::getDBConvert(), ',');
        foreach ($insert as $in){
            $columns = $in[0];
            $value = $in[1];
            eval("\$sql = \"".implode('\n',file("Sql/PostComponent.sql"))."\";");
           $query_result = DBRequest::request($sql);
        }
        $this->app->response->setStatus(200);
    }
    
    /**
     * GET GetComponent
     *
     * @param $componentid (description)
     */
    public function getComponent($componentid)
    {
        eval("\$sql = \"".implode('\n',file("Sql/GetComponent.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $data = DBJson::getRows($query_result);
        $components = DBJson::getResultObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
        $this->app->response->setBody(Component::encodeComponent($components));
        $this->app->response->setStatus(200);
    }
    
    
    
    
    /**
     * GET GetComponentDefinitions
     *
     * @param $userid (description)
     */
    public function getComponentDefinitions()
    {
        eval("\$sql = \"".implode('\n',file("Sql/GetComponentDefinitions.sql"))."\";");
        $query_result = DBRequest::request($sql);
        $this->app->response->setStatus(200);
        $data = DBJson::getRows($query_result);

        $components = DBJson::getObjectsByAttributes($data, Component::getDBPrimaryKey(), Component::getDBConvert());
        $links = DBJson::getObjectsByAttributes($data, Link::getDBPrimaryKey(), Link::getDBConvert());
        $result = DBJson::concatObjectLists($data, $components,Component::getDBPrimaryKey(),Component::getDBConvert()['CO_links'] ,$links,Link::getDBPrimaryKey());  
        $this->app->response->setBody(Component::encodeComponent($result));
    }
    
    /**
     * GET GetComponentDefinition
     *
     * @param $userid (description)
     */
    public function getComponentDefinition($componentid)
    {
        eval("\$sql = \"".implode('\n',file("Sql/GetComponentDefinition.sql"))."\";");
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
     * GET SendComponentDefinitions
     *
     * @param $courseid (description)
     */
    public function sendComponentDefinitions()
    {
        eval("\$sql = \"".implode('\n',file("Sql/GetComponentDefinitions.sql"))."\";");
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