<?php
/**
 * @file (filename)
 * (description)
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/Structures.php' );
include_once( 'Include/Request.php' );
include_once( 'Include/DBJson.php' );
include_once( 'Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig(DBCourseStatus::getPrefix());

if (!$com->used())
    new DBCourseStatus($com->loadConfig());  
    
/**
 * (description)
 */
class DBCourseStatus
{
    private $_app=null;
    private $_conf=null;
    
    private $query=array();
    
    private static $_prefix = "coursestatus";
    
    public static function getPrefix()
    {
        return DBCourseStatus::$_prefix;
    }
    
    public static function setPrefix($value)
    {
        DBCourseStatus::$_prefix = $value;
    }
    
    public function __construct($conf){
        $this->_conf = $conf;
        $this->query = array(CConfig::getLink($conf->getLinks(),"out"));
        
        $this->_app = new \Slim\Slim();
        $this->_app->response->headers->set('Content-Type', 'application/json');

        // PUT EditMemberRight
        $this->app->put('/' . $this->getPrefix() . '/course/:courseid/user/:userid',
                        array($this, 'editMemberRight'));
                        
        // DELETE RemoveCourseMember
        $this->_app->delete('/' . $this->getPrefix() . '/course/:courseid/user/:userid', 
                            array($this,'removeCourseMember'));
                            
        // POST AddCourseMember
        $this->_app->post('/' . $this->getPrefix() . '/course/:courseid/user/:userid',
                         array($this,'addCourseMember'));
        
        // GET GetMemberRight
        $this->_app->get('/' . $this->getPrefix() . '/course/:courseid/user/:userid',
                        array($this,'getMemberRight'));
        
        if (strpos ($this->_app->request->getResourceUri(),'/' . $this->getPrefix()) === 0){
            // run Slim
            $this->_app->run();
        }
    }
    
    /**
     * PUT EditMemberRight
     *
     * @param $userid (description)
     */
    public function editMemberRight($courseid,$userid)
    {
 
    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     * @param $userid (description)
     */
    // DELETE RemoveCourseMember
    public function removeCourseMember($courseid,$userid)
    {

    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     * @param $userid (description)
     */
    // POST AddCourseMember
    public function addCourseMember($courseid,$userid)
    {

    }
    
    /**
     * (description)
     *
     * @param $courseid (description)
     * @param $userid (description)
     */
    // POST GetMemberRight
    public function getMemberRight($courseid,$userid)
    {

    }
}
?>