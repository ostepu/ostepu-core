<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
include_once( 'include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * The Attachment class
 *
 * This class handles everything belongs to Attachments
 */
class Attachment
{   
    /**
     *Values needed for conversation with other components
     */
     
    private $_conf=null;
    
    private static $_prefix = "attachment";
    
    public static function getPrefix()
    {
        return Attachment::$_prefix;
    }
    public static function setPrefix($value)
    {
        Attachment::$_prefix = $value;
    }

    /**
     *Address of the Logic-Controller
     *dynamic set by CConf below
     */
    private $lURL = ""; 
    
    public function __construct($conf)
    {    
        /**
         *Initialise the Sli-Framework
         */
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        /**
         *Set the Logiccontroller-URL
         */
        $this->_conf = $conf;
        $this->query = array();
        
        $this->query = array(CConfig::getLink($conf->getLinks(),"controller"));
        $this->lURL = $querry['address'];
        
        //POST AddAttachment
        $this->app->post('/course/:courseid/exercisesheet/:exercisesheetid', 
                        array($this, 'addAttachment'));
        
        //GET GetAttachmentURL
        $this->app->get('/file/:fileid', 
                        array ($this, 'getAttachmentURL'));
        
        //DELETE DeleteAttachment
        $this->app->delete('/file/:fileid', 
                        array($this, 'deleteAttachment'));
                        
        //PUT EditAttachment
        $this->app->put('/file/:fileid', 
                        array($this, 'editAttachment'));
        //run Slim
        $this->app->run();
    }    
    
    /**
     * Funktion to add attachment to FS and DB 
     * takes two arguments and retunrs a Status-Code
     * @param $courseid an integer identifies the Course
     * @param $exercisesheetid an integer identifies the exercisesheet
     */
    public function addAttachment($courseid, $exercisesheetid){     
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        /**
         *Request to FS 
         */
        $URL = $this->lURL.'/FS/course/'.$courseid.'/exercisesheet/'.$exercisesheetid;
        $answer = Request::custom('POST', $URL, $header, $body);
        
        /**
         * if Request to FS was succsessfull add new Line to DB
         * otherwise return status from FS
         */
        if($answer['status'] == 200){ 
            $body = $answer['content'];
            $URL = $this->lURL.'/DB/course/'.$courseid.'/exercisesheet/'.$exercisesheetid;
            $answer = Request::custom('POST', $URL, $header, $body);
            $this->app->response->setStatus($answer['status']);
        } else {
            $this->app->response->setStatus($answer['status']);
        }
    }
    /**
     * Funktion to get the address of an attachment
     * takes one argument and retunrs a URL
     * @param $fileid an integer identifies the file
     */
    public function getAttachmentURL($fileid) {        
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/file/'.$fileid;        
        $answer = Request::custom('GET', $URL, $header, $body);  
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Funktion to delete an attachment from FS and DB 
     * takes one argument and retunrs a Status-Code
     * @param $fileid an integer identifies the file
     */
    public function deleteAttachment($fileid){ 
        /**
         * Request to DB 
         */
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/file/'.$fileid;        
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        
        /**
         * if DB-Request was succsessfull the file also gets removed from FS 
         * otherwise returns the Status-Code from DB 
         */
        if( $answer['status'] == 200){ 
            $URL = $this->lURL.'/FS/file/'.$fileid; 
            /*
             * eigentlich url der zu löschenden datei schicken und nicht die id???????????????
             */
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }             
    }
    /**
     * Funktion to edit an existing attachment
     * takes one argument and retunrs a Status-Code
     * @param $file an integer identifies the file
     */
    public function editAttachment($fileid){  
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        /**
         *Request to FS 
         */
        $URL = $this->lURL.'/FS/file/'.$fileid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        
        /**
         * if the new file is succsessfully saved in FS 
         * update the according entry at DB
         */
        if($answer['status'] == 200){ 
            $body->{'_file'} = $answer['content'];
            $URL = $this->lURL.'/DB/marking/'.$markingid.'/tutor/'.$tutorid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        } else {
            $this->app->response->setStatus($answer['status']);
        }
    }   
}
/**
 * get new Config-Datas from DB 
 */
$com = new CConfig(Attachment::getPrefix());

/**
 * make a new instance of Attachment-Class with the Config-Datas
 */
if (!$com->used())
    new Attachment($com->loadConfig());

?>