<?php 


/**
 * @file DBChoice.php contains the DBChoice class
 *
 * @author Till Uhlig
 */

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/Request.php' );
include_once ( '../../Assistants/DBJson.php' );
include_once ( '../../Assistants/DBRequest.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

// runs the CConfig
$com = new CConfig( DBChoice::getPrefix( ) . ',course,link' );

// runs the DBChoice
if ( !$com->used( ) )
    new DBChoice( $com );

/**
 * A class, to abstract the "DBChoice" table from database
 */
class DBChoice
{

    /**
     * @var Slim $_app the slim object
     */
    private $_app = null;

    /**
     * @var Component $_conf the component data object
     */
    private $_conf = null;

    /**
     * @var Link[] $query a list of links to a query component
     */
    private $query = array( );

    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */
    private static $_prefix = 'choice';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBChoice::$_prefix;
    }

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function setPrefix( $value )
    {
        DBChoice::$_prefix = $value;
    }

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct( $conf )
    {

        // initialize component
        $this->_conf = $conf;

        // initialize slim
        $this->_app = new \Slim\Slim( array('debug' => true) );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );
                                                                  
        // POST AddCourse
        $this->_app->post( 
                         '(/:preChoice(/:preForm(/:preExercise)))/course',
                         array( 
                               $this,
                               'addCourse'
                               )
                         );
                         
        // DELETE DeleteCourse
        $this->_app->delete( 
                         '(/:preChoice(/:preForm(/:preExercise)))/course(/course)/:courseid',
                         array( 
                               $this,
                               'deleteCourse'
                               )
                         );

        // PUT EditChoice
        $this->_app->put( 
                         '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '(/choice)/:choiceid(/)',
                         array( 
                               $this,
                               'editChoice'
                               )
                         );

        // DELETE DeleteChoice
        $this->_app->delete( 
                            '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '(/choice)/:choiceid(/)',
                            array( 
                                  $this,
                                  'deleteChoice'
                                  )
                            );

        // POST AddChoice
        $this->_app->post( 
                          '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '(/)',
                          array( 
                                $this,
                                'addChoice'
                                )
                          );

        // GET GetChoice
        $this->_app->get( 
                         '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '(/choice)/:choiceid(/)',
                         array( 
                               $this,
                               'getChoice'
                               )
                         );

        // GET GetCourseChoices
        $this->_app->get( 
                         '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '/course/:courseid(/)',
                         array( 
                               $this,
                               'getCourseChoices'
                               )
                         );
                         
        // GET GetExistsCourseChoices
        $this->_app->get( 
                         '(/:preChoice(/:preForm(/:preExercise)))/link/exists/course/:courseid(/)',
                         array( 
                               $this,
                               'getExistsCourseChoices'
                               )
                         );
                         
        // GET GetSheetChoices
        $this->_app->get( 
                         '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '/exercisesheet/:esid(/)',
                         array( 
                               $this,
                               'getSheetChoices'
                               )
                         );
                         
        // GET GetExerciseChoices
        $this->_app->get( 
                         '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '/exercise/:eid(/)',
                         array( 
                               $this,
                               'getExerciseChoices'
                               )
                         );
                         
        // GET GetFormChoices
        $this->_app->get( 
                         '(/:preChoice(/:preForm(/:preExercise)))/' . $this->getPrefix( ) . '/form/:formid(/)',
                         array( 
                               $this,
                               'getFormChoices'
                               )
                         );
                         
            // run Slim
            $this->_app->run( );
    }
    
    public function loadConfig( $preChoice='',  $preForm='',  $preExercise='')
    {
        // initialize component
        $this->_conf = $this->_conf->loadConfig( $preChoice, $preForm,  $preExercise );
        $this->query = array( CConfig::getLink( 
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );
    }
    
    public function editChoice( $preChoice='',  $preForm='',  $preExercise='', $choiceid )
    {
        $this->loadConfig($preChoice, $preForm,  $preExercise);
        $preChoice = ($preChoice === '' ? '' : '_') . $preChoice;
        $preForm = ($preForm === '' ? '' : '_') . $preForm;
        $preExercise = ($preExercise === '' ? '' : '_') . $preExercise;
        
        Logger::Log( 
                    'starts PUT EditChoice',
                    LogLevel::DEBUG
                    );

        $choiceid = DBJson::mysql_real_escape_string( $choiceid );
        $formid = DBJson::mysql_real_escape_string( $formid );
        $preChoice = DBJson::mysql_real_escape_string( $preChoice );
        $preForm = DBJson::mysql_real_escape_string( $preForm );
        $preExercise = DBJson::mysql_real_escape_string( $preExercise );
        
        // decode the received user data, as an object
        $insert = Choice::decodeChoice( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        foreach ( $insert as $in ){

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  'Sql/EditChoice.sql',
                                                  array( 
                                                        'choiceid' => $choiceid,
                                                        'object' => $in,
                                                         'preChoice' => $preChoice,
                                                         'preForm' => $preForm,
                                                         'preExercise' => $preExercise
                                                        )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'PUT EditChoice failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->stop( );
            }
        }
    }
    
    public function deleteChoice( $preChoice='',  $preForm='',  $preExercise='', $choiceid )
    {
        $this->loadConfig($preChoice, $preForm,  $preExercise);
        $preChoice = ($preChoice === '' ? '' : '_') . $preChoice;
        $preForm = ($preForm === '' ? '' : '_') . $preForm;
        $preExercise = ($preExercise === '' ? '' : '_') . $preExercise;
        
        Logger::Log( 
                    'starts DELETE DeleteForm',
                    LogLevel::DEBUG
                    );

        $choiceid = DBJson::mysql_real_escape_string( $choiceid );
        $formid = DBJson::mysql_real_escape_string( $formid );
        $preChoice = DBJson::mysql_real_escape_string( $preChoice );
        $preForm = DBJson::mysql_real_escape_string( $preForm );
        $preExercise = DBJson::mysql_real_escape_string( $preExercise );
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteChoice.sql',
                                              array( 'choiceid' => $choiceid,
                                                     'preChoice' => $preChoice,
                                                     'preForm' => $preForm,
                                                     'preExercise' => $preExercise )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){

            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );

            $this->_app->response->setStatus( 201 );
            $this->_app->stop( );
            
        } else {
            Logger::Log( 
                        'DELETE DeleteChoice failed',
                        LogLevel::ERROR
                        );

            $this->_app->response->setBody( Choice::encodeChoice( new Choice( ) ) );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }
    
    public function addChoice( $preChoice='',  $preForm='',  $preExercise='' )
    {
        $this->loadConfig($preChoice, $preForm,  $preExercise);
        $preChoice = ($preChoice === '' ? '' : '_') . $preChoice;
        $preForm = ($preForm === '' ? '' : '_') . $preForm;
        $preExercise = ($preExercise === '' ? '' : '_') . $preExercise;
        
        Logger::Log( 
                    'starts POST AddChoice',
                    LogLevel::DEBUG
                    );
                    
        $formid = DBJson::mysql_real_escape_string( $formid );
        $preChoice = DBJson::mysql_real_escape_string( $preChoice );
        $preForm = DBJson::mysql_real_escape_string( $preForm );
        $preExercise = DBJson::mysql_real_escape_string( $preExercise );

        // decode the received choice data, as an object
        $insert = Choice::decodeChoice( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  'Sql/AddChoice.sql',
                                                  array( 'object' => $in,
                                                         'preChoice' => $preChoice,
                                                         'preForm' => $preForm,
                                                         'preExercise' => $preExercise)
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Choice( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);

                $obj->setChoiceId( $course['id'] . '_' . $queryResult[count($queryResult)-2]->getInsertId( ) );
                
                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddChoice failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Choice::encodeChoice( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Choice::encodeChoice( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Choice::encodeChoice( $res ) );
    }
    
    public function get( 
                        $functionName,
                        $sqlFile,
                        $preChoice,
                        $preForm,
                        $preExercise,
                        $formid,
                        $choiceid,
                        $courseid,
                        $esid,
                        $eid,
                        $singleResult = false,
                        $checkSession = true
                        )
    {
        $this->loadConfig($preChoice, $preForm,  $preExercise);
        $preChoice = ($preChoice === '' ? '' : '_') . $preChoice;
        $preForm = ($preForm === '' ? '' : '_') . $preForm;
        $preExercise = ($preExercise === '' ? '' : '_') . $preExercise;
        
        Logger::Log( 
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        // checks whether incoming data has the correct data type
        $choiceid = DBJson::mysql_real_escape_string( $choiceid );
        $formid = DBJson::mysql_real_escape_string( $formid );
        $preChoice = DBJson::mysql_real_escape_string( $preChoice );
        $preForm = DBJson::mysql_real_escape_string( $preForm );
        $preExercise = DBJson::mysql_real_escape_string( $preExercise );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              $sqlFile,
                                              array( 
                                                    'formid' => $formid,
                                                    'choiceid' => $choiceid,
                                                    'courseid' => $courseid,
                                                    'esid' => $esid,
                                                    'eid' => $eid,
                                                     'preChoice' => $preChoice,
                                                     'preForm' => $preForm,
                                                     'preExercise' => $preExercise
                                                    ),
                                              $checkSession
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if (is_array($query))
            $query = $query[count($query)-1];
            
            if ( $query->getNumRows( ) > 0 ){
                $res = Choice::ExtractChoice( 
                                         $query->getResponse( ),
                                         $singleResult
                                         );
                                         
                $this->_app->response->setBody( Choice::encodeChoice( $res ) );

                $this->_app->response->setStatus( 200 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

                $this->_app->stop( );
                
            } else 
                $result['status'] = 404;
        }

        Logger::Log( 
                    'GET ' . $functionName . ' failed',
                    LogLevel::ERROR
                    );
        $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
        $this->_app->response->setBody( Choice::encodeChoice( new Choice( ) ) );
        $this->_app->stop( );
    }
    
    public function getChoice( $preChoice='',  $preForm='',  $preExercise='', $choiceid )
    {
        $this->get( 
                   'GetChoice',
                   'Sql/GetChoice.sql',
                   $preChoice,
                   $preForm,
                   $preExercise,
                   isset( $formid ) ? $formid : '',
                   isset( $choiceid ) ? $choiceid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   true
                   );
    }
    
    public function getCourseChoices( $preChoice='',  $preForm='',  $preExercise='', $courseid )
    {
        $this->get( 
                   'GetCourseChoices',
                   'Sql/GetCourseChoices.sql',
                   $preChoice,
                   $preForm,
                   $preExercise,
                   isset( $formid ) ? $formid : '',
                   isset( $choiceid ) ? $choiceid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   false
                   );
    }
    
    public function getExistsCourseChoices( $preChoice='',  $preForm='',  $preExercise='', $courseid )
    {
        $this->get( 
                   'GetExistsCourseChoices',
                   'Sql/GetExistsCourseChoices.sql',
                   $preChoice,
                   $preForm,
                   $preExercise,
                   isset( $formid ) ? $formid : '',
                   isset( $choiceid ) ? $choiceid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   true
                   );
    }
    
    public function getSheetChoices( $preChoice='',  $preForm='',  $preExercise='', $esid )
    {
        $this->get( 
                   'GetSheetChoices',
                   'Sql/GetSheetChoices.sql',
                   $preChoice,
                   $preForm,
                   $preExercise,
                   isset( $formid ) ? $formid : '',
                   isset( $choiceid ) ? $choiceid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   false
                   );
    }
    
    public function getExerciseChoices( $preChoice='',  $preForm='',  $preExercise='', $eid )
    {
        $this->get( 
                   'GetExerciseChoices',
                   'Sql/GetExerciseChoices.sql',
                   $preChoice,
                   $preForm,
                   $preExercise,
                   isset( $formid ) ? $formid : '',
                   isset( $choiceid ) ? $choiceid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   false
                   );
    }
    
    public function getFormChoices( $preChoice='',  $preForm='',  $preExercise='', $formid )
    {
        $this->get( 
                   'GetFormChoices',
                   'Sql/GetFormChoices.sql',
                   $preChoice,
                   $preForm,
                   $preExercise,
                   isset( $formid ) ? $formid : '',
                   isset( $choiceid ) ? $choiceid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   false
                   );
    }
    
    public function deleteCourse( $preChoice='',  $preForm='',  $preExercise='', $courseid )
    {
        $this->loadConfig($preChoice, $preForm,  $preExercise);
        $preChoice = ($preChoice === '' ? '' : '_') . $preChoice;
        $preForm = ($preForm === '' ? '' : '_') . $preForm;
        $preExercise = ($preExercise === '' ? '' : '_') . $preExercise;
        
        Logger::Log( 
                    'starts DELETE DeleteCourse',
                    LogLevel::DEBUG
                    );
                    
        $courseid = DBJson::mysql_real_escape_string( $courseid ); 
        $formid = DBJson::mysql_real_escape_string( $formid );
        $preChoice = DBJson::mysql_real_escape_string( $preChoice );
        $preForm = DBJson::mysql_real_escape_string( $preForm );
        $preExercise = DBJson::mysql_real_escape_string( $preExercise );
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              'Sql/DeleteCourse.sql',
                                              array( 'courseid' => $courseid,
                                                     'preChoice' => $preChoice,
                                                     'preForm' => $preForm,
                                                     'preExercise' => $preExercise )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){

            $this->_app->response->setStatus( 201 );
            $this->_app->response->setBody( '' );
            if ( isset( $result['headers']['Content-Type'] ) )
                $this->_app->response->headers->set( 
                                                    'Content-Type',
                                                    $result['headers']['Content-Type']
                                                    );
            
        } else {
            Logger::Log( 
                        'DELETE DeleteCourse failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->response->setBody( '' );
            $this->_app->stop( );
        }
    }
    
    public function addCourse( $preChoice='',  $preForm='',  $preExercise='' )
    {
        $this->loadConfig($preChoice, $preForm,  $preExercise);
        $preChoice = ($preChoice === '' ? '' : '_') . $preChoice;
        $preForm = ($preForm === '' ? '' : '_') . $preForm;
        $preExercise = ($preExercise === '' ? '' : '_') . $preExercise;
        
        Logger::Log( 
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Course::decodeCourse( $this->_app->request->getBody( ) );
        $formid = DBJson::mysql_real_escape_string( $formid );
        $preChoice = DBJson::mysql_real_escape_string( $preChoice );
        $preForm = DBJson::mysql_real_escape_string( $preForm );
        $preExercise = DBJson::mysql_real_escape_string( $preExercise );
        
        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){
        
            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  'Sql/AddCourse.sql',
                                                  array( 'object' => $in,
                                                         'preChoice' => $preChoice,
                                                         'preForm' => $preForm,
                                                         'preExercise' => $preExercise )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                $res[] = $in;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST AddCourse failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Course::encodeCourse( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Course::encodeCourse( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Course::encodeCourse( $res ) );
    }
}
?>

