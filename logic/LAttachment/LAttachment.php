<?php
/**
 * @file LAttachment.php Contains the LAttachment class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Peter König <upbkgs20@arcor.de>
 * @date 2014
 * @author Martin Daute <martin.daute@student.uni-halle.de>
 * @date 2014
 */

require '../../Assistants/vendor/Slim/Slim/Slim.php';
include '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';
include '../Include/LFileHandler.php';

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
    private $_postFile = array();
    private $_postAttachment = array();

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LAttachment::getPrefix( ), dirname(__FILE__) );

        // runs the LAttachment
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );

        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->setStatus( 409 );
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");
        $this->_postFile = CConfig::getLinks($conf->getLinks(),"postFile");
        $this->_postAttachment = CConfig::getLinks($conf->getLinks(),"postAttachment");

        // initialize lURL
        $this->lURL = $this->query->getAddress();

        //POST AddAttachment
        $this->app->post('/'.$this->getPrefix().'(/)',
                        array($this, 'addAttachment'));

        //GET GetAttachment
        $this->app->get('/'.$this->getPrefix().'/attachment/:attachmentid(/)',
                        array ($this, 'getAttachment'));

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
        $fileObjects = Attachment::decodeAttachment($body);

        // always been an array
        $arr = true;
        if ( !is_array( $fileObjects ) ){
            $fileObjects = array( $fileObjects );
            $arr = false;
        }

        $res = array( );
        $files=array();

        foreach ( $fileObjects as $fileObject )
            $files[] = $fileObject->getFile();

        //add the Files
        $result = Request::routeRequest(
                                        'POST',
                                        '/file',
                                        array(),
                                        File::encodeFile($files),
                                        $this->_postFile,
                                        'file'
                                        );

        $tempFiles = File::decodeFile($result['content']);

          // checks the correctness of the query
        if ( $result['status'] === 201 && isset($result['content'])){

            // upload files
            $countObjects = count($fileObjects);
            for($i=0;$i<$countObjects;$i++){
                if ($tempFiles[$i]->getStatus() === 201){
                    $fileObjects[$i]->setFile($tempFiles[$i]);

                    if ($files[$i] !== null)
                        $files[$i]->setStatus(201);
                } else {
                    $fileObjects[$i]->setStatus(409);
                    $fileObjects[$i]->addMessage("Die Datei konnte nicht gespeichert werden.");
                    if ($files[$i] !== null)
                        $files[$i]->setBody();
                }
            }
        } else {
            $this->app->response->setStatus(409);
            $this->app->response->setBody( Attachment::encodeAttachment( new Attachment()) );
            $this->app->stop();
        }

        // upload attachments
        $result = Request::routeRequest(
                                        'POST',
                                        '/attachment',
                                        array(),
                                        Attachment::encodeAttachment($fileObjects),
                                        $this->_postAttachment,
                                        'attachment'
                                        );

        if ( $result['status'] === 201 && isset($result['content'])){
            $tempAttachments = Attachment::decodeAttachment($result['content']);

            $countObjects = count($fileObjects);
            for($i=0;$i<$countObjects;$i++){
                $fileObjects[$i]->setStatus($tempAttachments[$i]->getStatus());
                $fileObjects[$i]->addMessages($tempAttachments[$i]->getMessages());

                if ($tempAttachments[$i]->getStatus() !== 201){
                    $fileObjects[$i]->addMessage('Anhang konnte nicht erstellt werden.');
                    $fileObjects[$i]->getFile()->setBody();
                } else
                    $fileObjects[$i]->setId($tempAttachments[$i]->getId());

                $res[] = $fileObjects[$i];
            }

        } else {
            $this->app->response->setStatus(409);
            $this->app->response->setBody( Attachment::encodeAttachment( new Attachment()) );
            $this->app->stop();

        }

        if ( !$arr &&
             count( $res ) == 1 )
            $res = $res[0];

        $this->app->response->setBody( Attachment::encodeAttachment( $res ) );
        $this->app->response->setStatus( 201 );
    }

    /**
     * Returns an attachment.
     *
     * Called when this component receives an HTTP GET request to
     * /attachment/attachment/$attachmentid(/).
     *
     * @param int $attachmentid The id of the attachment that should be returned.
     */
    public function getAttachment($attachmentid) {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/attachment/attachment/'.$attachmentid;
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
        //getAttachment to get the file of the Attachment
        $URL = $this->lURL.'/DB/attachment/attachment/'.$attachmentid;
        $answer = Request::custom('GET', $URL, $header, "");
        $body = json_decode($answer['content'], true);

        if (isset($body['file'])) {
            // request to database
            $URL = $this->lURL.'/DB/attachment/'.$attachmentid;
            $answer = Request::custom('DELETE', $URL, $header, $body);
            $this->app->response->setStatus($answer['status']);

            // delete the file
            LFileHandler::delete($this->lURL, $header, $body['file']);
        } else {
            $this->app->response->setStatus(409);
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
        if (isset($body['file']['body']))
        {
            //getAttachment to get the file of the old Attachment
            $URL = $this->lURL.'/DB/attachment/attachment/'.$attachmentid;
            $answer = Request::custom('GET', $URL, $header, "");
            $bodyOld = json_decode($answer['content'], true);
            //save the new file
            $body['file'] = LFileHandler::add($this->lURL, $header, $body['file']);


            // if file has not been saved
            if(empty($body['file'])){
                $this->app->response->setStatus(409);
            } else { // if file has been saved
                //save the new information
                $URL = $this->lURL.'/DB/attachment/attachment/'.$attachmentid;
                $answer = Request::custom('PUT', $URL, $header, json_encode($body));
                $this->app->response->setStatus($answer['status']);
            }

            // delete the old file
            LFileHandler::delete($this->lURL, $header, $bodyOld['file']);
        } else {
            // save the new information
            $URL = $this->lURL.'/DB/attachment/attachment/'.$attachmentid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }
}