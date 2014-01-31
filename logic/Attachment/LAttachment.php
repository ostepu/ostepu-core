<?php 
/**
 * @file LAttachment.php Contains the LAttachment class
 * 
 * @author Martin Daute
 * @author Peter Koenig
 * @author Christian Elze 
 */

require 'Slim/Slim.php';
include './Include/Request.php';
include_once( './Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LAttachment-Component
 */
class LAttachment
{
    /**
     * @var Component $_conf the component data object
     */  
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "attachment";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LAttachment::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LAttachment::$_prefix = $value;
    }

    /**
     * @var string $lURL the URL of the logic-controller
     */ 
    private $lURL = ""; // readed out from config below

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
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");

        // initialize lURL
        $this->lURL = $this->query->getAddress();

        //POST AddAttachment
        $this->app->post('/'.$this->getPrefix().'(/)', 
                        array($this, 'addAttachment'));

        //GET GetAttachmentURL
        $this->app->get('/'.$this->getPrefix().'/attachment/:attachmentid(/)',
                        array ($this, 'getAttachmentURL'));

        //DELETE DeleteAttachment
        $this->app->delete('/'.$this->getPrefix().'/attachment/:attachmentid(/)',
                        array($this, 'deleteAttachment'));

        //PUT EditAttachment
        $this->app->put('/'.$this->getPrefix().'/attachment/:attachmentid(/)',
                        array($this, 'editAttachment'));
        //run Slim
        $this->app->run();
    }

    /**
     * Adds an attachment.
     *
     * Called when this component receives an HTTP POST request to
     * /attachment(/).
     * The request body should contain a JSON object representing the 
     * attachment's attributes.
     */
    public function addAttachment(){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $body = json_decode($body, true);
        $file = $body['file'];
        // Request to FS
        $URL = $this->lURL.'/FS/file';
        $answer = Request::custom('POST', $URL, $header, json_encode($file));

        /*
         * if the file has been stored, the information
         * belongs to this attachment will be stored in the database
         */
        if($answer['status'] >= 200 && $answer['status'] < 300){ 
            $body['file'] = json_decode($answer['content'], true);
            $URL = $this->lURL.'/DB/attachment';
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        } else {
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Returns the URL to a given attachment.
     *
     * Called when this component receives an HTTP GET request to
     * /exercisesheet/exercisesheet/$sheetid/url(/).
     *
     * @param int $sheetid The id of the exercise sheet the returned URL belongs to.
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
     * Deletes an attachment.
     *
     * Called when this component receives an HTTP DELETE request to
     * /attachment/attachment/$attachmentid(/).
     *
     * @param string $attachmentid The id of the attachment that is being deleted.
     */
    public function deleteAttachment($attachmentid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        // request to database
        $URL = $this->lURL.'/DB/attachment/'.$attachmentid;
        $answer = Request::custum('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /*
         * if the file information has been deleted, the file
         * will being deleted from filesystem
         */
        $fileObject = json_decode($answer['content'], true);
        //if address-field exists, read it out
        if (isset($fileObject['address']) &&
            $answer['status'] >= 200 && $answer['status'] < 300){
                $fileAddress = $fileObject['address'];
                // request to filesystem
                $URL = $this->lURL.'/FS/file/'.$fileAddress;
                $answer = Request::custom('DELETE', $URL, $header, $body);
        }
    }

    /**
     * Edits an attachment.
     *
     * Called when this component receives an HTTP PUT request to
     * /attachment/attachment/$attachmentid(/).
     * The request body should contain a JSON object representing the 
     * attachment's new attributes.
     *
     * @param string $attachmentid The id of the attachment that is being updated.
     */
    public function editAttachment($attachmentid){  
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $body = json_decode($body, true);
        $file = $body['file'];
        // Request to FS
        $URL = $this->lURL.'/FS/file';
        $answer = Request::custom('POST', $URL, $header, json_encode($file));

        /*
         * if the file has been stored, the information
         * belongs to this attachment will be stored in the database
         */
        if($answer['status'] >= 200 && $answer['status'] < 300){
            $body['file'] = json_decode($answer['content'], true);
            $URL = $this->lURL.'/DB/attachment/'.$attachmentid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        } else {
            $this->app->response->setStatus($answer['status']);
        }
    }   
}

// get new config data from DB
$com = new CConfig(LAttachment::getPrefix());

// create a new instance of LExercisesheet class with the config data
if (!$com->used())
    new LAttachment($com->loadConfig());

?>