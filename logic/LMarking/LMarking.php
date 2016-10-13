<?php
/**
 * @file LMarking.php Contains the LMarking class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';
include_once ( dirname(__FILE__). '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LMarking-Component
 */
class LMarking
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "marking";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LMarking::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LMarking::$_prefix = $value;
    }

    private $_file = array( );
    private $_marking = array( );

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
        $com = new CConfig( LMarking::getPrefix( ), dirname(__FILE__) );

        // runs the LMarking
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );

        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->_file = CConfig::getLinks($conf->getLinks(),"file");
        $this->_marking = CConfig::getLinks($conf->getLinks(),"marking");

        // POST AddMarking
        $this->app->post('/'.$this->getPrefix().'(/)',
                        array($this, 'addMarking'));

        // DELETE DeleteMarking
        $this->app->delete('/'.$this->getPrefix().'/marking/:markingid(/)',
                        array($this, 'deleteMarking'));

        // run Slim
        $this->app->run();
    }

    /**
     * Adds a new marking.
     *
     * Called when this component receives an HTTP POST request to
     * /marking(/).
     * The request body should contain a JSON object representing the new marking.
     *
     * @author Till Uhlig
     * @date 2014
     */
    public function addMarking(){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();

        $markings = Marking::decodeMarking($body);

        $this->app->response->setStatus( 201 );

        // always been an array
        $arr = true;
        if ( !is_array( $markings ) ){
            $markings = array( $markings );
            $arr = false;
        }

        // this array contains the inserted objects
        $res = array( );

        foreach ( $markings as $marking ){
            if ($marking->getDate()===null) $marking->setDate(time());

            $file = $marking->getFile();
            if (!isset($file)) {$file = new File();}
            if ($file->getTimeStamp()===null) $file->setTimeStamp(time());

            // upload file to filesystem
            $result = Request::routeRequest(
                                    'POST',
                                    '/file',
                                    $this->app->request->headers->all( ),
                                    File::encodeFile($file),
                                    $this->_file,
                                    'file'
                                    );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                // file is uploaded
                $newFile = File::decodeFile($result['content']);
                $file->setAddress($newFile->getAddress());
                $file->setHash($newFile->getHash());
                $file->setFileId($newFile->getFileId());
                $file->setBody(null);
                $marking->setFile($file);

                // upload marking to database
                if ($marking->getId()===null){
                    $result = Request::routeRequest(
                                                    'POST',
                                                    '/marking',
                                                    $this->app->request->headers->all( ),
                                                    Marking::encodeMarking($marking),
                                                    $this->_marking,
                                                    'marking'
                                                    );
                }
                else{
                    $result = Request::routeRequest(
                                                    'PUT',
                                                    '/marking/marking/'.$marking->getId(),
                                                    $this->app->request->headers->all( ),
                                                    Marking::encodeMarking($marking),
                                                    $this->_marking,
                                                    'marking'
                                                    );
                }

                if ( $result['status'] >= 200 &&
                     $result['status'] <= 299 ){
                    // marking is uploaded
                    $newmarking = Marking::decodeMarking($result['content']);
                    if (is_array($newmarking)) $newmarking = $newmarking[0];

                    if ($newmarking->getId()!==null)
                        $marking->setId($newmarking->getId());

                    $res[] = $marking;
                    if ( isset( $result['headers']['Content-Type'] ) )
                        $this->app->response->headers->set(
                                                            'Content-Type',
                                                            $result['headers']['Content-Type']
                                                            );
                } else {
                    Logger::Log(
                                'POST AddMarking failed',
                                LogLevel::ERROR
                                );
                    $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                    $this->app->response->setBody( Marking::encodeMarking( $res ) );
                    $this->app->stop( );
                }

            } else {
                Logger::Log(
                            'POST AddMarking failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->response->setBody( Marking::encodeMarking( $res ) );
                $this->app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->app->response->setBody( Marking::encodeMarking( $res[0] ) );

        } else
            $this->app->response->setBody( Marking::encodeMarking( $res ) );
    }

    /**
     * Deletes a marking.
     *
     * Called when this component receives an HTTP DELETE request to
     * /marking/marking/$markingid(/).
     *
     * @param int $markingid The id of the marking that is being deleted.
     *
     * @author Till Uhlig
     * @date 2014
     */
    public function deleteMarking($markingid){
        $result = Request::routeRequest(
                                        'DELETE',
                                        '/marking/marking/'.$markingid,
                                        $this->app->request->headers->all( ),
                                        '',
                                        $this->_marking,
                                        'marking'
                                        );

        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){

            // marking is deleted
            $this->app->response->setStatus( 201 );
            $this->app->response->setBody( '' );
            if ( isset( $result['headers']['Content-Type'] ) )
                $this->app->response->headers->set(
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );
        } else {
            Logger::Log(
                        'DELETE DeleteMarking failed',
                        LogLevel::ERROR
                        );
            $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->app->response->setBody( '' );
            $this->app->stop( );
        }
    }
}