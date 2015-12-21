<?php


/**
 * @file DBTransaction.php contains the DBTransaction class
 *
 * @author Till Uhlig
 * @date 2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures/Transaction.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures/Course.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures/Query.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBRequest.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "Transaction" table from database
 */
class DBTransaction
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
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct( )
    {
        // runs the CConfig
        $com = new CConfig( 'transaction,course,link', dirname(__FILE__) );

        // runs the DBTransaction
        if ( $com->used( ) ) return;

        $this->_conf = $com;

        // initialize slim
        $this->_app = new \Slim\Slim(array('debug' => true));
        $this->_app->response->headers->set(
                                            'Content-Type',
                                            'application/json'
                                            );

        // POST AddCourse
        $this->_app->post(
                         '(/:name)/course',
                         array(
                               $this,
                               'addCourse'
                               )
                         );

        // POST DeleteCourse
        $this->_app->delete(
                         '(/:name)/course/:courseid',
                         array(
                               $this,
                               'deleteCourse'
                               )
                         );

       // GET GetExistsCourseTransactions
        $this->_app->get(
                         '(/:name)/link/exists/course/:courseid',
                         array(
                               $this,
                               'getExistsCourseTransactions'
                               )
                        );

        // DELETE DeleteTransaction
        $this->_app->delete(
                            '(/:name)/transaction/authentication/:auid/transaction/:tid',
                            array(
                                  $this,
                                  'deleteTransaction'
                                  )
                            );

        // DELETE DeleteTransactionShort
        $this->_app->delete(
                            '(/:name)/transaction/transaction/:tid',
                            array(
                                  $this,
                                  'deleteTransactionShort'
                                  )
                            );

        // POST AddTransaction
        $this->_app->post(
                          '(/:name)/transaction/course/:courseid',
                          array(
                                $this,
                                'addTransaction'
                                )
                          );

        // POST AddSheetTransaction
        $this->_app->post(
                          '(/:name)/transaction/exercisesheet/:esid',
                          array(
                                $this,
                                'addSheetTransaction'
                                )
                          );

        // POST AddExerciseTransaction
        $this->_app->post(
                          '(/:name)/transaction/exercise/:eid',
                          array(
                                $this,
                                'addExerciseTransaction'
                                )
                          );

        // GET GetTransaction
        $this->_app->get(
                         '(/:name)/transaction/authentication/:auid/transaction/:tid',
                         array(
                               $this,
                               'getTransaction'
                               )
                         );

        // GET GetAmountOfExpiredTransactions
        $this->_app->get(
                         '(/:name)/clean/clean/course/:courseid',
                         array(
                               $this,
                               'getAmountOfExpiredTransactions'
                               )
                         );

        // DELETE CleanTransactions
        $this->_app->delete(
                         '(/:name)/clean/clean/course/:courseid',
                         array(
                               $this,
                               'cleanTransactions'
                               )
                         );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Loads the configuration data for the component from CConfig.json file
     *
     * @param int $name A optional name for the attachment table.
     *
     * @return an component object, which represents the configuration
     */
    public function loadConfig( $name='' ){
        // initialize component
        $this->_conf = $this->_conf->loadConfig( $name );
        $this->query = array( CConfig::getLink(
                                               $this->_conf->getLinks( ),
                                               'out'
                                               ) );
    }

    /**
     * Deletes a transaction.
     *
     * Called when this component receives an HTTP DELETE request to
     * (/:name)/authentication/:auid/transaction/:tid.
     *
     * @param string $tid The id of the transaction that is being deleted.
     * @param string $auid A text to verify the call .
     * @param int $name A optional name for the transaction table.
     */
    public function deleteTransaction( $name='' ,$auid, $tid )
    {
        $this->loadConfig($name);
        $name = ($name === '' ? '' : '_') . $name;

        Logger::Log(
                    'starts DELETE DeleteTransaction',
                    LogLevel::DEBUG
                    );

        $tid = DBJson::mysql_real_escape_string( $tid );
        $name = DBJson::mysql_real_escape_string( $name );
        $auid = DBJson::mysql_real_escape_string( $auid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteTransaction.sql',
                                              array( 'auid' => $auid,'name' => $name,'tid' => $tid )
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
                        'DELETE DeleteTransaction failed',
                        LogLevel::ERROR
                        );

            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->stop( );
        }
    }

    public function deleteTransactionShort( $name='', $tid )
    {
        $this->deleteTransaction( $name ,null, $tid );
    }

    /**
     * Adds a transaction.
     *
     * Called when this component receives an HTTP POST request to
     * /transaction(/).
     * The request body should contain a JSON object representing the
     * transaction's attributes.
     *
     * @param string $name A optional name for the transaction table.
     */
    public function addTransaction( $name='', $courseid )
    {
        $this->add($name, $courseid, 'courseid', 'POST AddTransaction', dirname(__FILE__) . '/Sql/AddTransaction.sql');
    }

    public function addSheetTransaction( $name='', $esid )
    {
        $this->add($name, $esid, 'esid', 'POST AddShetTransaction', dirname(__FILE__) . '/Sql/AddSheetTransaction.sql');
    }

   public function addExerciseTransaction( $name='', $eid )
    {
        $this->add($name, $eid, 'eid', 'POST AddExerciseTransaction', dirname(__FILE__) . '/Sql/AddExerciseTransaction.sql');
    }

    public function add( $name='', $id, $idName ,$functionName, $sqlFile)
    {
        $this->loadConfig($name);
        $name = ($name === '' ? '' : '_') . $name;

        Logger::Log(
                    'starts '.$functionName,
                    LogLevel::DEBUG
                    );

        // decode the received attachment data, as an object
        $insert = Transaction::decodeTransaction( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        $name = DBJson::mysql_real_escape_string( $name );
        $id = DBJson::mysql_real_escape_string( $id );

        $uuid = new uuid();
        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){

            $random = str_replace('-','',$uuid->get());
            $in->setTransactionId(null);

            // generates the insert data for the object
            $data = $in->getInsertData( );

            // starts a query, by using a given file
            $result = DBRequest::getRoutedSqlFile(
                                                  $this->query,
                                                  $sqlFile,
                                                  array( 'object' => $in,'name' => $name, $idName => $id, 'random' => $random )
                                                  );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $queryResult = Query::decodeQuery( $result['content'] );

                // sets the new auto-increment id
                $obj = new Transaction( );
                $course = Course::ExtractCourse($queryResult[count($queryResult)-1]->getResponse(),true);

                $obj->setTransactionId( $course->getId() . '_' . $queryResult[count($queryResult)-2]->getInsertId( ) . '_' . $random );


                $res[] = $obj;
                $this->_app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->_app->response->headers->set(
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );

            } else {
                Logger::Log(
                            $functionName.' failed',
                            LogLevel::ERROR
                            );
                $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->_app->response->setBody( Transaction::encodeTransaction( $res ) );
                $this->_app->stop( );
            }
        }

        if ( !$arr &&
             count( $res ) == 1 ){
            $this->_app->response->setBody( Transaction::encodeTransaction( $res[0] ) );

        } else
            $this->_app->response->setBody( Transaction::encodeTransaction( $res ) );
    }

    public function get(
                        $functionName,
                        $sqlFile,
                        $name='' ,
                        $userid,
                        $courseid,
                        $esid,
                        $eid,
                        $auid,
                        $tid,
                        $singleResult = false
                        )
    {
        $this->loadConfig($name);
        $name = ($name === '' ? '' : '_') . $name;

        Logger::Log(
                    'starts GET ' . $functionName,
                    LogLevel::DEBUG
                    );

        $name = DBJson::mysql_real_escape_string( $name );
        $userid = DBJson::mysql_real_escape_string( $userid );
        $courseid = DBJson::mysql_real_escape_string( $courseid );
        $esid = DBJson::mysql_real_escape_string( $esid );
        $eid = DBJson::mysql_real_escape_string( $eid );
        $auid = DBJson::mysql_real_escape_string( $auid );
        $tid = DBJson::mysql_real_escape_string( $tid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              $sqlFile,
                                              array(
                                                    'name' => $name,
                                                    'userid' => $userid,
                                                    'courseid' => $courseid,
                                                    'esid' => $esid,
                                                    'eid' => $eid,
                                                    'auid' => $auid,
                                                    'tid' => $tid
                                                    )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){
            $query = Query::decodeQuery( $result['content'] );

            if (is_array($query))
            $query = $query[count($query)-1];

            if ( $query->getNumRows( ) > 0 ){
                $res = Transaction::ExtractTransaction(
                                                     $query->getResponse( ),
                                                     $singleResult
                                                     );

                $this->_app->response->setBody( Transaction::encodeTransaction( $res ) );

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
        $this->_app->response->setBody( Transaction::encodeTransaction( new Transaction( ) ) );
        $this->_app->stop( );
    }

    /**
     * Returns a transaction.
     *
     * Called when this component receives an HTTP GET request to
     * (/:name)/authentication/:auid/transaction/:tid.
     *
     * @param string $tid The id of the transaction that is being deleted.
     * @param string $auid A text to verify the call .
     * @param int $name A optional name for the transaction table.
     */
    public function getTransaction( $name='' ,$auid, $tid )
    {
        $this->get(
                   'GetTransaction',
                   dirname(__FILE__) . '/Sql/procedures/GetTransaction.sql',
                   isset( $name ) ? $name : '',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $auid ) ? $auid : '',
                   isset( $tid ) ? $tid : '',
                   true
                   );
    }

    /**
     * Returns status code 200, if this component is correctly installed for the given course
     *
     * Called when this component receives an HTTP GET request to
     * (/$name)/link/exists/course/$courseid.
     *
     * @param string $courseid The id of the course.
     * @param int $name A optional name for the transaction table.
     */
    public function getExistsCourseTransactions( $name='' , $courseid )
    {
        $this->get(
                   'GetExistsCourseTransactions',
                   dirname(__FILE__) . '/Sql/procedures/GetExistsCourseTransactions.sql',
                   isset( $pre ) ? $pre : '',
                   isset( $userid ) ? $userid : '',
                   isset( $courseid ) ? $courseid : '',
                   isset( $esid ) ? $esid : '',
                   isset( $eid ) ? $eid : '',
                   isset( $auid ) ? $auid : '',
                   isset( $tid ) ? $tid : '',
                   true
                   );
    }

    /**
     * Removes the component from a given course
     *
     * Called when this component receives an HTTP DELETE request to
     * (/$name)/course/$courseid.
     *
     * @param string $courseid The id of the course.
     * @param string $name A optional name for the transaction table.
     */
    public function deleteCourse( $name='' , $courseid )
    {
        $this->loadConfig($name);
        $name = ($name === '' ? '' : '_') . $name;

        Logger::Log(
                    'starts DELETE DeleteCourse',
                    LogLevel::DEBUG
                    );

        $courseid = DBJson::mysql_real_escape_string( $courseid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/DeleteCourse.sql',
                                              array( 'courseid' => $courseid,'name' => $name )
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

    public function cleanTransactions( $name='' , $courseid )
    {
        $this->loadConfig($name);
        $name = ($name === '' ? '' : '_') . $name;

        Logger::Log(
                    'starts DELETE CleanTransactions',
                    LogLevel::DEBUG
                    );
        $courseid = DBJson::mysql_real_escape_string( $courseid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/CleanTransactions.sql',
                                              array( 'courseid' => $courseid,'name' => $name )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){

            $this->_app->response->setStatus( 201 );
            $this->_app->response->setBody( '' );

        } else {
            Logger::Log(
                        'DELETE CleanTransactions failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->response->setBody( '' );
            $this->_app->stop( );
        }
    }

    public function getAmountOfExpiredTransactions( $name='' , $courseid )
    {
        $this->loadConfig($name);
        $name = ($name === '' ? '' : '_') . $name;

        Logger::Log(
                    'starts GET GetAmountOfExpiredTransactions',
                    LogLevel::DEBUG
                    );
        $courseid = DBJson::mysql_real_escape_string( $courseid );

        // starts a query, by using a given file
        $result = DBRequest::getRoutedSqlFile(
                                              $this->query,
                                              dirname(__FILE__) . '/Sql/procedures/GetAmountOfExpiredTransactions.sql',
                                              array( 'courseid' => $courseid,'name' => $name )
                                              );

        // checks the correctness of the query
        if ( $result['status'] >= 200 &&
             $result['status'] <= 299 ){

             $query = Query::decodeQuery($result['content']);
             $result = $query->getResponse();
             foreach ($result as &$res){
                $res['component'] = $this->_conf->getName();
             }
            $this->_app->response->setStatus( 200 );
            $this->_app->response->setBody( json_encode($result) );

        } else {
            Logger::Log(
                        'GET GetAmountOfExpiredTransactions failed',
                        LogLevel::ERROR
                        );
            $this->_app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
            $this->_app->response->setBody( '' );
            $this->_app->stop( );
        }
    }

    /**
     * Adds the component to a course
     *
     * Called when this component receives an HTTP POST request to
     * (/$name)/course.
     *
     * @param string $name A optional name for the attachment table.
     */
    public function addCourse( $name='' )
    {
        $this->loadConfig($name);
        $name = ($name === '' ? '' : '_') . $name;

        Logger::Log(
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Course::decodeCourse( $this->_app->request->getBody( ) );

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
                                                  array( 'object' => $in,'name' => $name )
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

/**
 @see http://de2.php.net/manual/de/function.uniqid.php#88400
 */
class uuid {

    protected $urand;

    public function __construct() {
        $this->urand = @fopen ( '/dev/urandom', 'rb' );
    }

    /**
     * @brief Generates a Universally Unique IDentifier, version 4.
     *
     * This function generates a truly random UUID. The built in CakePHP String::uuid() function
     * is not cryptographically secure. You should uses this function instead.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.4
     * @see http://en.wikipedia.org/wiki/UUID
     * @return string A UUID, made up of 32 hex digits and 4 hyphens.
     */
    function get() {

        $pr_bits = false;
        if (is_a ( $this, 'uuid' )) {
            if (is_resource ( $this->urand )) {
                $pr_bits .= @fread ( $this->urand, 16 );
            }
        }
        if (! $pr_bits) {
            $fp = @fopen ( '/dev/urandom', 'rb' );
            if ($fp !== false) {
                $pr_bits .= @fread ( $fp, 16 );
                @fclose ( $fp );
            } else {
                // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
                $pr_bits = "";
                for($cnt = 0; $cnt < 16; $cnt ++) {
                    $pr_bits .= chr ( mt_rand ( 0, 255 ) );
                }
            }
        }
        $time_low = bin2hex ( substr ( $pr_bits, 0, 4 ) );
        $time_mid = bin2hex ( substr ( $pr_bits, 4, 2 ) );
        $time_hi_and_version = bin2hex ( substr ( $pr_bits, 6, 2 ) );
        $clock_seq_hi_and_reserved = bin2hex ( substr ( $pr_bits, 8, 2 ) );
        $node = bin2hex ( substr ( $pr_bits, 10, 6 ) );

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec ( $time_hi_and_version );
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec ( $clock_seq_hi_and_reserved );
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        return sprintf ( '%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node );
    }

}