
<?php
/**
 * @file DBBeispiel.php
 *
 * @author Till Uhlig
 * @date 2015
 */

// bindet die Modellklasse und weitere wichtige ein (hier ist auch Slim enthalten)
include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

class DBOOP
{

    private $_component = null;

    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */
    private static $_prefix = 'course';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBOOP::$_prefix;
    }

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function setPrefix( $value )
    {
        DBOOP::$_prefix = $value;
    }


    public function __construct( )
    {
        // erzeugt das Model Objekt, welches einiges erleichtern soll
        /*$component = new Model('testcase', dirname(__FILE__), $this);
        $this->_component=$component;*/
        
        // startet nun unsere Komponente, dabei kommt die Zuordnung von Aufruf und Funktion
        // aus der Commands.json
        //$component->run();

        /* alte Variante */

        // runs the CConfig
        $com = new CConfig( DBOOP::getPrefix(), dirname(__FILE__) );

        // runs the DBProcess
        if ( $com->used( ) ) return;
            
        // initialize component
        $this->_conf = $com;

        // initialize slim
        $this->_app = new \Slim\Slim( array('debug' => true) );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );

        // POST AddCourse
        $this->_app->post( 
                         '(/:pre)/course(/)',
                         array( 
                               $this,
                               'addCourse'
                               )
                         );

        // DELETE DeleteCourse
        $this->_app->delete( 
                         '(/:pre)/course/:courseid(/)',
                         array( 
                               $this,
                               'deleteCourse'
                               )
                         );

        // POST insert
        $this->_app->post( 
                         '(/:pre)/insert(/)',
                         array( 
                               $this,
                               'insertTestcase'
                               )
                         );

        // GET pop
        $this->_app->get( 
                         '(/:pre)/pop(/)',
                         array( 
                               $this,
                               'popTestcase'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    // ist aber nicht Aufrufbar
    public function editCourse( $callName, $input, $params = array() )
    {
        // ruft eine SQL Datei über den Ausgang 'out' auf
        return $this->_component->callSqlTemplate(
                                                  'out',    // der Name des Ausgangs, über welchen der Aufruf erfolgen soll
                                                  dirname(__FILE__).'/Sql/EditCourse.sql', // Pfad zur SQL Datei (ebenfalls Template, mit PHP kombinierbar)
                                                  array_merge($params,array('values' => $input->getInsertData( ))), // gibt diese Werte an das SQL Template
                                                  201, // eine positive Antwort der Anfrage wäre eine 201
                                                  'Model::isCreated', // dann soll unsere Antwort ebenfalls eine 201 (hier kann eigene aufzurufende Funktion angegeben werden, falls Antwort positiv ist)
                                                  array(new Course()), // bei positiver Antwort soll ein leeres Veranstaltungsobjekt zurückgegeben werden
                                                  'Model::isProblem', // wenn keine 201 zurückkommt, geben wir eine 409 zurück bzw. einen Problemcode
                                                  array(new Course()) // zur negativen Antwort noch ein leeres Veranstaltungsobjekt
                                                  );
    }

    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        // in diesem Beispiel wird der Name, welcher in der Commands.json für diesen Funktionsaufruf angegeben wurde,
        // direkt Verwendet, um einen gleichnamigen Ausgang der Component.json anzusprechen und den dort
        // angegeben Aufruf auzulösen (wobei courseid entsprechend ersetzt wird)
        
        // diese Funktion soll aufgerufen werden, wenn unsere Anfrage an die Datenbank positiv war
        $positive = function($input, $singleResult) {
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract course data from db answer
                    $result['content'] = array_merge($result['content'], Course::ExtractCourse( $inp->getResponse( ), $singleResult));
                    $result['status'] = 200;
                }
            }
            return $result;
        };
        
        // hier wird eine MySql stored-procedure aufgerufen
        // dabei haben die aufzurufen Befehle die Form /funktionsname/:idA/:idB  (stehen in der Component.json)
        // dabei werden idA und idB durch die Werte in $params ersetzt Bsp.: $params = array("idA"=>2, "idB"=>3)
        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call($linkName, // der Name der Verknüpfungen welche aufgerufen werden soll
                                       $params, // übergibt eventuelle Parameter an das Aufruftemplate
                                       '', // einen Aufrufinhalt (content)
                                       200, // erwartete positive Antwort
                                       $positive, // falls positive Antwort, rufe diese Funktion auf
                                       array($singleResult), // übergib zusätzlich an $positive diese Parameter
                                       'Model::isProblem', // bei negativer Antwort eine Fehlernummer
                                       array(), // und bei negativer Antwort an die "Negativfunktion" diese Parameter übergeben
                                       'Query' // der Rückgabetyp der Anfrage, unsere Funktionen erhalten diesen Typ als $input (muss daher eine /Assistants/Structures sein)
                                       );
    }
    
    public function getDaten($callName, $input, $params = array())
    {
        return Model::isProblem();
    }

    public function getMatch($callName, $input, $params = array())
    {
        // soll GET Anfragen behandeln, welche nur ein einzelnes Ergebnis erwarten
        // (Bsp.: getUser oder getCourse als Einzelobjekt)
        return $this->get($callName,$callName,$params);
    }
    public function getMatchSingle($callName, $input, $params = array())
    {
        // soll GET Anfragen behandeln, welche eine mehrzahl von Ergebnissen erwarten
        // (Bsp.: getAllUsers als Array)
        return $this->get($callName,$callName,$params,true,false);
    }

    /**
     * Loads the configuration data for the component from CConfig.json file
     *
     * @param int $pre A optional prefix for the process table.
     *
     * @return an component object, which represents the configuration
     */
    public function loadConfig( $pre='' )
    {
        // initialize component
        $this->_conf = $this->_conf->loadConfig( $pre );
        $this->query = array( CConfig::getLink( 
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );
    }

    /**
     * Adds a Testcase to the 
     *
     * Called when this component receives an HTTP POST request to
     * (/:pre)/course/:courseid(/).
     *
     * @param int $pre A optional prefix for the Exercise table.
     */
    public function insertTestcase( $pre='' )
    {
        //file_put_contents('php://stderr', print_r("------TTTTTTEEEEEEESSSSSSTTTTTT---------", TRUE));
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts POST insertTestcase',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Testcase::decodeTestcase( $this->_app->request->getBody( ) );

        $pre = DBJson::mysql_real_escape_string( $pre );
        
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

            //cast process
            $in->setProcess(Process::decodeProcess($in->getProcess(),false));

            //set submission

            if ($in->getSubmission() === null){
                if ($in->getProcess()->getSubmission()->getId() !== null){
                    $mysubmission = clone $in->getProcess()->getSubmission();
                    $in->getProcess()->setSubmission(null);
                    $in->setSubmission($mysubmission);
                } elseif ($in->getProcess()->getRawSubmission()->getId() !== null){
                    $mysubmission = clone $in->getProcess()->getRawSubmission();
                    $in->getProcess()->setSubmission(null);
                    $in->setSubmission($mysubmission);
                } else {
                    //Testcases können nicht ohne Submission erstellt werden, eine Fehler-Ausgabe ist nicht nötig 
                    continue;
                } 
            }
            //file_put_contents('php://stderr', print_r($in, TRUE));
            
            $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/AddTestcase.sql',
                                                  array( 'object' => $in,
                                                         'pre' => $pre)
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
                $this->_app->response->setBody( Testcase::encodeTestcase( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Testcase::encodeTestcase( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Testcase::encodeTestcase( $res ) );
    }

    /**
     * Adds a Testcase to the 
     *
     * Called when this component receives an HTTP POST request to
     * (/:pre)/course/:courseid(/).
     *
     * @param int $pre A optional prefix for the Exercise table.
     */
    public function popTestcase( $pre='' )
    {

        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts GET popTestcase',
                    LogLevel::DEBUG
                    );

        $pre = DBJson::mysql_real_escape_string( $pre );
        
        $result = DBRequest::getRoutedSqlFile( 
                                                  $this->query,
                                                  dirname(__FILE__) . '/Sql/CallPopTestcase.sql',
                                                  array()
                                                  );
        
        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){

            $query = Query::decodeQuery( $result['content'] );
            
            if (is_array($query)){
                $query = $query[0];
            }

            if ( $query->getNumRows( ) !== null && !empty($query->getNumRows( )) && $query->getNumRows( ) > 0 ){
                
                $res = Testcase::ExtractTestcase( 
                                         $query->getResponse( ),
                                         true
                                         );

                $this->_app->response->setBody( Testcase::encodeTestcase( $res ) );

                $this->_app->response->setStatus( 200 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

                $this->_app->stop( );
            } else {
                $this->_app->response->setStatus( 404 );
            }
        }

    }

    /**
     * Adds the component to a course
     *
     * Called when this component receives an HTTP POST request to
     * (/:pre)/course/:courseid(/).
     *
     * @param int $pre A optional prefix for the Exercise table.
     */
    public function addCourse( $pre='' )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Course::decodeCourse( $this->_app->request->getBody( ) );
        $pre = DBJson::mysql_real_escape_string( $pre );
        
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
                                                  dirname(__FILE__) . '/Sql/AddCourse.sql',
                                                  array( 'object' => $in,
                                                         'pre' => $pre)
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

    /**
     * Removes the component from a given course
     *
     * Called when this component receives an HTTP DELETE request to
     * (/$pre)/course/$courseid(/).
     *
     * @param string $courseid The id of the course.
     * @param int $pre A optional prefix for the attachment table.
     */
    public function deleteCourse( $pre='' , $courseid )
    {
        $this->loadConfig($pre);
        $pre = ($pre === '' ? '' : '_') . $pre;
        
        Logger::Log( 
                    'starts DELETE DeleteCourse',
                    LogLevel::DEBUG
                    );
                    
        $courseid = DBJson::mysql_real_escape_string( $courseid ); 
        
        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile( 
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteCourse.sql',
                                              array( 'courseid' => $courseid ,'pre' => $pre )
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
}
