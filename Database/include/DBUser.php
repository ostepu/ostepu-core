<?php
/**
* @file (filename)
* %(description)
*/ 


/**
* (description)
*/
class DBUser
{
    public function __construct(){
        $this->app = new \Slim\Slim();
        
        // PUT SetUserRights
        $this->app->put('/user/user/:userid/rights',
                        array($this,'SetUserRights'));
                        
        // PUT EditUser
        $this->app->put('/user/user/:userid',
                        array($this,'EditUser'));
                        
        // DELETE RemoveUser
        $this->app->put('/user/user/:userid',
                        array($this,'RemoveUser'));
                        
        // POST AddUser
        $this->app->put('/user',
                        array($this,'AddUser'));
                        
        // GET GetUsers
        $this->app->put('/user/user',
                        array($this,'GetUsers'));
                        
        // GET GetUser
        $this->app->put('/user/user/:userid',
                        array($this,'GetUser'));

        if (strpos ($this->app->request->getResourceUri(),"/user")==0){
            // run Slim
            $this->app->run();
        }
    }
    
    /**
    * (description)
    *
    * @param $userid (description)
    */
    // PUT SetUserRights
    public function SetUserRights($userid){
    
    }
    
    /**
    * (description)
    *
    * @param $userid (description)
    */
    // PUT EditUser
    public function EditUser($userid){
    
    }
    
    /**
    * (description)
    *
    * @param $userid (description)
    */
    // DELETE RemoveUser
    public function RemoveUser($userid){
    
    }
    
    /**
    * (description)
    */
    // POST AddUser
    public function AddUser(){
    
    }
    
    /**
    * (description)
    */
    // GET GetUsers
    public function GetUsers(){
    
    }
    
    /**
    * (description)
    *
    * @param $userid (description)
    */
    // GET GetUser
    public function GetUser($userid){
    
    }
}
?>